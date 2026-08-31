<?php

namespace Modules\Frontend\ItemRecipeMaster\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;



class ItemRecipeMaster extends BaseController
{

    public function addItemrecipemaster($itemRecipeId = 0)
    {

        $data['pageTitle'] = 'Add Program';
        if ($itemRecipeId) {
            $data['pageTitle'] = 'Edit Program';
        }

        $data['itemRecipeId'] = $itemRecipeId;

        $data["view"] =  'Modules\Frontend\ItemRecipeMaster\Views\addItemrecipemaster';

        $html = view('ajaxViewLoader', $data);

        $response = [
            'status' => true,
            'data' => $html
        ];

        return $this->response->setJSON($response);
    }

    public function editItemrecipemaster($itemRecipeId)
    {
        return $this->addItemrecipemaster($itemRecipeId);
    }

    public function manageItemrecipemaster()
    {
        // AssetManager::loadLibrary('DataTables');
        // AssetManager::loadLibrary('Select2');
        // AssetManager::loadLibrary('konva');

        $data['pageTitle'] = 'Manage Program';
        $data["view"] =  'Modules\Frontend\ItemRecipeMaster\Views\manageItemrecipemaster';

        $html =  view('ajaxViewLoader', $data);

        $response = [
            'status' => true,
            'data' => $html
        ];

        return $this->response->setJSON($response);
    }

    public function importFile()
    {
        $data['pageTitle'] = 'Import Program';
        $data["view"] =  'Modules\Frontend\ItemRecipeMaster\Views\importFile';

        $html = view('ajaxViewLoader', $data);

        $response = [
            'status' => true,
            'data' => $html
        ];

        return $this->response->setJSON($response);
    }
}
