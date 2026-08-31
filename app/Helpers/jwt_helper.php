<?php
// app/Helpers/jwt_helper.php

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\Jwt as JwtConfig;

if (!function_exists('createJWT')) {
    /**
     * Create a JWT token.
     *
     * @param array $data Payload data (e.g., user information)
     * @return string JWT token
     */
    function createJWT(array $data): array
    {
        $config = new JwtConfig();
        $issuedAt = time();

        $expire = $issuedAt + (int)$config->expireTime;

        $payload = [
            'iat'  => $issuedAt,
            'exp'  => $expire,
            'data' => $data
        ];

        return [
            'token' => JWT::encode($payload, $config->secretKey, $config->algorithm),
            'expire' => $expire,
            'issuedAt' => $issuedAt
        ];
    }
}

function createRefreshJWT(array $data, bool $rememberMe = false): array
{
    $config = new JwtConfig();
    $issuedAt = time();

    $expire = $issuedAt + (int)$config->refreshExpireTime;
    if ($rememberMe) {
        $expire = $issuedAt + (int)$config->longExpireTime;
    }

    $payload = [
        'iat'  => $issuedAt,
        'exp'  => $expire,
        'data' => $data
    ];

    return [
        'token' => JWT::encode($payload, $config->secretKey, $config->algorithm),
        'expire' => $expire,
        'issuedAt' => $issuedAt
    ];
}

if (!function_exists('verifyJWT')) {
    /**
     * Verify and decode a JWT token.
     *
     * @param string $token JWT token
     * @return object|false Decoded token object or false on failure
     */
    function verifyJWT(string $token)
    {
        $config = new JwtConfig();

        try {
            $decoded = JWT::decode($token, new Key($config->secretKey, $config->algorithm));
            return $decoded;
        } catch (\Exception $e) {
            // Log the exception message if needed
            return false;
        }
    }
}
