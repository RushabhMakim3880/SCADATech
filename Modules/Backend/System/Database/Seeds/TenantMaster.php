<?php

namespace Modules\Backend\System\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TenantMaster extends Seeder
{
    public $priority = 2;

    public function run()
    {
        $seedName = static::class;
        $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
        if ($exists > 0) {
            return;
        }

        $tenantData = [
            "subDomain" => "datenics",
            "customDomain" => "datenics.com",
            "tenantName" => "Datenics",
            "companyName" => "Datenics",
            "mobile" => "1234567890",
            "email" => "info@datenics.com",
            "companyAddress" => "",
            "locationId" => null,
            "isActive" => 1,
            "createdBy" => 1,
            "createdOn" => date("Y-m-d H:i:s"),
            "updatedBy" => 1,
            "updatedOn" => date("Y-m-d H:i:s")
        ];

        $this->db->table("tenantMaster")->insert($tenantData);

        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
