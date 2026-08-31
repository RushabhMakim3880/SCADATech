<?php

namespace Modules\Frontend\PlcMaster\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use CodeIgniter\API\ResponseTrait;


class PlcMaster extends BaseController
{
    use ResponseTrait;

    public function addPlcMaster($plcId = 0)
    {

        $data['pageTitle'] = 'Add Plc ';
        if ($plcId) {
            $data['pageTitle'] = 'Edit Plc';
        }

        $data['plcId'] = $plcId;
        AssetManager::loadLibrary('Select2');
        AssetManager::loadLibrary('DatePicker');

        $data["view"] =  'Modules\Frontend\PlcMaster\Views\addPlcMaster';
        return view('viewLoader', $data);
    }

     public function editPlcMaster($plcId)
    {
        return $this->addPlcMaster($plcId);
    }

    public function managePlcMaster()
    {
        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');

        $data['pageTitle'] = 'Manage Plc Master';
        $data["view"] =  'Modules\Frontend\PlcMaster\Views\managePlcMaster';

        return view('viewLoader', $data);
    }
}
