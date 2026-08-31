<?php

namespace Modules\Frontend\punchCounters\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use CodeIgniter\API\ResponseTrait;


class punchCounters extends BaseController
{
    use ResponseTrait;

    public function managePunchcounters()
    {
        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');
        
        $data['pageTitle'] = 'Manage Punchcounters';
        $data["view"] =  'Modules\Frontend\punchCounters\Views\managePunchcounters';

        return view('viewLoader', $data);
    }
}
