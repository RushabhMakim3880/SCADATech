<?php

namespace Modules\Backend\System\Controllers;

use App\Controllers\ApiBaseController;
use Modules\Backend\System\Models\UserModel;
use Modules\Backend\System\Models\tenantMasterModel;
use Modules\Backend\System\Models\WebauthnCredentialModel;
// use CodeIgniter\API\ResponseTrait;
use lbuchs\WebAuthn\WebAuthn;
use App\Libraries\Auth;
// use lbuchs\WebAuthn\Binary\ByteBuffer;

class Webauth extends ApiBaseController
{
    protected $rpName = 'My Awesome Application';
    protected $rpID = 'new.test';
    protected $origin = 'https://new.test';
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function register()
    {
        $json = $this->request->getJSON();
        $userId = $json->userId;
        $user = (new UserModel())->find($userId);

        $webAuthn = $this->getWebAuthn();
        $userBinaryId = hash('sha256', (string)$userId, true); // raw binary

        $createArgs = $webAuthn->getCreateArgs(
            $userBinaryId,            // ✅ binary userId
            $user->username,        // ✅ display name (shown in UI)
            $user->firstName . ' ' . $user->lastName,        // ✅ used again for displayName
            60,                       // timeout
            true,                    // requireResidentKey
            true,                     // ✅ require biometric verification
            'platform',                     // crossPlatformAttachment
            $this->getCredentialIds($userId) // credentials to exclude
        )->publicKey;

        // Extract binary fields to convert to base64url
        $challenge = $webAuthn->getChallenge();

        $createArgs->user->id = $userBinaryId;

        // Build clean array response manually
        $response = [
            'rp' => [
                'name' => $createArgs->rp->name,
                'id' => $createArgs->rp->id,
            ],
            'user' => [
                'id' => $this->toBase64Url($userBinaryId),
                'name' => $user->username,
                'displayName' => $user->username,
            ],
            'challenge' => $this->toBase64Url($challenge),
            'pubKeyCredParams' => $createArgs->pubKeyCredParams,
            'timeout' => $createArgs->timeout,
            'attestation' => $createArgs->attestation,
            'authenticatorSelection' => $createArgs->authenticatorSelection,
            'excludeCredentials' => array_map(function ($cred) {
                return [
                    'id' => $this->toBase64Url($cred->id),
                    'type' => $cred->type,
                    'transports' => $cred->transports
                ];
            }, $createArgs->excludeCredentials),
            'extensions' => $createArgs->extensions ?? []
        ];

        return $this->response->setJSON([
            'publicKey' => $response,
            'challenge' => $response['challenge']
        ]);
    }

