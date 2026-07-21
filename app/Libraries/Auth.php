<?php

namespace App\Libraries;

use Config\Services;
use App\Models\UserModel;

class Auth
{
    private static ?object $user = null; // Cached authenticated user
    public static ?bool $totpRequired = true; // Cached JWT data

    /**
     * Check if user is authenticated
     * @return bool
     */
    public static function check(): bool
    {
        return self::user() !== null;
    }

    /**
     * Get authenticated user data
     * @return object|null User data or null if not authenticated
     */
    public static function user(): ?object
    {

        // Return cached user if already retrieved
        if (self::$user !== null) {
            return self::$user;
        }

        // Auto-detect and get token from the correct source
        $token = self::getToken();

        if (!$token) {
            return null; // No token found
        }

        $decoded = verifyJWT($token);
        if (!$decoded) {
            //verify against refresh token here if main token is expired, if refresh token is also expired then return false
            //pending for future if required.
            return null;
        }

        //verify TOTP
        if (isset($decoded->data->totpVerified) and !$decoded->data->totpVerified and self::$totpRequired) {
            return null;
        }



        $db = db_connect();
        $user = $db->table('userMaster')->where('userId', $decoded->data->userId)->get()->getRow();
        $user->jwtData = $decoded->data;

        // get user group
        $userGroup = $db->table('userGroups')->where('groupId', $user->groupId)->get()->getRow();
        $user->group = $userGroup;

        // Cache the user for the current request
        self::$user = $user ?: null;

        return self::$user;
    }

    // In Auth Library
    public static function verifySingleSignOn()
    {
        $user = self::user(); // Already stores user info from JWT

        if (!$user) false;

        //get user data
        $config = config('AppConfig');
        //disabled to avoid circuler dependency issue. auth -> appConfig -> tenant -> auth
        if ($config->singleSignOn) {
            if ($user->singleSignonToken != $user->jwtData->singleSignonToken) {
                // debug($user->singleSignonToken);
                // debug($user->jwtData->singleSignonToken);
                // die();

                return false;
            } else {
                // die('Single Sign On Verified');
            }
        }
        return true;
    }


    /**
     * Get authenticated user ID
     * @return int|null
     */
    public static function userId(): ?int
    {
        return self::user()->userId ?? null;
    }

    /**
     * Logout user (clear cache and session/cookie)
     */
    public static function logout(): void
    {
        $user = self::user();
        if ($user) {
            $userId = $user->userId;
            $refreshToken = get_cookie('refreshToken');

            if (!$refreshToken) {
                // If no refresh token cookie, just clear user cache
                self::$user = null;
                return;
            }

            $decoded = verifyJWT($refreshToken);
            if (!$decoded) {
                return;
            }

            $jti = $decoded->data->jti;

            $db = db_connect();
            // Delete refresh token from database
            $db->table('refreshTokens')->where('userId', $userId)->where('jti', $jti)->delete();

            // Optionally, delete the refresh token cookie
            helper('cookie');
            delete_cookie('refreshToken');
            delete_cookie('jwt'); // Clear JWT cookie if used

            // Clear cached user data
            self::$user = null;
        }
    }

    /**
     * Auto-detect request type and get token
     * @return string|null
     */
    private static function getToken(): ?string
    {
        $request = Services::request();
        helper('cookie'); // Load cookie helper

        // 1️⃣ Check for Authorization Header (Backend/API Requests)
        $headerToken = $request->getHeaderLine('Authorization');
        if ($headerToken && preg_match('/Bearer\s(\S+)/', $headerToken, $matches)) {
            return $matches[1]; // Return token from Authorization header
        }

        // 2️⃣ Check for Cookie (Frontend Requests)
        $cookieToken = get_cookie('jwt');
        if ($cookieToken) {
            return $cookieToken; // Return token from frontend cookie
        }

        // // 3️⃣ Check for Session (If Used)
        // $sessionToken = session('jwt');
        // if ($sessionToken) {
        //     return $sessionToken; // Return token from session
        // }

        return null; // No token found
    }
}
