<?php

namespace Modules\Backend\AalarmModules\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AlarmConfigSeeder2 extends Seeder
{
    public $priority = 100;

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
            ['uiTagId' => '49', 'loloTheresold' => '-2', 'loTheresold' => '0', 'hiTheresold' => '2', 'hihiTheresold' => '5', 'message' => 'Princher is collided in the machine please check the prinher strick proxy'],
            ['uiTagId' => '560', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Machine is not in home psition during the cycle start condition, please presse the all home command'],
            ['uiTagId' => '595', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'No bar is available at the loader during auto loading'],
            ['uiTagId' => '593', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Machine is not healthy please check the machine healthy condition before opeartion'],
            ['uiTagId' => '594', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Please select the manual operation from the software before operating'],
            ['uiTagId' => '596', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'If you want to operate the machine In the manual mode than put machine in manual mode first'],
            ['uiTagId' => '579', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'In feed is not in position for loading the angle into in feed'],
            ['uiTagId' => '574', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Chain feeder proxy is sensed that\'s why chain feeder is not working'],
            ['uiTagId' => '577', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Edge finder is clamped that\'s why princher is not going up'],
            ['uiTagId' => '588', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher is clmaped that\'s why princher is not going up'],
            ['uiTagId' => '580', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'In feed 90 pressure is achieved that\'s why princher is not going down'],
            ['uiTagId' => '581', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'In feed 90 proxy is arrived that\'s why princher is not going down'],
            ['uiTagId' => '587', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher is clmaped or princher clamp pressure is achieved that\'s why princher is not going down'],
            ['uiTagId' => '585', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher is not down or proxy is not received that\'s why princher clamp is not working'],
            ['uiTagId' => '592', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher is not down or proxy is not received that\'s why princher declamp is not working'],
            ['uiTagId' => '575', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Chain feeder proxy is received during that\'s why in feed 0 is not working'],
            ['uiTagId' => '571', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Angle ref of angle slow sensor is received that\'s why in feed 0 is not working'],
            ['uiTagId' => '570', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => '6 meter photo sensor is received that\'s why in feed 0 is not working'],
            ['uiTagId' => '576', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Edge finder declamp sensor not received that\'s why in feed 0 is not working'],
            ['uiTagId' => '578', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Edge finder photo sensor receicved that\'s why in feed o is not working'],
            ['uiTagId' => '591', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher up proxy not received that\'s why in feed 90 is not working'],
            ['uiTagId' => '590', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher is inside the marking that\'s why marking body is not working'],
            ['uiTagId' => '573', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Casset 1 proxy is not received that\'s why the marking body is not working'],
            ['uiTagId' => '584', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Marking cylinder up sensor not received that\'s why the marking body is not working'],
            ['uiTagId' => '589', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher is inside the marking that\'s why making body is not working'],
            ['uiTagId' => '583', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Marking cylinder up proxy not received that\'s why markig body is not working'],
            ['uiTagId' => '572', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Casset 1 proxy not received that;s why marking body is not working'],
            ['uiTagId' => '586', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher is inside the marking that\'s why casset operation is not working'],
            ['uiTagId' => '582', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Marking body down not received that\'s why the casset operation is not working'],
            ['uiTagId' => '598', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'No any casset proxy is received that\'s why the casset operation is not working'],
            ['uiTagId' => '604', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher is inside marking that\'s why the marking cylinder is not working'],
            ['uiTagId' => '599', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'No any casset proxy is received that\'s why the marking cylinder is not working'],
            ['uiTagId' => '597', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Marking body down sensor not received that’s why cylinder is not woring'],
            ['uiTagId' => '600', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher is inside punch 1 body that\'s why punch 1 is not working'],
            ['uiTagId' => '601', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher is inside punch 2 body that\'s why punch 2 is not working'],
            ['uiTagId' => '602', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher is inside punch 3 body that\'s why punch 3 is not working'],
            ['uiTagId' => '603', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher is inside punch 4 body that\'s why punch 4 is not working'],
            ['uiTagId' => '605', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher is not at zero position that\'s why edge finder clamp is not working'],
            ['uiTagId' => '19', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Chain feeder 2 motor tripped'],
            ['uiTagId' => '23', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Head lubrication motor tripped.'],
            ['uiTagId' => '29', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Outfeed motor tripped.'],
            ['uiTagId' => '27', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Oil circulation motor tripped.'],
            ['uiTagId' => '509', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher hydraulic motor tripped.'],
            ['uiTagId' => '46', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher lubrication motor tripped.'],
            ['uiTagId' => '389', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Edge finder servo has a error']
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
