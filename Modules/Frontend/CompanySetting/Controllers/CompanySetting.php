<?php

namespace Modules\Frontend\CompanySetting\Controllers;

use App\Controllers\BaseController;
use App\Libraries\UserPermissionLib;

class CompanySetting extends BaseController
{
    public function manageCompanyMasterSettings()
    {
        if (!UserPermissionLib::userCanDo("setting", 'view')) {
            return redirect()->to('');
        }

        $data['pageTitle'] = 'Manage Company Settings';
        $data["view"] = 'Modules\Frontend\Setting\Views\manageCompanyMasterSettings';

        return view('viewLoader', $data);
    }

    public function manageCompanySetting()
    {
        return $this->manageCompanyMasterSettings();
    }
}
