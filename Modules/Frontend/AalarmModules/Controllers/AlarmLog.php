<?php

namespace Modules\Frontend\AalarmModules\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use CodeIgniter\API\ResponseTrait;


class AlarmLog extends BaseController
{
    use ResponseTrait;

    // public function addAlarmlog($logId = 0)
    // {

    //     $data['pageTitle'] = 'Add Alarm Log';
    //     if ($logId) {
    //         $data['pageTitle'] = 'Edit Alarm Log';
    //     }

    //     $data['logId'] = $logId;
    //     AssetManager::loadLibrary('Select2');
    //     AssetManager::loadLibrary('DatePicker');

    //     $view =  'Modules\Frontend\AlarmLog\Views\addAlarmlog';

    //     $finalHtml = view($view, $data);

    //     $response = [
    //         'status' => true,
    //         "message" => "Popup view retrived successfully",
    //         "data" => $finalHtml,
    //     ];

    //     return $this->respond($response, 200);
    // }

    public function manageAlarmlog()
    {
        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');
        
        $data['pageTitle'] = 'Manage Alarm Log';
        $data["view"] =  'Modules\Frontend\AalarmModules\Views\manageAlarmlog';

        return view('viewLoader', $data);
    }
}
