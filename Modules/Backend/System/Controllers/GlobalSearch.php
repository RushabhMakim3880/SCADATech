<?php

namespace Modules\Backend\System\Controllers;

use App\Controllers\BaseController;
use App\Controllers\ApiBaseController;

use Modules\Backend\GlobalSearchLogic\Services\SearchProvider;

class GlobalSearch extends ApiBaseController
{
    public function index()
    {

        $json = $this->request->getJSON(true);
        $query = $json['data']['search'] ?? '';

        $html = (new SearchProvider())->run($query);

        return $this->response->setJSON([
            'status' => true,
            'message' => '',
            'data' => $html
        ]);
    }
}
