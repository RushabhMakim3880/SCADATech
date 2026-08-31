<?php

namespace Modules\Frontend\System\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use App\Libraries\UserPermissionLib;

class LocationMaster extends BaseController
{
    public function addLocationMaster($locationId = 0)
    {
        if (!UserPermissionLib::userCanDo("locationMaster", 'add')) {
            return redirect()->to('');
        }

        $data['pageTitle'] = 'Add Location Master';
        if ($locationId) {
            $data['pageTitle'] = 'Edit Location Master';
        }

        $data['locationId'] = $locationId;


        $data["view"] =  'Modules\Frontend\System\Views\addLocationMaster';

        return view('viewLoader', $data);
    }

    public function editLocationMaster($locationId)
    {

        return $this->addLocationMaster($locationId);
    }

    public function manageLocationMaster()
    {
        if (!UserPermissionLib::userCanDo("locationMaster", 'view')) {
            return redirect()->to('');
        }

        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');
        // AssetManager::loadLibrary('Flatpickr');


        $data['pageTitle'] = 'Manage Location Master';
        $data["view"] =  'Modules\Frontend\System\Views\manageLocationMaster';

        return view('viewLoader', $data);
    }
}
