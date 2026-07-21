<?php

namespace Modules\Backend\AalarmModules\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AlarmConfigSeeder6 extends Seeder
{
    public $priority = 105;

    public function run()
    {

        $seedName = static::class;
        $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
        if ($exists > 0) {
            return;
        }

        $tenantId = 1;
        $serialNo = 1;


        $data = [
            ['uiTagId' => '644', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Head 1 Servo Torque Reached'],
            ['uiTagId' => '645', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Head 2 Servo Torque Reached'],
            ['uiTagId' => '646', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Head 3 Servo Torque Reached'],
            ['uiTagId' => '647', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Head 4 Servo Torque Reached']
        ];

        foreach ($data as $row) {
            $row['tenantId'] = $tenantId;
            $row['serialNo'] = 0;
            $row['isActive'] = 1;
            $row['createdAt'] = date('Y-m-d H:i:s');
            $row['updatedAt'] = date('Y-m-d H:i:s');
            $this->db->table('AlarmConfig')->insert($row);

            $alarmId = $this->db->insertID();
            assignSerialNumber($tenantId, "AlarmConfig", "alarmId", $alarmId);
        }

        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
