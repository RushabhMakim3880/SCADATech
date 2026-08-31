<?php

namespace Modules\Backend\System\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ModuleMasterData extends Seeder
{
    public $priority = 4;

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
                "moduleName" => "Lead",
                "moduleDescription" => "Lead Module",
                "createdAt" => date("Y-m-d H:i:s"),
                "updatedAt" => date("Y-m-d H:i:s"),

            ],
        ];


        $this->db->table('moduleMaster')->insertBatch($data);

        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
