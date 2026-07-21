<?php

namespace Modules\Frontend\productionMasters\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use CodeIgniter\API\ResponseTrait;


class productionMasters extends BaseController
{
    use ResponseTrait;

    public function manageProductionmaster()
    {
        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');
        
        $data['pageTitle'] = 'Manage Productionmaster';
        $data["view"] =  'Modules\Frontend\productionMasters\Views\manageProductionmaster';

        return view('viewLoader', $data);
    }
}
