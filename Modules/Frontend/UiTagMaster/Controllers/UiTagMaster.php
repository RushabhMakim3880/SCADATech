<?php

namespace Modules\Frontend\UiTagMaster\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use CodeIgniter\API\ResponseTrait;


class UiTagMaster extends BaseController
{
    use ResponseTrait;

    public function addUiTag($uiTagId = 0)
    {

        $data['pageTitle'] = 'Add Scada Tag ';
        if ($uiTagId) {
            $data['pageTitle'] = 'Edit Scada Tag';
        }

        $data['uiTagId'] = $uiTagId;
        AssetManager::loadLibrary('Select2');
        AssetManager::loadLibrary('DatePicker');

        $view =  'Modules\Frontend\UiTagMaster\Views\addUiTag';

        $finalHtml = view($view, $data);

        $response = [
            'status' => true,
            "message" => "Popup view retrived successfully",
            "data" => $finalHtml,
        ];

        return $this->respond($response, 200);
    }

    public function manageUiTag()
    {
        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');
        AssetManager::loadLibrary('SweetAlert2');
        
        $data['pageTitle'] = 'Manage Scada Tag';
        $data["view"] =  'Modules\Frontend\UiTagMaster\Views\manageUiTag';

        return view('viewLoader', $data);
    }
}
