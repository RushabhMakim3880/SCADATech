<?php

namespace Modules\Frontend\programAlignMaster\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;

class programAlignMaster extends BaseController
{
    public function manageProgramalignmaster()
    {
        AssetManager::loadLibrary('DataTables');
        
        $data['pageTitle'] = 'Manage Programalignmaster';
        $data["view"] =  'Modules\Frontend\programAlignMaster\Views\manageProgramalignmaster';

        return view('viewLoader', $data);
    }
}