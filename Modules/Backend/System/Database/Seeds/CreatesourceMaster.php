<?php

namespace Modules\Backend\System\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CreatesourceMaster extends Seeder
{
    public function run()
    {
        $seedName = static::class;
        $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
        if ($exists > 0) {
            return;
        }

        $serialNo = 1; // <-- Initialize serial number counter

        $sources = ["Instagram", "Facebook", "Website", "Whatsapp", "Email", "SMS", "Radio", "Hoarding", "Newspaper Ads", "Expo/Event", "Google", "Referrence"];

        $data = [];
        foreach ($sources as $source) {
            $data[] = [
                'tenantId' => 1,
                'serialNo' => $serialNo++,
                'sourceName' => $source,
                'isActive' => 1,
                'updatedAt' => timenow(),
                'updatedBy' => null,
                'createdAt' => timenow(),
                'createdBy' => null,
            ];
        }

        $this->db->table('sourceMaster')->insertBatch($data);

        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
