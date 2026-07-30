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
            ['uiTagId' => '21', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Main Panel Emergency Stop is pressed.', 'solution' => 'Release the red Emergency button on the main panel by twisting it, then Reset.'],
            ['uiTagId' => '23', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Remote Emergency Stop is pressed.', 'solution' => 'Check and release the Emergency Stop button on the Princher remote.'],
            ['uiTagId' => '22', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Remote Emergency Stop is pressed.', 'solution' => 'Check and release the Emergency Stop button on the Outfeed remote.'],
            ['uiTagId' => '56', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Main Power Phase fault or Voltage drop.', 'solution' => 'Check the R-Y-B phases of the main power supply. Look for a red light on the GIC relay.'],
            ['uiTagId' => '11', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Safety Barrier sensor is blocked.', 'solution' => 'Clear any objects or people away from the safety sensor and clean it.'],
            ['uiTagId' => '69', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher strike sensor is active.', 'solution' => 'Check if the princher has struck an obstacle or material.'],
            ['uiTagId' => '42', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Machine is in Auto. Please select Manual mode.', 'solution' => 'Press the \'Manual\' mode button on the ALARM HISTORY screen to proceed.'],
            ['uiTagId' => '62', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher Forward Limit crossed (Overtravel).', 'solution' => 'Put machine in Manual mode and move princher backward to free the limit switch.'],
            ['uiTagId' => '68', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher Reverse Limit crossed (Overtravel).', 'solution' => 'Put machine in Manual mode and move princher forward to free the limit switch.'],
            ['uiTagId' => '59', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Please switch to Manual mode.', 'solution' => 'Select Manual mode on the control panel to continue this operation.'],
            ['uiTagId' => '58', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Please switch to Aanual mode.', 'solution' => 'Select Auto mode on the control panel to continue this operation.'],
            ['uiTagId' => '50', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher stopped at Punch 1 for safety.', 'solution' => 'Check Punch 1 area for obstructions or clearance issues.'],
            ['uiTagId' => '27', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Hold 1 UP completed, but command stuck.', 'solution' => 'Check hydraulic valve coil or relay for Hold 1 in the panel.'],
            ['uiTagId' => '73', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 1 UP completed, but command stuck.', 'solution' => 'Check hydraulic valve coil or relay for Punch 1 in the panel.'],
            ['uiTagId' => '51', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher stopped at Punch 2 for safety.', 'solution' => 'Check Punch 2 area for obstructions or clearance issues.'],
            ['uiTagId' => '28', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Hold 2 UP completed, but command stuck.', 'solution' => 'Check hydraulic valve coil or relay for Hold 2 in the panel.'],
            ['uiTagId' => '75', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 2 UP completed, but command stuck.', 'solution' => 'Check hydraulic valve coil or relay for Punch 2 in the panel.'],
            ['uiTagId' => '52', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher stopped at Punch 3 for safety.', 'solution' => 'Check Punch 3 area for obstructions or clearance issues.'],
            ['uiTagId' => '29', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Hold 3 UP completed, but command stuck.', 'solution' => 'Check hydraulic valve coil or relay for Hold 3 in the panel.'],
            ['uiTagId' => '77', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 3 UP completed, but command stuck.', 'solution' => 'Check hydraulic valve coil or relay for Punch 3 in the panel.'],
            ['uiTagId' => '53', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher stopped at Punch 4 for safety.', 'solution' => 'Check Punch 4 area for obstructions or clearance issues.'],
            ['uiTagId' => '30', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Hold 4 UP completed, but command stuck.', 'solution' => 'Check hydraulic valve coil or relay for Hold 4 in the panel.'],
            ['uiTagId' => '79', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 4 UP completed, but command stuck.', 'solution' => 'Check hydraulic valve coil or relay for Punch 4 in the panel.'],
            ['uiTagId' => '54', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher stopped at Punch 5 for safety.', 'solution' => 'Check Punch 5 area for obstructions or clearance issues.'],
            ['uiTagId' => '31', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Hold 5 UP completed, but command stuck.', 'solution' => 'Check hydraulic valve coil or relay for Hold 5 in the panel.'],
            ['uiTagId' => '81', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 5 UP completed, but command stuck.', 'solution' => 'Check hydraulic valve coil or relay for Punch 5 in the panel.'],
            ['uiTagId' => '55', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher stopped at Punch 6 for safety.', 'solution' => 'Check Punch 6 area for obstructions or clearance issues.'],
            ['uiTagId' => '32', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Hold 6 UP completed, but command stuck.', 'solution' => 'Check hydraulic valve coil or relay for Hold 6 in the panel.'],
            ['uiTagId' => '83', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 6 UP completed, but command stuck.', 'solution' => 'Check hydraulic valve coil or relay for Punch 6 in the panel.'],
            ['uiTagId' => '45', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Marking Body UP completed, but command stuck.', 'solution' => 'Check hydraulic valve coil or relay for Marking Body in the panel.'],
            ['uiTagId' => '47', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher stopped at Marking for safety.', 'solution' => 'Check Marking area for obstructions.'],
            ['uiTagId' => '44', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Marking Body DOWN completed, command stuck.', 'solution' => 'Check hydraulic valve coil or relay for Marking Body in the panel.'],
            ['uiTagId' => '39', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Marking Cylinder UP sensor fault.', 'solution' => 'The marking cylinder is up, but there is no signal. Check sensor.'],
            ['uiTagId' => '38', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Marking Cassette sensor is not detecting.', 'solution' => 'Cassette is not locked properly, or dirt is blocking the sensor.'],
            ['uiTagId' => '46', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'No Cassette detected at Marking.', 'solution' => 'Insert and lock the marking cassette properly.'],
            ['uiTagId' => '43', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Marking Body Down sensor fault.', 'solution' => 'Marking body is down but sensor is not detecting. Clean or adjust.'],
            ['uiTagId' => '70', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher is clamped, cannot go UP!', 'solution' => 'Unclamp the material first. Princher can only go up when unclamped.'],
            ['uiTagId' => '37', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Infeed at 90-degree position not detected.', 'solution' => 'Check material alignment and 90-degree sensor.'],
            ['uiTagId' => '20', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Cut Hold UP completed, but command stuck.', 'solution' => 'Check hydraulic valve coil or relay for Cut Hold in the panel.'],
            ['uiTagId' => '19', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Cut Cylinder UP completed, but command stuck.', 'solution' => 'Check hydraulic valve coil or relay for Cut Cylinder in the panel.'],
            ['uiTagId' => '17', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Cut Cylinder DOWN completed, command stuck.', 'solution' => 'Check hydraulic valve coil or relay for Cut Cylinder in the panel.'],
            ['uiTagId' => '18', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Cutting operation stopped for safety.', 'solution' => 'Check cutting area for jamming or open guards.'],
            ['uiTagId' => '57', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Material is stuck at the 6-Meter Sensor.', 'solution' => 'Clear the jammed angle (material) and clean the photo sensor.'],
            ['uiTagId' => '71', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher UP sensor is not working.', 'solution' => 'Princher is fully up, but sensor light isn\'t on. Clean or adjust gap.'],
            ['uiTagId' => '13', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Chain Feeder Motor 1 has tripped.', 'solution' => 'Reset MPCB/Overload Relay in the panel. Check if material is stuck.'],
            ['uiTagId' => '14', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Chain Feeder Motor 2 has tripped.', 'solution' => 'Reset MPCB/Overload Relay in the panel. Check if material is stuck.'],
            ['uiTagId' => '36', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Infeed at 0-degree position not detected.', 'solution' => 'Check material alignment and 0-degree sensor.'],
            ['uiTagId' => '16', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Panel Cooling Fan has stopped (Tripped).', 'solution' => 'Check fan\'s MCB. Panel might be overheating.'],
            ['uiTagId' => '48', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Oil Circulation Pump has tripped.', 'solution' => 'Check wiring and overload relay for the oil pump near hydraulic tank.'],
            ['uiTagId' => '66', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher Lubrication pump fault.', 'solution' => 'The oil pump has tripped. Reset it from the electrical panel.'],
            ['uiTagId' => '25', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Head Lubrication pump fault.', 'solution' => 'The oil pump has tripped. Reset it from the electrical panel.'],
            ['uiTagId' => '92', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Tower Light RED is active.', 'solution' => 'Machine has a critical fault. Check other alarms on ALARM HISTORY.'],
            ['uiTagId' => '7', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Machine is not at \'Home\' (Zero) position.', 'solution' => 'Perform a \'Home\' operation for all axes first, then start Auto cycle.'],
            ['uiTagId' => '67', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher is in an unsafe position.', 'solution' => 'Move princher to a safe home position manually.'],
            ['uiTagId' => '72', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 1 is stuck in the Down position.', 'solution' => 'Punch did not return to the top. Check if die/punch is jammed.'],
            ['uiTagId' => '74', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 2 is stuck in the Down position.', 'solution' => 'Punch did not return to the top. Check if die/punch is jammed.'],
            ['uiTagId' => '76', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 3 is stuck in the Down position.', 'solution' => 'Punch did not return to the top. Check if die/punch is jammed.'],
            ['uiTagId' => '78', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 4 is stuck in the Down position.', 'solution' => 'Punch did not return to the top. Check if die/punch is jammed.'],
            ['uiTagId' => '80', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 5 is stuck in the Down position.', 'solution' => 'Punch did not return to the top. Check if die/punch is jammed.'],
            ['uiTagId' => '82', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Punch 6 is stuck in the Down position.', 'solution' => 'Punch did not return to the top. Check if die/punch is jammed.'],
            ['uiTagId' => '91', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Safety door is open.', 'solution' => 'Close the machine\'s safety door properly.'],
            ['uiTagId' => '33', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Machine turned off due to idle time.', 'solution' => 'Restart cycle. Machine was inactive for too long.'],
            ['uiTagId' => '26', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Oil Temperature is too high!', 'solution' => 'Check if chiller/heat exchanger is running. Stop machine to let it cool.'],
            ['uiTagId' => '49', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Machine Auto-Off due to high oil heat.', 'solution' => 'Let the hydraulic system cool down. Check thermostat sensor.'],
            ['uiTagId' => '24', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Low oil level in Head. Machine turned off.', 'solution' => 'Refill oil in the lubrication tank, run pump manually, remove air.'],
            ['uiTagId' => '64', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Low oil level in Princher. Machine off.', 'solution' => 'Refill oil in the lubrication tank, run pump manually, remove air.'],
            ['uiTagId' => '34', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Infeed 0-degree Sensor 1 Error.', 'solution' => 'Check infeed area sensor 1 for damage or misalignment.'],
            ['uiTagId' => '35', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Infeed 0-degree Sensor 2 Error.', 'solution' => 'Check infeed area sensor 2 for damage or misalignment.'],
            ['uiTagId' => '15', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Chain Feeder Sensor Error.', 'solution' => 'Check chain feeder sensor for blockages or dirt.'],
            ['uiTagId' => '9', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Angle positioning sensor error.', 'solution' => 'Check slow-down or reference photo sensors on the track.'],
            ['uiTagId' => '6', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Material is stuck at the 6-Meter Sensor.', 'solution' => 'Clear the jammed angle (material) and clean the photo sensor.'],
            ['uiTagId' => '60', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher DOWN sensor is not working.', 'solution' => 'Princher is fully down, but sensor light isn\'t on. Clean or adjust gap.'],
            ['uiTagId' => '61', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Princher DOWN sensor is not working.', 'solution' => 'Princher is fully down, but sensor light isn\'t on. Clean or adjust gap.'],
            ['uiTagId' => '10', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Angle slow-down photo sensor error.', 'solution' => 'Check and clean the slow-down photo sensor on the track.'],
            ['uiTagId' => '8', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Angle reference photo sensor error.', 'solution' => 'Check and clean the reference photo sensor on the track.'],
            ['uiTagId' => '84', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Connection to Servo Drive X is lost.', 'solution' => 'Check power to servo drive. Ensure EtherCAT/Comm cable is tight.'],
            ['uiTagId' => '85', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Connection to Servo Drive Y1 is lost.', 'solution' => 'Check power to servo drive. Ensure EtherCAT/Comm cable is tight.'],
            ['uiTagId' => '86', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Connection to Servo Drive Y2 is lost.', 'solution' => 'Check power to servo drive. Ensure EtherCAT/Comm cable is tight.'],
            ['uiTagId' => '87', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Connection to Servo Drive Y3 is lost.', 'solution' => 'Check power to servo drive. Ensure EtherCAT/Comm cable is tight.'],
            ['uiTagId' => '88', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Connection to Servo Drive Y4 is lost.', 'solution' => 'Check power to servo drive. Ensure EtherCAT/Comm cable is tight.'],
            ['uiTagId' => '89', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Connection to Servo Drive Y5 is lost.', 'solution' => 'Check power to servo drive. Ensure EtherCAT/Comm cable is tight.'],
            ['uiTagId' => '90', 'loloTheresold' => '-2', 'loTheresold' => '-1', 'hiTheresold' => '1', 'hihiTheresold' => '5', 'message' => 'Connection to Servo Drive Y6 is lost.', 'solution' => 'Check power to servo drive. Ensure EtherCAT/Comm cable is tight.']
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
