<?php

namespace Modules\Frontend\MachineMaster\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use CodeIgniter\API\ResponseTrait;


class MachineOperationConfig extends BaseController
{
    use ResponseTrait;

    public function addMachineOperation($operationConfigId = 0)
    {

        $data['pageTitle'] = 'Add Machine Operation Config  ';
        if ($operationConfigId) {
            $data['pageTitle'] = 'Edit Machine Operation Config ';
        }

        $data['operationConfigId'] = $operationConfigId;
        AssetManager::loadLibrary('Select2');
        AssetManager::loadLibrary('DatePicker');

        $view =  'Modules\Frontend\MachineMaster\Views\addMachineOperation';

        $finalHtml = view($view, $data);

        $response = [
            'status' => true,
            "message" => "",
            "data" => $finalHtml,
        ];

        return $this->respond($response, 200);
    }

    public function manageMachineOperation()
    {
        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');
        
        $data['pageTitle'] = 'Manage Machine Operation Config ';
        $data["view"] =  'Modules\Frontend\MachineMaster\Views\manageMachineOperation';

        return view('viewLoader', $data);
    }
}
