<?php

namespace Modules\Frontend\jobCards\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use CodeIgniter\API\ResponseTrait;


class jobCards extends BaseController
{
    use ResponseTrait;

    public function addJobcard($jobId = 0)
    {

        $data['pageTitle'] = 'Add Jobcard ';
        if ($jobId) {
            $data['pageTitle'] = 'Edit Jobcard';
        }

        $data['jobId'] = $jobId;
        AssetManager::loadLibrary('Select2');
        AssetManager::loadLibrary('DatePicker');

        $view =  'Modules\Frontend\jobCards\Views\addJobcard';

        $finalHtml = view($view, $data);

        $response = [
            'status' => true,
            // "message" => "Popup view retrived successfully",
            "data" => $finalHtml,
        ];

        return $this->respond($response, 200);
    }

    public function manageJobcard()
    {
        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');

        $data['pageTitle'] = 'Manage Jobcard';
        $data["view"] =  'Modules\Frontend\jobCards\Views\manageJobcard';

        return view('viewLoader', $data);
    }
}
