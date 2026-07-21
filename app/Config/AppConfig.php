<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use App\Libraries\Tenant;

class AppConfig extends BaseConfig
{
    public function __construct()
    {
        parent::__construct();

        $tenantId = Tenant::id();

        $configData = \App\Libraries\SettingManager::getAllResolvedSettings($tenantId);
        foreach ($configData as $key => $value) {
            $this->$key = $value;
        }
    }
}
