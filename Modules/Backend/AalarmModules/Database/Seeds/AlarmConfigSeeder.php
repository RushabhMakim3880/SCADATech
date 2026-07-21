<?php

namespace Modules\Backend\AalarmModules\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AlarmConfigSeeder extends Seeder
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
      ['uiTagId' => '17', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Chain feeder motor 1 tripped.'],
      ['uiTagId' => '355', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher servo fault.'],
      ['uiTagId' => '359', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 1 servo fault.'],
      ['uiTagId' => '364', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 2 servo fault.'],
      ['uiTagId' => '369', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 3 servo fault.'],
      ['uiTagId' => '374', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 4 servo fault.'],
      ['uiTagId' => '419', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Main hydraulic motor stopped due to no operation.'],
      ['uiTagId' => '420', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Return line high-pressure alarm.'],
      ['uiTagId' => '421', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Return line filter choked — please change the filter first.'],
      ['uiTagId' => '422', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Head lubrication time over — first fill the lubrication, then and only then the machine will start.'],
      ['uiTagId' => '423', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher lubrication time over — first fill the lubrication, then and only then the machine will start.'],
      ['uiTagId' => '424', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Oil temperature too high — the machine will start when the temperature falls below the high setpoint.'],
      ['uiTagId' => '425', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Main hydraulic motor did not start after receiving run command.'],
      ['uiTagId' => '426', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Oil circulation motor did not start after receiving run command.'],
      ['uiTagId' => '427', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Air cooler motor did not start after receiving run command.'],
      ['uiTagId' => '428', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Head lubrication motor did not start after receiving run command.'],
      ['uiTagId' => '429', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Chain feeder motor forward did not start after receiving run command.'],
      ['uiTagId' => '430', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Chain feeder motor reverse did not start after receiving run command.'],
      ['uiTagId' => '431', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher lubrication pump did not start after receiving run command.'],
      ['uiTagId' => '432', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher hydraulic motor did not start after receiving run command.'],
      ['uiTagId' => '433', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher up proxy signal not received within the ideal time.'],
      ['uiTagId' => '434', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher down proxy signal not received within the ideal time.'],
      ['uiTagId' => '435', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher clamp pressure transducer signal not received within the ideal time.'],
      ['uiTagId' => '436', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'In-feed 0° proxy 1 signal not received within the ideal time.'],
      ['uiTagId' => '437', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'In-feed 0° proxy 2 signal not received within the ideal time.'],
      ['uiTagId' => '438', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'In-feed 90° proxy signal not received within the ideal time.'],
      ['uiTagId' => '439', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'In-feed 90° pressure transducer signal not received within the ideal time.'],
      ['uiTagId' => '440', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Outfeed up sensor signal not received within the ideal time.'],
      ['uiTagId' => '441', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Marking body up sensor signal not received within the ideal time.'],
      ['uiTagId' => '442', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Marking body down sensor signal not received within the ideal time.'],
      ['uiTagId' => '443', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Marking casset up sensor signal not received within the ideal time.'],
      ['uiTagId' => '444', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Marking casset down sensor signal not received within the ideal time.'],
      ['uiTagId' => '445', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Marking cylinder up sensor signal not received within the ideal time.'],
      ['uiTagId' => '446', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Marking cylinder down sensor or pressure transducer signal not received within the ideal time.'],
      ['uiTagId' => '447', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Cutting cylinder up sensor not received within the ideal time.'],
      ['uiTagId' => '448', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Cutting cylinder down sensor not received within the ideal time.'],
      ['uiTagId' => '449', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Cutting hold down up sensor not received within the ideal time.'],
      ['uiTagId' => '450', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Cutting hold pressure switch is not received within the ideal time.'],
      ['uiTagId' => '451', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Hold down 1 up sensor is not received within the ideal time.'],
      ['uiTagId' => '452', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Hold down 2 up sensor is not received within the ideal time.'],
      ['uiTagId' => '453', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Hold down 3 up sensor is not received within the ideal time.'],
      ['uiTagId' => '454', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Hold down 4 up sensor is not received within the ideal time.'],
      ['uiTagId' => '455', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 1 down sensor is not received within the ideal time.'],
      ['uiTagId' => '456', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 1 up sensor is not received within the ideal time.'],
      ['uiTagId' => '457', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 2 down sensor is not received within the ideal time.'],
      ['uiTagId' => '458', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 2 up sensor is not received within the ideal time.'],
      ['uiTagId' => '459', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 3 down sensor is not received within the ideal time.'],
      ['uiTagId' => '460', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 3 up sensor is not received within the ideal time.'],
      ['uiTagId' => '461', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 4 down sensor is not received within the ideal time.'],
      ['uiTagId' => '462', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 4 up sensor is not received within the ideal time.'],
      ['uiTagId' => '463', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Edge finder clamp pressure switch is not received within the ideal time.'],
      ['uiTagId' => '464', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Edge finder declmap pressure switch is not received within the ideal time.'],
      ['uiTagId' => '465', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Operation type is not selected please select the operation type.'],
      ['uiTagId' => '466', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher is not safe please check  the condition which was not true.'],
      ['uiTagId' => '467', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 1 servo not take the position after the getting the run command.'],
      ['uiTagId' => '468', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 2 servo not take the position after the getting the run command.'],
      ['uiTagId' => '469', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 3 servo not take the position after the getting the run command.'],
      ['uiTagId' => '470', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 4 servo not take the position after the getting the run command.'],
      ['uiTagId' => '471', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher servo not take the position after the getting the run command.'],
      ['uiTagId' => '472', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Chain feeder proxy is sensed during the in feed 0 degree command.'],
      ['uiTagId' => '473', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'In feed 0 degree proxy 1 is not received during the command time.'],
      ['uiTagId' => '474', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'In feed 0 degree proxy 2 is not received during the command time.'],
      ['uiTagId' => '475', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Edge finder photo sensor is not sensed during the ideal time.'],
      ['uiTagId' => '476', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Edge finder servo did not take the home pos after the edge finder cycle complet'],
      ['uiTagId' => '477', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Edge finder servo did not take the position.'],
      ['uiTagId' => '478', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher servo not gone at the clamping pos.'],
      ['uiTagId' => '479', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'During Cutting cycle start the cut hold down pressure switch is arrived.'],
      ['uiTagId' => '480', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Casset 1 proxy is not received during the cycle time'],
      ['uiTagId' => '481', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Casset 2 proxy is not received during the cycle time'],
      ['uiTagId' => '482', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Casset 3 proxy is not received during the cycle time'],
      ['uiTagId' => '483', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Casset 4 proxy is not received during the cycle time'],
      ['uiTagId' => '484', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Angle reference photo sensor is not received during the ideal time'],
      ['uiTagId' => '485', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Angle slow photo sensor is not received during the ideal time.'],
      ['uiTagId' => '486', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 1 down proxy is sensed during the punch 1.'],
      ['uiTagId' => '487', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 2 down proxy is sensed during the punch 2.'],
      ['uiTagId' => '488', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 3 down proxy is sensed during the punch 3.'],
      ['uiTagId' => '489', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 4 down proxy is sensed during the punch 4.'],
      ['uiTagId' => '32', 'loloTheresold' => '-2', 'loTheresold' => '0', 'hiTheresold' => '2', 'hihiTheresold' => '5', 'message' => 'Operating panel emergency is pressed.'],
      ['uiTagId' => '33', 'loloTheresold' => '-2', 'loTheresold' => '0', 'hiTheresold' => '2', 'hihiTheresold' => '5', 'message' => 'Princher remote emergency is pressed.'],
      ['uiTagId' => '34', 'loloTheresold' => '-2', 'loTheresold' => '0', 'hiTheresold' => '2', 'hihiTheresold' => '5', 'message' => 'Outfeed remote emergency is pressed.'],
      ['uiTagId' => '13', 'loloTheresold' => '-2', 'loTheresold' => '0', 'hiTheresold' => '2', 'hihiTheresold' => '5', 'message' => 'Machine barrier is open please close the barrier or else machine will not work'],
      ['uiTagId' => '43', 'loloTheresold' => '-2', 'loTheresold' => '0', 'hiTheresold' => '2', 'hihiTheresold' => '5', 'message' => 'Princher machine-side hard limit reached.'],
      ['uiTagId' => '48', 'loloTheresold' => '-2', 'loTheresold' => '0', 'hiTheresold' => '2', 'hihiTheresold' => '5', 'message' => 'Princher reverse-side hard limit reached.'],
      ['uiTagId' => '44', 'loloTheresold' => '-2', 'loTheresold' => '0', 'hiTheresold' => '2', 'hihiTheresold' => '5', 'message' => 'Princher lubrication low.'],
      ['uiTagId' => '87', 'loloTheresold' => '-2', 'loTheresold' => '0', 'hiTheresold' => '2', 'hihiTheresold' => '5', 'message' => 'Machine lubrication low.']
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
