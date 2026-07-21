<?php

namespace Modules\Backend\Setting\Controllers;

use App\Controllers\ApiBaseController;
use Modules\Backend\Setting\Models\SettingModel;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\UserPermissionLib;

class Setting extends ApiBaseController
{
    use ResponseTrait;

    protected $settingModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
    }
    public function getSetting()
    {
        if (!UserPermissionLib::userCanDo("setting", 'view')) {
            return redirect()->to('');
        }
        $settings = $this->settingModel->where("tenantId", $this->user->tenantId) // Adding tenantId condition with default value 1
            ->findAll();

        $settingsArray = [];
        foreach ($settings as $setting) {
            $settingsArray[$setting->key] = $setting->value;
        }
        return $this->respond(['status' => true, 'message' => '', 'data' => $settingsArray]);
    }

    public function saveSetting()
    {
        if (!UserPermissionLib::userCanDo("setting", 'view')) {
            return redirect()->to('');
        }

        $input = $this->getInputData();

        $jsonInput = $input['jsonInput'];

        foreach ($jsonInput as $key => $value) {

            if (is_array($value)) {
                continue;
            }

            $existingSetting = $this->db->table('tenantCompanySettings')
                ->where('key', $key)
                ->where('tenantId', $this->user->tenantId)
                ->get()
                ->getRow();


            if (is_null($existingSetting)) {
                $this->db->table('tenantCompanySettings')->insert([
                    'key'   => $key,
                    'value' => $value,
                    'tenantId' => $this->user->tenantId
                ]);
            } else {
                $this->db->table('tenantCompanySettings')->update([
                    'value' => $value
                ], [
                    'key' => $key,
                    'tenantId' => $this->user->tenantId
                ]);
            }
        }

        // Clear the cache
        service('cache')->delete('1_tenantCompanySettings');



        return $this->respondCreated(['message' => 'Data saved successfully']);
    }
}
