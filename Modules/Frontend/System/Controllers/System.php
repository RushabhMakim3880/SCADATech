<?php

namespace Modules\Frontend\System\Controllers;

use App\Controllers\BaseController;
use Modules\Backend\System\Models\UserModel;
use OTPHP\TOTP;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

use App\Libraries\AssetManager;
use App\Libraries\UserPermissionLib;
use App\Libraries\Auth;

class System extends BaseController
{

    public function totpSetup()
    {
        // Create a new TOTP instance with the application name as label prefix
        $config = config('AppConfig');

        if (!is_null($this->user->{'2FaToken'}) and $this->user->{'2FaToken'} != '') {
            $secret = $this->user->{'2FaToken'};
        } else {
            // Create a new TOTP instance and generate a secret
            $totp = TOTP::create();
            $totp->setLabel($this->user->email);
            $totp->setIssuer($config->appName);
            $secret = $totp->getSecret();

            // Save new secret to the user's record
            $userModel = new UserModel();
            $this->user->{'2FaToken'} = $secret;
            $userModel->update($this->user->userId, ['2FaToken' => $secret]);
        }

        // Recreate the TOTP instance using the (existing or new) secret
        $totp = TOTP::create($secret);
        $totp->setLabel($this->user->email);
        $totp->setIssuer($config->appName);

        // Generate provisioning URI
        $provisioningUri = $totp->getProvisioningUri();

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($provisioningUri)
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(200)
            ->margin(0)
            ->build();

        // Get the QR code as data URI
        $qrCodeDataUri = $result->getDataUri();

        $data['pageTitle'] = 'Setup Two-Factor Authentication';
        $data['qrCodeDataUri'] = $qrCodeDataUri;
        $data['secret'] = $secret; // Optionally display the secret
        $data["view"] =  'Modules\Frontend\System\Views\totpSetup';

        return view('viewLoader', $data);
    }

    /************************  Logo & BG screen ****************************/
    public function addLogoBg()
    {
        $data['pageTitle'] = 'Logo & Background';

        $data['darkBg'] = getLogoUrl('dark');
        $data['favicon'] = getFaviconUrl();
        $data['loginBg'] = getLoginBgUrl();
        $data['lightBg'] = getLogoUrl('light');
        $data['printLg'] = getLogoUrl('print');

        AssetManager::loadLibrary('ImageUpload');
        AssetManager::addJs('Modules/branding/addLogoBg.js');
        $data["view"] =  'Modules\Frontend\System\Views\addLogoBg';
        return view('viewLoader', $data);
    }
    /************************  Logo & BG screen ****************************/

    public function manageGroupPermissions()
    {
        if (!UserPermissionLib::userCanDo("userMaster", ['managePermission'])) {
            return redirect()->to('');
        }

        $data['pageTitle'] = 'Manage Group Permission';
        $data["view"] =  'Modules\Frontend\System\Views\manageGroupPermissions';

        AssetManager::addJs('Modules/users/manageGroupPermissions.js');

        return view('viewLoader', $data);
    }

    /************************  UPLOAD APK FILE ****************************/
    public function uploadapk()
    {
        if (!UserPermissionLib::userCanDo("superSaasAdmin", ['uploadapk'])) {
            return redirect()->to('');
        }
        $data['pageTitle'] = 'Upload APK';
        $data["view"] =  'Modules\Frontend\System\Views\uploadapk';
        return view('viewLoader', $data);
    }
    /************************ UPLOAD APK FILE ****************************/


    public function docs()
    {
        // if not in development mode, show 404 error
        if (ENVIRONMENT !== 'development') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }


        $data["view"] =  'Modules\Frontend\System\Views\docs';

        return view('ajaxViewLoader', $data);
    }

    public function viewDocs($path = '', $path2 = '', $path3 = '', $path4 = '', $path5 = '')
    {
        helper('filesystem');

        $file = ROOTPATH . 'docs/' . $path . '/' . $path2 . '/' . $path3 . '/' . $path4 . '/' . $path5;
        $file = str_replace('//', '/', $file);
        $file = str_replace('//', '/', $file);
        $file = str_replace('//', '/', $file);
        $file = str_replace('//', '/', $file);
        $file = str_replace('//', '/', $file);

        //remove trailing slash
        $file = rtrim($file, '/');


        // die($file);

        // Security check
        if (!file_exists($file) || pathinfo($file, PATHINFO_EXTENSION) !== 'md') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setContentType('text/markdown')
            ->setBody(file_get_contents($file));
    }

    public function userGuide()
    {
        $data["view"] =  'Modules\Frontend\System\Views\userGuide';

        return view('ajaxViewLoader', $data);
    }

    public function viewUserGuide($path = '', $path2 = '', $path3 = '', $path4 = '', $path5 = '')
    {
        helper('filesystem');

        $file = ROOTPATH . 'userGuide/' . $path . '/' . $path2 . '/' . $path3 . '/' . $path4 . '/' . $path5;
        $file = str_replace('//', '/', $file);
        $file = str_replace('//', '/', $file);
        $file = str_replace('//', '/', $file);
        $file = str_replace('//', '/', $file);
        $file = str_replace('//', '/', $file);

        //remove trailing slash
        $file = rtrim($file, '/');


        // die($file);

        // Security check
        if (!file_exists($file) || pathinfo($file, PATHINFO_EXTENSION) !== 'md') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setContentType('text/markdown')
            ->setBody(file_get_contents($file));
    }
}
