<?php

namespace Modules\Frontend\{{MODULE_NAME}}\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;



class {{MODULE_NAME}} extends BaseController
{

    public function add{{ITEM_NAME}}(${{PRIMARY_FIELD}} = 0)
    {

        $data['pageTitle'] = 'Add {{ITEM_NAME}} ';
        if (${{PRIMARY_FIELD}}) {
            $data['pageTitle'] = 'Edit {{ITEM_NAME}}';
        }

        $data['{{PRIMARY_FIELD}}'] = ${{PRIMARY_FIELD}};
        AssetManager::loadLibrary('Select2');
        AssetManager::loadLibrary('DatePicker');

        $data["view"] =  'Modules\Frontend\{{MODULE_NAME}}\Views\add{{ITEM_NAME}}';

        return view('viewLoader', $data);
    }

    public function edit{{ITEM_NAME}}(${{PRIMARY_FIELD}})
    {
        return $this->add{{ITEM_NAME}}(${{PRIMARY_FIELD}});
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
