<?php

namespace Modules\Backend\System\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DepartmentMasterData extends Seeder
{
    public function run()
    {
         $seedName = static::class;
        $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
        if ($exists > 0) {
            return;
        }

         $data = [
            [
                "tenantId" => 1,
                "serialNo" => 1,
                "departmentName"=> "Default",
                "updatedBy" => 1,
                "createdAt" => date("Y-m-d H:i:s"),
                "createdBy" => 1
            ],
            ];


        $this->db->table('departmentMaster')->insertBatch($data);

        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
