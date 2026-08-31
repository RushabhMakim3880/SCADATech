<?php

namespace Modules\Backend\OpMaster\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingsData extends Seeder
{
    public function run()
    {
        $seedName = static::class;
        $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
        if ($exists > 0) {
            return;
        }

        $settings = [
            'pause_threshold_seconds' => 30,
            'idle_threshold_seconds' => 10
        ];

        $data = [];
        foreach ($settings as $key => $value) {
            $data[] = [
                'tenantId'   => 1,
                'serialNo'   => null,
                'key'        => $key,
                'label'     => ucfirst(str_replace('_', ' ', $key)),
                'value'      => $value,
                'updatedAt'  => null,
                'updatedBy'  => null,
            ];
        }

        // Insert data into settings table
        $this->db->table('settings')->insertBatch($data);

        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
