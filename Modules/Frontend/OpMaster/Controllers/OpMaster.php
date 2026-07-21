<?php

namespace Modules\Frontend\OpMaster\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AssetManager;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\UserPermissionLib;

class OpMaster extends BaseController
{
    use ResponseTrait;
    var $isIpc = false;
    var $identifier = '';

    //constructor
    public function __construct()
    {
        $request = \Config\Services::request();
        $this->identifier = $request->getHeaderLine('X-App-Identifier');

        if (($this->identifier !== "" and $this->identifier != null and $this->identifier == getenv('appIdentifier')) or getenv("CI_ENVIRONMENT") == 'development') {
            $this->isIpc = true;
        }
    }

    public function start()
    {
        // AssetManager::loadLibrary('DataTables');
        // AssetManager::loadLibrary('Select2');
        if ($this->isIpc) {
            AssetManager::addJs('Modules/plcScada/websocket.js');
            AssetManager::addJs('Modules/plcScada/productionRuntime.js');
            AssetManager::addJs('Modules/plcScada/opMaster.js');
            AssetManager::addJs('Modules/plcScada/AngleBarVisualizer.js');
            AssetManager::loadLibrary('konva');
            AssetManager::loadLibrary('echarts');
            AssetManager::loadLibrary('virtualNumKeypad');
            // Sortable
            AssetManager::loadLibrary('Sortable');

            AssetManager::loadLibrary('DataTables');

            AssetManager::loadLibrary('Select2');
            AssetManager::loadLibrary('DatePicker');
            AssetManager::addJs("Modules/ItemRecipeMaster/addItemrecipemaster.js");
        } else {
            $data['identifier'] = $this->identifier;
        }



        //tag permission to frontend
        $tagPermisionGroups = tagPermisionGroups();
        $disAllowedTags = [];
        foreach ($tagPermisionGroups as $group => $p) {
            foreach ($p as $permission => $tags) {
                if (!UserPermissionLib::userCanDo($group, $permission)) {
                    $disAllowedTags = array_merge($disAllowedTags, $tags);
                }
            }
        }

        $data['pageTitle'] = 'Manage Machine';
        $data['isIpc'] = $this->isIpc;
        $data['disAllowedTags'] = $disAllowedTags;
        $data["view"] =  'Modules\Frontend\OpMaster\Views\start';

        return view('viewLoader', $data);
    }

    // public function home()
    // {
    //     $data =  view('Modules\Frontend\OpMaster\Views\home');
    //     return $this->respond(['status' => true, 'htmlContent' => $data]);
    // }

    public function machineParameters()
    {
        $data =  view('Modules\Frontend\OpMaster\Views\machineParameters');
        return $this->respond(['status' => true, 'htmlContent' => $data]);
    }

    public function homing()
    {
        $data =  view('Modules\Frontend\OpMaster\Views\homing');
        return $this->respond(['status' => true, 'htmlContent' => $data]);
    }

    public function manualControl()
    {
        $data =  view('Modules\Frontend\OpMaster\Views\manualControl');
        return $this->respond(['status' => true, 'htmlContent' => $data]);
    }

    public function autoControl()
    {
        $data =  view('Modules\Frontend\OpMaster\Views\autoControl');
        return $this->respond(['status' => true, 'htmlContent' => $data]);
    }

    // programPrepare
    public function programPrepare()
    {
        $data =  view('Modules\Frontend\OpMaster\Views\programPrepare');
        return $this->respond(['status' => true, 'htmlContent' => $data]);
    }

    public function log()
    {
        $data =  view('Modules\Frontend\OpMaster\Views\log');
        return $this->respond(['status' => true, 'htmlContent' => $data]);
    }
}
