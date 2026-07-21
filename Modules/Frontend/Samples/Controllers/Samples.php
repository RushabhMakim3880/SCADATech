<?php

namespace Modules\Frontend\Samples\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use App\Libraries\UserPermissionLib;
use CodeIgniter\API\ResponseTrait;

class Samples extends BaseController
{

    use ResponseTrait;

    public function index()
    {

        if (!UserPermissionLib::userCanDo("superSaasAdmin", 'sampleDashboard')) {
            return redirect()->to('');
        }

        $data["view"] = '\Modules\Frontend\Samples\Views\sample';
        $data["pageTitle"] = "Home";

        // Load custom CSS and JS files
        // AssetManager::addCss('assets/css/app.css');
        AssetManager::addJs('Modules/Samples/sample.js');


        // Load predefined libraries
        AssetManager::loadLibrary('echarts');

        return view('viewLoader', $data);
    }

    public function sse()
    {
        $data["view"] = '\Modules\Frontend\Samples\Views\sse';
        $data["pageTitle"] = "Home";

        // Load custom CSS and JS files
        // AssetManager::addCss('assets/css/app.css');
        AssetManager::addJs('assets/js/sharedWorker.js');

        // Load predefined libraries
        AssetManager::loadLibrary('echarts');

        return view('viewLoader', $data);
    }
    public function addSampleNew($newSampleId = 0)
    {
        $data["pageTitle"] = "Sample New11";
        if ($newSampleId) {
            $data["pageTitle"] = "edit New1";
        }

        $data['newSampleId'] = $newSampleId;

        $data["view"] = '\Modules\Frontend\Samples\Views\addSampleNew';
        AssetManager::loadLibrary('DatePicker');
        AssetManager::loadLibrary('ColorPicker');
        AssetManager::loadLibrary('IconPicker');

        AssetManager::addJs('Modules/Samples/sampleModule.js');

        AssetManager::loadLibrary('ImageUpload');
        $data['profile_pic'] = sampleProfilePicUrl($newSampleId);

        return view('viewLoader', $data);
    }

    public function addSampleNewAjax($newSampleId = 0)
    {
        $data["pageTitle"] = "Sample New11";
        if ($newSampleId) {
            $data["pageTitle"] = "edit New1";
        }

        $data['newSampleId'] = $newSampleId;

        $view = '\Modules\Frontend\Samples\Views\addSampleNewAjax';
        $data['profile_pic'] = sampleProfilePicUrl($newSampleId);

        $finalHtml = view($view, $data);

        $response = [
            'status' => true,
            "message" => "Popup view retrived successfully",
            "data" => $finalHtml,
        ];

        return $this->respond($response, 200);

        // return view('ajaxViewLoader', $data);
    }

    public function editSampleNew($newSampleId)
    {

        return $this->addSampleNew($newSampleId);
        // debug($newSampleId);die;
    }

    public function manageSampleNew()
    {
        AssetManager::loadLibrary('DataTables');
        AssetManager::loadLibrary('Select2');
        AssetManager::loadLibrary('IconPicker');
        AssetManager::loadLibrary('ColorPicker');
        AssetManager::loadLibrary('InternationalNumber');
        AssetManager::addJs('Modules/Samples/sampleModule.js');


        AssetManager::loadLibrary('DatePicker');

        AssetManager::addJs('Modules/Samples/sampleModule.js');

        AssetManager::loadLibrary('ImageUpload');


        $data['pageTitle'] = 'Manage Sample';
        $data["view"] = '\Modules\Frontend\Samples\Views\manageSampleNew';
        return view('viewLoader', $data);
    }
}
