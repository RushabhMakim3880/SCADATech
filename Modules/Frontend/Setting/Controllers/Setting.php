<?php

namespace Modules\Frontend\Setting\Controllers;

use App\Controllers\BaseController;
use App\Libraries\UserPermissionLib;

class Setting extends BaseController
{

    public function settings($settingId = 0)
    {
        if (!UserPermissionLib::userCanDo("setting", 'view')) {
            return redirect()->to('');
        }

        $data['pageTitle'] = 'Setting';
        if ($settingId) {
            $data['pageTitle'] = 'Setting';
        }

        $data['settingId'] = $settingId;

        $data["view"] =  'Modules\Frontend\Setting\Views\setting';

        return view('viewLoader', $data);
    }

    public function companySetting($settingId = 0)
    {
        if (!UserPermissionLib::userCanDo("setting", 'view')) {
            return redirect()->to('');
        }

        $data['pageTitle'] = 'Company Setting';
        $data['settingId'] = $settingId;

        $data["view"] =  'Modules\Frontend\Setting\Views\companySetting';

        return view('viewLoader', $data);
    }

    public function manageCompanyMasterSettings()
    {
        if (!UserPermissionLib::userCanDo("setting", 'view')) {
            return redirect()->to('');
        }

        $data['pageTitle'] = 'Manage Company Settings';
        $data["view"] = 'Modules\Frontend\Setting\Views\manageCompanyMasterSettings';

        return view('viewLoader', $data);
    }

    public function addCompanyMasterSetting($id = 0)
    {
        if (!UserPermissionLib::userCanDo("setting", 'view')) {
            return redirect()->to('');
        }

        $data['pageTitle'] = $id ? 'Edit Company Setting' : 'Add Company Setting';
        $data['id'] = $id;

        return view('Modules\Frontend\Setting\Views\addCompanyMasterSetting', $data);
    }
}
