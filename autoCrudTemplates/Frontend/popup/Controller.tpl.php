<?php

namespace Modules\Frontend\{{MODULE_NAME}}\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use CodeIgniter\API\ResponseTrait;


class {{MODULE_NAME}} extends BaseController
{
    use ResponseTrait;

    public function add{{ITEM_NAME}}(${{PRIMARY_FIELD}} = 0)
    {

        $data['pageTitle'] = 'Add {{ITEM_NAME}} ';
        if (${{PRIMARY_FIELD}}) {
            $data['pageTitle'] = 'Edit {{ITEM_NAME}}';
        }

        $data['{{PRIMARY_FIELD}}'] = ${{PRIMARY_FIELD}};
        AssetManager::loadLibrary('Select2');
        AssetManager::loadLibrary('DatePicker');

        $view =  'Modules\Frontend\{{MODULE_NAME}}\Views\add{{ITEM_NAME}}';

        $finalHtml = view($view, $data);

        $response = [
            'status' => true,
            "message" => "Popup view retrived successfully",
            "data" => $finalHtml,
        ];

        return $this->respond($response, 200);
    }

    public function manage{{ITEM_NAME}}()
    {
        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');
        
        $data['pageTitle'] = 'Manage {{ITEM_NAME}}';
        $data["view"] =  'Modules\Frontend\{{MODULE_NAME}}\Views\manage{{ITEM_NAME}}';

        return view('viewLoader', $data);
    }
}
