<?php

namespace Modules\Frontend\System\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use App\Libraries\UserPermissionLib;

class MenuConfig extends BaseController
{
    public function appConfig($appconfigId = 0)
    {
        if (!UserPermissionLib::userCanDo("superSaasAdmin", 'manageAppConfig')) {
            return redirect()->to('');
        }

        $data['pageTitle'] = 'App Config';
        if ($appconfigId) {
            $data['pageTitle'] = 'App Config';
        }

        $data['appconfigId'] = $appconfigId;

        $data["view"] =  'Modules\Frontend\System\Views\appConfig';

        return view('viewLoader', $data);
    }

    public function addMenuConfig($menuConfigId = 0)
    {
        if (!UserPermissionLib::userCanDo("superSaasAdmin", 'manageMenuConfig')) {
            return redirect()->to('');
        }

        // die('addMenuConfig');
        $data['pageTitle'] = '';


        $data['menuId'] = $menuConfigId;

        $data["view"] =  'Modules\Frontend\System\Views\addMenuConfig';
        AssetManager::addJs('Modules/menuConfig/menuConfig.js');
        AssetManager::loadLibrary("IconPicker");
        AssetManager::loadLibrary("Sortable");


        return view('viewLoader', $data);
    }
}
