<?php

namespace Modules\Frontend\System\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use App\Libraries\UserPermissionLib;


class TrustedDevice extends BaseController
{
    //Trusted Devices screen
    public function manageTrustedDevice()
    {
        if (!UserPermissionLib::userCanDo("userMaster", 'manageApprovedDevices')) {
            return redirect()->to('');
        }

        $config = config('AppConfig');
        if (!$config->limitLoginToTrustedDevices == 1) {
            return redirect()->to('');
        }

        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');

        $data['pageTitle'] = 'Manage Trusted Device';
        $data["view"] =  'Modules\Frontend\System\Views\manageTrustedDevice';

        return view('viewLoader', $data);
    }
}