    public function verify()
    {
        $json = $this->request->getJSON();
        $challenge = $this->fromBase64Url($json->challenge);

        $clientData = $this->fromBase64Url($json->credential->response->clientDataJSON);
        $attestation = $this->fromBase64Url($json->credential->response->attestationObject);

        $webAuthn = $this->getWebAuthn();

        $data = $webAuthn->processCreate(
            $clientData,
            $attestation,
            $challenge,
            function ($incomingUserHandle) {
                $userId = Auth::user()->userId; // already logged in
                $expected = hash('sha256', (string)$userId, true);
                return hash_equals($incomingUserHandle, $expected);
            }
        );

        // Store credential
        $model = new WebauthnCredentialModel();

        $aaguid = base64_encode($data->AAGUID ?? '');
        $deviceInfo = getFriendlyDeviceName($aaguid);

        $model->insert([
            'userId'        => Auth::user()->userId,
            'credentialId'  => base64_encode($data->credentialId),
            'publicKey'     => $data->credentialPublicKey,
            'signCount'     => isset($data->signatureCounter) ? (int)$data->signatureCounter : null,
            'fmt'           => $data->attestationFormat ?? 'none',
            'aaguid'        => $aaguid,
            'deviceName'    => $deviceInfo['name'] ?? null,
            'darkIcon'      => $deviceInfo['icon_dark'] ?? null,
            'lightIcon'     => $deviceInfo['icon_light'] ?? null,
            'fingerprint'   => $json->fingerprint ?? null,
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function loginStart()
    {
        $webAuthn = $this->getWebAuthn();

        // Call getGetArgs() to generate the challenge properly
        $getArgs = $webAuthn->getGetArgs(
            null,         // allowCredentials (null = discoverable creds)
            60,           // timeout
            true          // require user verification
        );

        $challenge = $webAuthn->getChallenge(); // ✅ now it will work

        // session()->set('loginChallenge', $challenge);

        return $this->response->setJSON([
            'challenge' => $this->toBase64Url($challenge)
        ]);
    }



    public function login()
    {
        $json = $this->request->getJSON();

        $challenge = $this->fromBase64Url($json->challenge); // ✅ use passed challenge

        $clientData = $this->fromBase64Url($json->assertion->response->clientDataJSON);
        $authData   = $this->fromBase64Url($json->assertion->response->authenticatorData);
        $signature  = $this->fromBase64Url($json->assertion->response->signature);
        $rawId      = $this->fromBase64Url($json->assertion->rawId);
        $userHandle = $this->fromBase64Url($json->assertion->response->userHandle);


        // Lookup user by userHandle
        $userId = $this->lookupUserByHandle($userHandle);
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'error' => 'user not found']);
        }

        $cred = $this->getCredentialByRawId($rawId);
        if (!$cred) {
            return $this->response->setJSON(['success' => false, 'error' => 'credential not found']);
        }

        $webAuthn = $this->getWebAuthn();
        $webAuthn->processGet(
            $clientData,             // 1. clientDataJSON (decoded binary)
            $authData,               // 2. authenticatorData (decoded binary)
            $signature,              // 3. signature (binary)
            $cred['publicKey'],      // 4. stored public key (string format)
            $challenge,              // 5. ✅ challenge (decoded binary)
            $cred['signCount'] ?? 0, // 6. previous signature count
            true,                    // 7. require user verification
            true                     // 8. require user present
        );


        // Only if successful, update signCount
        (new WebauthnCredentialModel())->update($cred['webAuthId'], [
            'signCount' => $webAuthn->getSignatureCounter(),
            'lastUsedAt' => date('Y-m-d H:i:s')
        ]);



        // Find user by username
        $user = $this->userModel->where('userId', $userId)->first();

        if (!$user) {
            sleep(3);
            return $this->fail('User account does not exist.', 401);
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


        $rememberMe = true;
        $singleSignonToken = "N/A";
        $config = config('AppConfig');
        if ($config->singleSignOn) {
            $singleSignonToken = md5(microtime());
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

        // Generate JWT token
        $token = createJWT([
            'userId'   => $user->userId,
            'username' => $user->username,
            'groupId'   => $user->groupId,
            'tenantId' => $user->tenantId,
            'rememberMe' => $rememberMe,
            'singleSignonToken' => $singleSignonToken,
            'totpVerified' => $totpVerified
        ], $rememberMe);

        $refreshToken = createRefreshJWT([
            'userId'   => $user->userId,
            'username' => $user->username,
            'groupId'   => $user->groupId,
            'rememberMe' => $rememberMe,
            'singleSignonToken' => $singleSignonToken,
            'totpVerified' => $totpVerified
        ]);

        $db = db_connect();

        $userGroupName = "N/A";
        if ($user->groupId > 0)
            $userGroupName = $db->query("SELECT groupName FROM `userGroups` WHERE groupId = " . $user->groupId . "")->getRow()->groupName;

        // Prepare response data
        $responseData = [
            'token' => $token,
            'refreshToken' => $refreshToken,
            'totpEnabled' => $totpEnabled,
            'user'  => [
                'userId'   => $user->userId,
                'profileImage' => userProfilePicUrl($user->userId),
                'username' => $user->username,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'email'    => $user->email,
                'groupId'   => $user->groupId,
                'groupName' => $userGroupName,
            ]
        ];

        return $this->respond(['status' => true, 'message' => 'Login successful', 'data' => $responseData], 200);
    }

    protected function getCredentialIds($userId): array
    {
        return array_map(
            fn($cred) => base64_decode($cred['credentialId'], true),
            (new WebauthnCredentialModel())->where('userId', $userId)->findAll()
        );
    }


    protected function getWebAuthn(): WebAuthn
    {
        $config = config('AppConfig');
        $this->rpName = $config->appName;
        $this->rpID = parse_url(base_url(), PHP_URL_HOST);
        $this->origin = base_url();

        return new WebAuthn(
            $this->rpName,
            $this->rpID,
            $this->origin
        );
    }

    protected function getCredentials($userId): array
    {
        $model = new WebauthnCredentialModel();
        $rows = $model->where('userId', $userId)->findAll();

        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'id' => $row['credentialId'],
                'type' => 'public-key',
                'transports' => ['internal']
            ];
        }
        return $result;
    }

    private function toBase64Url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function fromBase64Url(string $data): string
    {
        $data = strtr($data, '-_', '+/');

        $remainder = strlen($data) % 4;
        if ($remainder > 0) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        $decoded = base64_decode($data, true); // ✅ strict mode
        if ($decoded === false) {
            throw new \RuntimeException("Invalid base64url string");
        }

        return $decoded;
    }


    protected function getCredentialByRawId($rawId)
    {
        return (new WebauthnCredentialModel())
            ->where('credentialId', base64_encode($rawId))
            ->first();
    }

    private function lookupUserByHandle($userHandle)
    {
        foreach ((new UserModel())->findAll() as $user) {
            $expected = hash('sha256', (string)$user->userId, true);
            if (hash_equals($expected, $userHandle)) {
                return $user->userId;
            }
        }
        return null;
    }
}
