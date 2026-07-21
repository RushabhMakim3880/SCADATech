<?php

namespace Modules\Frontend\System\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use App\Libraries\UserPermissionLib;


class TenantMaster extends BaseController
{
public function addTenant($tenantId = 0)
    {
        if (!UserPermissionLib::userCanDo("tenantMaster", 'add') and $tenantId == 0) {
            return redirect()->to('');
        }

        if (!UserPermissionLib::userCanDo("tenantMaster", 'edit') and $tenantId != 0) {
            return redirect()->to('');
        }

        $data['pageTitle'] = 'Add Tenant';
        if ($tenantId) {
            $data['pageTitle'] = 'Edit Tenant';
        }

        $data['tenantId'] = $tenantId;
        AssetManager::loadLibrary('InternationalNumber');


        $data["view"] =  'Modules\Frontend\System\Views\addTenant';

        return view('viewLoader', $data);
    }
    public function editTenant($tenantId)
    {
        return $this->addTenant($tenantId);
    }

    public function manageTenant()
    {
        if (!UserPermissionLib::userCanDo("tenantMaster", ['view', 'viewAll'])) {
            return redirect()->to('');
        }

        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');
        // AssetManager::loadLibrary('Flatpickr');


        $data['pageTitle'] = 'Manage Tenant';
        $data["view"] =  'Modules\Frontend\System\Views\manageTenant';

        return view('viewLoader', $data);
    }
}
