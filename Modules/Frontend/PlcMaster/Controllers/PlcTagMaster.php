<?php

namespace Modules\Frontend\PlcMaster\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use CodeIgniter\API\ResponseTrait;


class PlcTagMaster extends BaseController
{
    use ResponseTrait;

    public function addPlctagmaster($tagId = 0)
    {

        $data['pageTitle'] = 'Add PlcTagMaster ';
        if ($tagId) {
            $data['pageTitle'] = 'Edit PlcTagMaster';
        }

        $data['tagId'] = $tagId;
        AssetManager::loadLibrary('Select2');
        AssetManager::loadLibrary('DatePicker');

        $view =  'Modules\Frontend\PlcMaster\Views\addPlctagmaster';

        $finalHtml = view($view, $data);

        $response = [
            'status' => true,
            "message" => "",
            "data" => $finalHtml,
        ];

        return $this->respond($response, 200);
    }

    public function managePlctagmaster($plcId = 0)
    {


        $data['plcId'] = $plcId;

        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');

        $data['pageTitle'] = 'Manage PlcTagMaster';
        $data["view"] =  'Modules\Frontend\PlcMaster\Views\managePlctagmaster';

        return view('viewLoader', $data);
    }
}
