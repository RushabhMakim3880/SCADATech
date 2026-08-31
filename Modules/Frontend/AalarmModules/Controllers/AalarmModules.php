<?php

namespace Modules\Frontend\AalarmModules\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use CodeIgniter\API\ResponseTrait;


class AalarmModules extends BaseController
{
    use ResponseTrait;

    public function addAlarmconfig($alarmId = 0)
    {

        $data['pageTitle'] = 'Add Alarm Config';
        if ($alarmId) {
            $data['pageTitle'] = 'Edit Alarm Config';
        }

        $data['alarmId'] = $alarmId;
        AssetManager::loadLibrary('Select2');
        AssetManager::loadLibrary('DatePicker');

        $view =  'Modules\Frontend\AalarmModules\Views\addAlarmconfig';

        $finalHtml = view($view, $data);

        $response = [
            'status' => true,
            "message" => "",
            "data" => $finalHtml,
        ];

        return $this->respond($response, 200);
    }

    public function manageAlarmconfig()
    {
        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');
        
        $data['pageTitle'] = 'Manage Alarm Config';
        $data["view"] =  'Modules\Frontend\AalarmModules\Views\manageAlarmconfig';

        return view('viewLoader', $data);
    }
}
