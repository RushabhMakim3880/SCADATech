<?php

namespace Modules\Frontend\PlcMaster\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use CodeIgniter\API\ResponseTrait;


class PlcTagGroupMaster extends BaseController
{
    use ResponseTrait;

    public function addPlcTag($tagGroupId = 0)
    {

        $data['pageTitle'] = 'Add Plc Tag';
        if ($tagGroupId) {
            $data['pageTitle'] = 'Edit Plc Tag';
        }

        $data['tagGroupId'] = $tagGroupId;
        AssetManager::loadLibrary('Select2');
        AssetManager::loadLibrary('DatePicker');

        $view =  'Modules\Frontend\PlcMaster\Views\addPlcTag';

        $finalHtml = view($view, $data);

        $response = [
            'status' => true,
            "message" => "",
            "data" => $finalHtml,
        ];

        return $this->respond($response, 200);
    }

    public function managePlcTag()
    {
        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');
        
        $data['pageTitle'] = 'Manage Plc Tag';
        $data["view"] =  'Modules\Frontend\PlcMaster\Views\managePlcTag';

        return view('viewLoader', $data);
    }
}
