<?php

namespace Modules\Frontend\System\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use App\Libraries\UserPermissionLib;


class StatusMaster extends BaseController
{

    public function addStatus($statusId = 0)
    {
        if (!UserPermissionLib::userCanDo("statusMaster", 'add')) {
            return redirect()->to('');
        }

        $data['pageTitle'] = 'Add Status';
        if ($statusId) {
            $data['pageTitle'] = 'Edit Status';
        }

        $data['statusId'] = $statusId;
        AssetManager::loadLibrary('IconPicker');
        AssetManager::loadLibrary('ColorPicker');

        $data["view"] =  'Modules\Frontend\System\Views\addStatus';

        return view('viewLoader', $data);
    }
    public function editStatus($statusId)
    {
        return $this->addStatus($statusId);
    }

    public function manageStatus()
    {
        if (!UserPermissionLib::userCanDo("statusMaster", 'view')) {
            return redirect()->to('');
        }
        
        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');
        // AssetManager::loadLibrary('Flatpickr');


        $data['pageTitle'] = 'Manage Status';
        $data["view"] =  'Modules\Frontend\System\Views\manageStatus';

        return view('viewLoader', $data);
    }
}
