<?php

namespace Modules\Frontend\MachineMaster\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use CodeIgniter\API\ResponseTrait;


class MachineMaster extends BaseController
{
    use ResponseTrait;

    public function editMachine($machineId = 0)
    {
        return $this->addMachine($machineId);
    }

    public function addMachine($machineId = 0)
    {

        $data['pageTitle'] = 'Add Machine ';
        if ($machineId) {
            $data['pageTitle'] = 'Edit Machine';
        }

        $data['machineId'] = $machineId;
        AssetManager::loadLibrary('Select2');
        AssetManager::loadLibrary('DatePicker');

        $data['view'] =  'Modules\Frontend\MachineMaster\Views\addMachine';

        return view('viewLoader', $data);
    }

    public function manageMachine()
    {
        //redirect to MachineMaster/editMachine/c3pFanRoMUlWVFBLaVFDd2tpajJ4dz09OjpEcXRZRjVkWXBYekdKd3FUbHI5RXJBPT0~
        return redirect()->to('MachineMaster/editMachine/' . setkey(1, "machineMaster"));


        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');

        $data['pageTitle'] = 'Manage Machine';
        $data["view"] =  'Modules\Frontend\MachineMaster\Views\manageMachine';

        return view('viewLoader', $data);
    }

    public function machineSetup()
    {

        $data = [];

        $db = db_connect();

        $db = db_connect();
        $machineDetails =  $db->table('machineDetails')
            ->where('tenantId', 1)
            ->where('machineId', 1)
            ->get()
            ->getResultArray();

        $data['machineDetails'] = $machineDetails;

        $view =  'Modules\Frontend\MachineMaster\Views\machineSetup';

        $finalHtml = view($view, $data);

        $response = [
            'status' => true,
            "message" => "",
            "data" => $finalHtml,
        ];

        return $this->respond($response, 200);
    }
}
