<?php

namespace Modules\Frontend\System\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use App\Libraries\UserPermissionLib;


class CustomStatusMaster extends BaseController
{
    public function addCustomStatus($fieldId = 0)
    {
        if (!UserPermissionLib::userCanDo("customStatusFields", 'add')) {
            return redirect()->to('');
        }

        $data['pageTitle'] = 'Add Status Field';
        if ($fieldId) {
            $data['pageTitle'] = 'Edit Custom Status';
        }

        $data['fieldId'] = $fieldId;

        $data["view"] =  'Modules\Frontend\System\Views\addCustomStatus';

        return view('viewLoader', $data);
    }
    public function editCustomStatus($fieldId)
    {
        return $this->addCustomStatus($fieldId);
    }

    public function manageCustomStatus()
    {
        if (!UserPermissionLib::userCanDo("customStatusFields", 'view')) {
            return redirect()->to('');
        }

        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');
        // AssetManager::loadLibrary('Flatpickr');


        $data['pageTitle'] = 'Manage Status Field';
        $data["view"] =  'Modules\Frontend\System\Views\manageCustomStatus';

        return view('viewLoader', $data);
    }
}
