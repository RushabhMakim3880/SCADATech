<?php

namespace Modules\Backend\OpMaster\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MachineMasterData3 extends Seeder
{
    public $priority = 16;

    public function run()
    {

        $seedName = static::class;
        $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
        if ($exists > 0) {
            return;
        }

        $this->db = \Config\Database::connect();

        //add PLC Tags
        $this->db->query("INSERT INTO `plcTagMaster` (`tagId`, `tenantId`, `serialNo`, `plcId`, `tagName`, `tagAddress`, `dataType`, `registerType`, `readWrite`, `scaleFactor`, `offset`, `unit`, `description`, `isActive`, `createdAt`, `updatedAt`, `createdBy`, `updatedBy`) VALUES
                            (560, 1, 560, 1, 'AL_HEAD_NOT_HOME', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_HEAD_NOT_HOME', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (561, 1, 561, 1, 'I_PROXY_FWD_X1', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.I_PROXY_FWD_X1', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (562, 1, 562, 1, 'I_PROXY_REV_X1', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.I_PROXY_REV_X1', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (563, 1, 563, 1, 'M_EDGE_FINDER_CLAMP', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.M_EDGE_FINDER_CLAMP', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (564, 1, 564, 1, 'M_EDGE_FINDER_DECLAMP', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.M_EDGE_FINDER_DECLAMP', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (565, 1, 565, 1, 'M_PRINCHER_HYD_MOTOR_CMD', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.M_PRINCHER_HYD_MOTOR_CMD', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (566, 1, 566, 1, 'S_MARKING_SAFETY_STOP', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_MARKING_SAFETY_STOP', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (567, 1, 567, 1, 'S_X1_WEAR', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_X1_WEAR', 'Double', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (568, 1, 568, 1, 'X1_FINAL_AUTO_MM', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.X1_FINAL_AUTO_MM', 'Double', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (569, 1, 569, 1, 'X1_HOME_SAFE', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.X1_HOME_SAFE', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (570, 1, 570, 1, 'AL_M_6_METER_PHOTO_SENSOR_SENSED_DURING_IN_FEED_0_DEGREE', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_6_METER_PHOTO_SENSOR_SENSED_DURING_IN_FEED_0_DEGREE', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (571, 1, 571, 1, 'AL_M_ANGLE_REF_OR_SLOW_SENSOR_SENSED_DURING_IN_FEED_0_DEGREE', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_ANGLE_REF_OR_SLOW_SENSOR_SENSED_DURING_IN_FEED_0_DEGREE', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (572, 1, 572, 1, 'AL_M_CASSET_1_PROXY_IS_NOT_SENSED_DURING_MARKING_BODY_DOWN', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_CASSET_1_PROXY_IS_NOT_SENSED_DURING_MARKING_BODY_DOWN', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (573, 1, 573, 1, 'AL_M_CASSET_1_PROXY_IS_NOT_SENSED_DURING_MARKING_BODY_UP', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_CASSET_1_PROXY_IS_NOT_SENSED_DURING_MARKING_BODY_UP', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (574, 1, 574, 1, 'AL_M_CHAIN_FEEDER_PROXY_SENSED_DURING_CHAIN_FEEDER_FWD_CMD', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_CHAIN_FEEDER_PROXY_SENSED_DURING_CHAIN_FEEDER_FWD_CMD', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (575, 1, 575, 1, 'AL_M_CHAIN_FEEDER_PROXY_SENSED_DURING_IN_FEED_0_DEGREE', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_CHAIN_FEEDER_PROXY_SENSED_DURING_IN_FEED_0_DEGREE', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (576, 1, 576, 1, 'AL_M_EDGE_DECLAMP_PROXY_NOT_SENSED_DURING_IN_FEED_0_DEGREE', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_EDGE_DECLAMP_PROXY_NOT_SENSED_DURING_IN_FEED_0_DEGREE', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (577, 1, 577, 1, 'AL_M_EDGE_FINDER_CLAMPED_DURING_PRINCHER_UP', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_EDGE_FINDER_CLAMPED_DURING_PRINCHER_UP', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (578, 1, 578, 1, 'AL_M_EDGE_FINDER_PHOTO_SENSOR_SENSED_DURING_IN_FEED_0_DEGREE', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_EDGE_FINDER_PHOTO_SENSOR_SENSED_DURING_IN_FEED_0_DEGREE', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (579, 1, 579, 1, 'AL_M_IN_FEED_0_DEGREE_PROXY_ERROR_DURING_CHAIN_FFEDER_FWD', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_IN_FEED_0_DEGREE_PROXY_ERROR_DURING_CHAIN_FFEDER_FWD', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (580, 1, 580, 1, 'AL_M_IN_FEED_90_PRESSURE_NOT_OK_DURING_PRINHCER_DOWN', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_IN_FEED_90_PRESSURE_NOT_OK_DURING_PRINHCER_DOWN', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (581, 1, 581, 1, 'AL_M_IN_FEED_90_PROXY_NOT_SENSED_DURING_PRINCHER_DOWN', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_IN_FEED_90_PROXY_NOT_SENSED_DURING_PRINCHER_DOWN', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (582, 1, 582, 1, 'AL_M_MARKING_BODY_DOWN_PROXY_NOT_SENSED_DURING_CASSET_OPERATION', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_MARKING_BODY_DOWN_PROXY_NOT_SENSED_DURING_CASSET_OPERATION', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (583, 1, 583, 1, 'AL_M_MARKING_CYL_UP_PROXY_NOT_SENED_DURING_MARKING_BODY_DOWN', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_MARKING_CYL_UP_PROXY_NOT_SENED_DURING_MARKING_BODY_DOWN', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (584, 1, 584, 1, 'AL_M_MARKING_CYL_UP_PROXY_NOT_SENED_DURING_MARKING_BODY_UP', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_MARKING_CYL_UP_PROXY_NOT_SENED_DURING_MARKING_BODY_UP', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (585, 1, 585, 1, 'AL_M_PRINCHER_DOWN_PROXY_NOT_SENSED_DURING_PRINCHER_CLAMP', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_PRINCHER_DOWN_PROXY_NOT_SENSED_DURING_PRINCHER_CLAMP', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (586, 1, 586, 1, 'AL_M_PRINCHER_INSIDE_BODY_DURING_CASSSET_OPERATION', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_PRINCHER_INSIDE_BODY_DURING_CASSSET_OPERATION', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (587, 1, 587, 1, 'AL_M_PRINCHER_IS_CLAMPED_DURING_PRINCHER_DOWN', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_PRINCHER_IS_CLAMPED_DURING_PRINCHER_DOWN', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (588, 1, 588, 1, 'AL_M_PRINCHER_IS_CLAMPED_DURING_PRINCHER_UP', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_PRINCHER_IS_CLAMPED_DURING_PRINCHER_UP', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (589, 1, 589, 1, 'AL_M_PRINCHER_IS_INSIDE_MARKING_DURING_MARKING_BODY_DOWN', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_PRINCHER_IS_INSIDE_MARKING_DURING_MARKING_BODY_DOWN', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (590, 1, 590, 1, 'AL_M_PRINCHER_IS_INSIDE_MARKING_DURING_MARKING_BODY_UP', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_PRINCHER_IS_INSIDE_MARKING_DURING_MARKING_BODY_UP', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (591, 1, 591, 1, 'AL_M_PRINCHER_UP_PROXY_NOT_SENSED_DURING_IN_FEED_90', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_PRINCHER_UP_PROXY_NOT_SENSED_DURING_IN_FEED_90', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (592, 1, 592, 1, 'AL_M_PRNCHER_DOWN_PROXY_NOT_SENSED_DURING_PRINCHER_DECLAMP', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_PRNCHER_DOWN_PROXY_NOT_SENSED_DURING_PRINCHER_DECLAMP', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (593, 1, 593, 1, 'AL_MACHINE_NOT_HEALTHY', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_MACHINE_NOT_HEALTHY', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (594, 1, 594, 1, 'AL_MANUAL_OPEARATION_NOT_SELECTED', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_MANUAL_OPEARATION_NOT_SELECTED', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (595, 1, 595, 1, 'AL_NO_BAR_AT_LOADER', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_NO_BAR_AT_LOADER', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (596, 1, 596, 1, 'AL_PLEASE_PUT_MACHINE_IN_MANUAL_MODE', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_PLEASE_PUT_MACHINE_IN_MANUAL_MODE', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (597, 1, 597, 1, 'AL_M_MARKING_BODY_IS_NOT_DOWN_DURING_CYLINDER_OPERATION', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_MARKING_BODY_IS_NOT_DOWN_DURING_CYLINDER_OPERATION', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (598, 1, 598, 1, 'AL_M_NO_CASSET_PROXY_SENSED_DURING_CASSET_UP_DOWN', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_NO_CASSET_PROXY_SENSED_DURING_CASSET_UP_DOWN', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (599, 1, 599, 1, 'AL_M_NO_CASSET_PROXY_SENSED_DURING_CYLINDER_OPERATION', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_NO_CASSET_PROXY_SENSED_DURING_CYLINDER_OPERATION', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (600, 1, 600, 1, 'AL_M_PRICHER_AT_PUNCH_1_DURING_PUNCH_1_OPERATION', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_PRICHER_AT_PUNCH_1_DURING_PUNCH_1_OPERATION', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (601, 1, 601, 1, 'AL_M_PRINCHER_AT_PUNCH_2_DURING_PUNCH_2_OPEARTION', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_PRINCHER_AT_PUNCH_2_DURING_PUNCH_2_OPEARTION', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (602, 1, 602, 1, 'AL_M_PRINCHER_AT_PUNCH_3_DURING_PUNCH_3_OPERATION', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_PRINCHER_AT_PUNCH_3_DURING_PUNCH_3_OPERATION', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (603, 1, 603, 1, 'AL_M_PRINCHER_AT_PUNCH_4_DURNG_PUNCH_4_OPERTION', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_PRINCHER_AT_PUNCH_4_DURNG_PUNCH_4_OPERTION', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (604, 1, 604, 1, 'AL_M_PRINCHER_IS_INSIDE_MARKING_DURING_CYLINDER_OPERATION', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_PRINCHER_IS_INSIDE_MARKING_DURING_CYLINDER_OPERATION', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (605, 1, 605, 1, 'AL_M_PRINCHER_IS_NOT_AT_ZERO_POS_DURING_EDGE_FINDER_CLAMP_OPERATION', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_M_PRINCHER_IS_NOT_AT_ZERO_POS_DURING_EDGE_FINDER_CLAMP_OPERATION', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (606, 1, 606, 1, 'I_PS_CUT_HOLD_D_1', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.I_PS_CUT_HOLD_D_1', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (607, 1, 607, 1, 'S_6_METER_DISTANCE', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_6_METER_DISTANCE', 'Double', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3)

                            ;");

        $lastIndex = 560;

        $this->db->query("INSERT INTO `uiTagMaster`(`uiTagId`,`tenantId`, `serialNo`, `tagId`, `tagGroupId`, `tagName`, `isActive`, `updatedAt`, `updatedBy`, `createdAt`, `createdBy`) SELECT tagId,tenantId, serialNo, tagId, 1 as tagGroupId, tagName, 1 as isActive, updatedAt, updatedBy, createdAt, createdBy FROM plcTagMaster WHERE tagId >=$lastIndex;");

        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
