<?php

namespace Modules\Backend\OpMaster\Database\Seeds;

use CodeIgniter\Database\Seeder;

class IdleReasonsData extends Seeder
{
    // public $priority = 100;

    public function run()
    {
        $seedName = static::class;
        $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
        if ($exists > 0) {
            return;
        }

        $reasons = [
            'No Job',
            'No Operator',
            'Maintenance',
            'Power Save',
            'Material Shortage',
            'Machine Setup',
            'Cleaning',
            'Inspection',
            'Calibration',
            'Other',
        ];

        $data = [];
        foreach ($reasons as $index => $reason) {
            $data[] = [
                'tenantId'   => 1,
                'serialNo'   => $index + 1,
                'label'      => $reason,
                'isActive'   => 1,
                'createdAt'  => timenow(),
                'createdBy'  => null,
                'updatedAt'  => null,
                'updatedBy'  => null,
            ];
        }

        // Insert data into idleReasons table
        $this->db->table('idleReasons')->insertBatch($data);

        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
