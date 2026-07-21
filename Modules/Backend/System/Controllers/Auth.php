<?php

namespace Modules\Backend\System\Controllers;

use App\Controllers\ApiBaseController;
use Modules\Backend\System\Models\UserModel;
use Modules\Backend\System\Models\tenantMasterModel;
use CodeIgniter\API\ResponseTrait;
use OTPHP\TOTP;
use App\Libraries\Auth as myAuth;

/**
 * @OA\Info(title="API Docs", version="1.0")
 *
 * @OA\SecurityScheme(
 *    securityScheme="bearerAuth",
 *    in="header",
 *    name="bearerAuth",
 *    type="http",
 *    scheme="bearer",
 *    bearerFormat="JWT"
 * )
 */
class Auth extends ApiBaseController
{
    use ResponseTrait;

    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Login User",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *          description="Username And Password",
     *          required=true,
     *          @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="username", type="string", description="Username of the user"),
     *             @OA\Property(property="password", type="string", description="Password for the user"),
     *             @OA\Property(property="rememberMe", type="boolean", description="Ture To get long lived token"),
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User authenticated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *              
     *         )
     *     )
     * )
     */
    public function login()
    {
        $input = $this->getInputData();
        $input = $input['jsonInput'];

        $db = db_connect();

        $config = config('AppConfig');
        if (isset($input['apiKey']) and $input['apiKey'] !== getenv('apiKey')) {
            return $this->fail('Invalid API key', 401);
        } else if ($config->simpleCaptcha and !isset($input['apiKey'])) {
            $captchaKey = $this->request->getHeaderLine('X-Captcha-Key');

            if (!password_verify($input['captcha'], $captchaKey)) {
                return $this->fail('Invalid captcha', 401);
            }
        }

        if (!$input) {
            return $this->fail('Invalid JSON input', 400);
        }

        // Validate input
        $validation = \Config\Services::validation();

        $validation->setRules([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (!$validation->run($input)) {
            return $this->fail($validation->getErrors(), 400);
        }

        // Find user by username
        $user = $this->userModel->where('username', $input['username'])->first();

        if (!$user) {
            $user = $this->userModel->where('email', $input['username'])->first();
            if (!$user) {
                sleep(3);
                return $this->fail('Invalid username or password', 401);
            }
        }

        // check for isActive status
        if (!$user->isActive) {
            sleep(3);
            return $this->fail('User is not active', 401);
        }

        //check for user locked out time.
        if (!is_null($user->lockoutUntil) and strtotime($user->lockoutUntil) > time()) {
            sleep(3);
            return $this->fail('Locked Out Until ' . humanTimeDifference(time(), strtotime($user->lockoutUntil)) . ' due to many failed attempts', 401);
        }

        // Verify password
        if (!password_verify($input['password'], $user->password)) {

            // $failedAttempts = $user->failedAttempts + 1;
            // if ($failedAttempts > $config->maxLoginAttempts) {
            //     $lockoutUntil = date("Y-m-d H:i:s", time() + ($config->lockoutTime * 60));

            //     $this->userModel->update($user->userId, [
            //         "failedAttempts" => $failedAttempts,
            //         "lockoutUntil" => $lockoutUntil
            //     ]);

            //     return $this->fail('Locked Out Until ' . $config->lockoutTime . ' minutes due to many failed attempts', 401);
            // } else {
            //     $this->userModel->update($user->userId, [
            //         "failedAttempts" => $failedAttempts
            //     ]);
            // }
            sleep(3);
            return $this->fail('Invalid username or password', 401);
        }


        // check for tenantId and if tenant is active
        if ($user->tenantId > 0) {

            $TenantMasterModel = new TenantMasterModel();

            $tenant = $TenantMasterModel->where('tenantId', $user->tenantId)->first();
            if (!$tenant) {
                return $this->fail('Application account not found', 404);
            }

            if (!$tenant->isActive) {
                return $this->fail('Application account is not active', 401);
            }
        }

        //check for deviceToken if trusted device is enabled
        $userGroup = $db->query("SELECT * FROM `userGroups` WHERE groupId = " . $user->groupId . "")->getRow();
        $deviceToken = $input['deviceToken'] ?? null;
        if ($userGroup->isAdmin or !$config->limitLoginToTrustedDevices) {
            $deviceToken = null;
        }


        if ($config->limitLoginToTrustedDevices && !$userGroup->isAdmin) {

            if (!$deviceToken) {
                return $this->fail('Login denied from untrested device', 400);
            }

            $lastUsedSince = $config->lastUsedSince ?? 30; // default to 30 days

            $device = $db->table("trustedDevices")->where('userId', $user->userId)
                ->where('deviceToken', $deviceToken)
                ->where('isApproved', 1)
                ->where('expiresAt >', date("Y-m-d H:i:s"))
                ->where('lastUsedAt >', date("Y-m-d H:i:s", time() - (60 * 60 * 24 * $lastUsedSince)))
                ->where('tenantId', $user->tenantId)
                ->get()
                ->getRow();

            if (!$device) {

                //check for pending approval
                $pendingDevice = $db->table("trustedDevices")->where('userId', $user->userId)
                    ->where('deviceToken', $deviceToken)
                    ->where('isApproved', 0)
                    ->where('tenantId', $user->tenantId)
                    ->get()
                    ->getRow();

                if ($pendingDevice) {
                    // Device is pending approval
                    $sr = $pendingDevice->serialNo;

                    $deviceData = [
                        'ipAddress' => $this->request->getIPAddress(),
                        'updatedAt' => date("Y-m-d H:i:s"),
                    ];

                    $db->table("trustedDevices")->where('deviceId', $pendingDevice->deviceId)
                        ->update($deviceData);
                } else {
                    // add device to trusted devices table for admin's approval
                    $deviceData = [
                        'tenantId' => $user->tenantId,
                        'userId' => $user->userId,
                        'deviceToken' => $deviceToken,
                        'userAgent' => $this->request->getUserAgent()->getAgentString(),
                        'ipAddress' => $this->request->getIPAddress(),
                        'isApproved' => 0,
                        'createdAt' => date("Y-m-d H:i:s"),
                        'updatedAt' => date("Y-m-d H:i:s"),
                    ];

                    $db->table("trustedDevices")->insert($deviceData);
                    $sr = assignSerialNumber($user->tenantId, "trustedDevices", "deviceId", $db->insertID());
                }
                return $this->fail('New device detected. Ask your admin to approve device #' . $sr, 400);
            } else {
                // Update last used time for the device
                $db->table("trustedDevices")->where('deviceId', $device->deviceId)
                    ->update(['lastUsedAt' => date("Y-m-d H:i:s")]);
            }
        }



        $rememberMe = false;
        if (isset($input['rememberMe']) and $input['rememberMe']) {
            $rememberMe = true;
        }


        $singleSignonToken = "N/A";
        if ($config->singleSignOn) {
            $singleSignonToken = bin2hex(random_bytes(16));
        }

        $lastLoginTime = timenow();
        $lastActiveTime = timenow();

        $this->userModel->update($user->userId, [
            "singleSignonToken" => $singleSignonToken,
            "lastLoginTime" => $lastLoginTime,
            "lastActiveTime" => $lastActiveTime,
            "failedAttempts" => 0,
            "lockoutUntil" => null,
        ]);

        $totpEnabled = false;
        $totpVerified = true;
        if (!is_null($user->{'2FaToken'}) and $user->{'2FaToken'} != "" and $config->twoFactorAuth) {
            $totpEnabled = true;
            $totpVerified = false;
        }

        $db = db_connect();
        $userGroup = $db->query("SELECT * FROM `userGroups` WHERE groupId = " . $user->groupId . "")->getRow();

        // check for devModePassword 
        $devModeEnabled = false;
        if (!empty($input['devModePassword']) && $input['devModePassword'] == getenv('devModePassword') && $userGroup->isAdmin) {
            $devModeEnabled = true;
        }

        // Generate JWT token
        $token = createJWT([
            'userId'   => $user->userId,
            'devModeEnabled' => $devModeEnabled,
            'singleSignonToken' => $singleSignonToken,
            'totpVerified' => $totpVerified
        ]);

        $jti =  bin2hex(random_bytes(16));

        $refreshToken = createRefreshJWT([
            'userId'   => $user->userId,
            'jti' => $jti,
        ], $rememberMe);

        //Insert JTI into database for refresh token HERE
        $db->table('refreshTokens')->insert([
            'jti' => $jti,
            'tenantId' => $user->tenantId,
            'userId' => $user->userId,
            'deviceToken' => $deviceToken,
            'singleSignonToken' => $singleSignonToken,
            'deviceInfo' => $this->request->getUserAgent()->getAgentString(),
            'ipAddress' => $this->request->getIPAddress(),
            'rememberMe' => $rememberMe,
            'expiresAt' => date("Y-m-d H:i:s", $refreshToken['expire']),
            'createdAt' => date("Y-m-d H:i:s", $refreshToken['issuedAt'])
        ]);

        $tokenId = $db->insertID();
        assignSerialNumber($user->tenantId, "refreshTokens", "tokenId", $tokenId);

        // Prepare response data
        $responseData = [
            'token' => $token['token'],
            'refreshToken' => $refreshToken['token'],
            'totpEnabled' => $totpEnabled,
            'user'  => [
                'userId'   => setKey($user->userId, "userMaster"),
                'profileImage' => userProfilePicUrl($user->userId),
                'username' => $user->username,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'email'    => $user->email,
                'groupId'   => $user->groupId,
                'groupName' => $userGroup->groupName,
            ]
        ];

        setcookie('jwt', $token['token'], [
            'expires' => 0, // in seconds , 0 means "when the browser closes"
            'path' => '/', // path (restrict access)
            // 'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        setcookie('refreshToken', $refreshToken['token'], [
            'expires' => 0, // in seconds, 0 means "when the browser closes"
            'path' => 'api/auth/refreshToken', // path (restrict access)
            // 'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        return $this->respond(['status' => true, 'message' => 'Login successful', 'data' => $responseData], 200);
    }

    public function logout()
    {
        // Authenticate the request using JWT
        if (!myAuth::check()) {
            return $this->fail('Unauthorized', 401);
        }

        // Logout the user
        myAuth::logout();

        // Prepare response data
        $responseData = [
            'message' => 'Logout successful',
        ];

        return $this->respond(['status' => true, 'message' => 'Logout successful', 'data' => $responseData], 200);
    }

    public function refreshToken()
    {
        // if (Auth::check()) {
        //     return $this->fail('Already logged in', 400);
        // }

        $headerToken = $this->request->getHeaderLine('Authorization');
        if ($headerToken && preg_match('/Bearer\s(\S+)/', $headerToken, $matches)) {
            $refreshToken = $matches[1];
        } else {
            $refreshToken = $this->request->getCookie('refreshToken');
        }

        if (!$refreshToken) {
            return $this->fail('Missing refresh token', 401);
        }

        $decoded = verifyJWT($refreshToken);

        if (!$decoded) {
            return $this->fail('Invalid or expired refresh token', 401);
        }

        $userId = $decoded->data->userId;

        $user = $this->userModel->find($userId);

        $jti = $decoded->data->jti;

        $db = db_connect();
        $tokenRecord = $db->table('refreshTokens')
            ->where('userId', $userId)
            ->where('jti', $jti)
            ->get()
            ->getRow();

        if (!$tokenRecord) {
            $db->table("tokenReuseLogs")->insert([
                'userId' => $userId,
                'tenantId' => $user->tenantId,
                'reusedJti' => $jti,
                'ipAddress' => $this->request->getIPAddress(),
                'deviceInfo' => $this->request->getUserAgent()->getAgentString(),
                'createdAt' => date("Y-m-d H:i:s")
            ]);
            $logId = $db->insertID();

            $db->table("refreshTokens")->where('userId', $userId)->where('jti', $jti)->delete();

            assignSerialNumber($user->tenantId, "tokenReuseLogs", "logId", $logId);

            return $this->fail('Session compromised', 401);
        }

        $config = config('AppConfig');
        $singleSignonToken = "N/A";
        if ($config->singleSignOn) {
            if ($tokenRecord->singleSignonToken != $user->singleSignonToken) {
                // If single sign-on token does not match, it means the session was started in another device

                // do not delete the token here, otherwise it will add entries to tokenReuseLogs table
                // $db->table("refreshTokens")->where('userId', $userId)->where('jti', $jti)->delete();
                return $this->fail('Session started in another device', 401);
            }
        }

        $newJti = bin2hex(random_bytes(16));
        // Generate JWT token
        $token = createJWT([
            'userId'   => $user->userId,
            'devModeEnabled' => false,
            'singleSignonToken' => $singleSignonToken,
            'totpVerified' => true
        ]);

        $refreshToken = createRefreshJWT([
            'userId'   => $user->userId,
            'jti' => $newJti,
        ], $tokenRecord->rememberMe);

        //delete the old refresh token
        $db->table('refreshTokens')->where('userId', $userId)->where('jti', $jti)->delete();

        //Insert JTI into database for refresh token HERE
        $db->table('refreshTokens')->insert([
            'jti' => $newJti,
            'tenantId' => $user->tenantId,
            'userId' => $user->userId,
            'deviceToken' => $tokenRecord->deviceToken,
            'deviceInfo' => $this->request->getUserAgent()->getAgentString(),
            'ipAddress' => $this->request->getIPAddress(),
            'rememberMe' => $tokenRecord->rememberMe,
            'expiresAt' => date("Y-m-d H:i:s", $refreshToken['expire']),
            'createdAt' => date("Y-m-d H:i:s", $refreshToken['issuedAt'])
        ]);

        $tokenId = $db->insertID();
        assignSerialNumber($user->tenantId, "refreshTokens", "tokenId", $tokenId);

        // Prepare response data
        $responseData = [
            'token' => $token['token'],
            'refreshToken' => $refreshToken['token'],
        ];

        setcookie('jwt', $token['token'], [
            'expires' => 0, // in seconds, 0 means "when the browser closes"
            'path' => '/', // path (restrict access)
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        setcookie('refreshToken', $refreshToken['token'], [
            'expires' => 0, // in seconds, 0 means "when the browser closes"
            'path' => 'api/auth/refreshToken', // path (restrict access)
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        return $this->respond(['status' => true, 'data' => $responseData], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/resetPassword",
     *     summary="Reset Password for User",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *          description="Username Or Email",
     *          required=true,
     *          @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="username", type="string", description="Username Or Email of the user")
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success Message",
     *         @OA\JsonContent(
     *             type="object",
     *         )
     *     )
     * )
     */
    public function resetPassword()
    {
        // Implement password reset functionality
        $input = $this->getInputData();
        $input = $input['jsonInput'];

        if (!$input) {
            return $this->fail('Invalid JSON input', 400);
        }

        // Validate input
        $validation = \Config\Services::validation();

        $validation->setRules([
            'username' => 'required',
        ]);

        if (!$validation->run($input)) {
            return $this->fail($validation->getErrors(), 400);
        }

        // Find user by username
        $user = $this->userModel->where('username', $input['username'])->first();

        if (!$user) {

            // find by email
            $user = $this->userModel->where('email', $input['username'])->first();

            if (!$user) {
                return $this->fail('User not found', 404);
            }
        }

        // Generate new password
        $newPassword = bin2hex(random_bytes(8));
        $newPassword = "password2"; // Temporary for testing

        // Hash the password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update user password
        $this->userModel->update($user->userId, ['password' => $hashedPassword]);

        // Send email with new password
        // Implement email sending functionality

        return $this->respond(['message' => 'Password reset successful'], 200);
    }

    public function verifyTotp()
    {

        // Authenticate the request using JWT. without topt requirement
        myAuth::$totpRequired = false;
        if (!myAuth::check()) {
            return $this->fail('Unauthorized', 401);
        }

        $this->user = myAuth::user();

        $config = config('AppConfig');

        $input = $this->getInputData();
        $input = $input['jsonInput'];

        if (!$input) {
            return $this->fail('Invalid JSON input', 400);
        }

        // Validate input
        $validation = \Config\Services::validation();

        $validation->setRules([
            'totp' => 'required',
        ]);

        if (!$validation->run($input)) {
            return $this->fail($validation->getErrors(), 400);
        }

        $code = $input['totp'];

        // Retrieve the user's secret from storage (e.g., session or database)
        $secret = $this->user->{'2FaToken'};

        if (!$secret) {
            return $this->fail('Secret not found', 404);
        }

        // Recreate the TOTP instance with the stored secret
        $totp = TOTP::create($secret);
        $totp->setLabel($this->user->email);
        $totp->setIssuer($config->appName);

        // Verify the provided code
        if ($totp->verify($code)) {
            // Verification successful: proceed with authenticated action
            // Generate JWT token
            $token = createJWT([
                'userId'   => $this->user->userId,
                'username' => $this->user->username,
                'groupId'   => $this->user->groupId,
                'singleSignonToken' => $this->user->jwtData->singleSignonToken,
                'totpVerified' => true
            ], $this->user->jwtData->rememberMe);

            // Prepare response data
            $responseData = [
                'token' => $token['token'],
                'user'  => [
                    'userId'   => $this->user->userId,
                    'username' => $this->user->username,
                    'email'    => $this->user->email,
                    'groupId'   => $this->user->groupId
                ]
            ];

            return $this->respond(['status' => true, 'message' => 'Login successful', 'data' => $responseData], 200);
        } else {
            // Verification failed: prompt for code again
            return $this->fail('Invalid code', 401);
        }
    }
}
