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
}
