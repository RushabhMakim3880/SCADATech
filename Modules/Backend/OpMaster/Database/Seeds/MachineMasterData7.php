<?php

namespace Modules\Backend\OpMaster\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MachineMasterData7 extends Seeder
{
    public $priority = 20;

    public function run()
    {

        $seedName = static::class;
        $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
        if ($exists > 0) {
            return;
        }

        $this->db = \Config\Database::connect();

        //fix data types
        $tagLists = [401, 402, 403, 404, 351];
        $this->db->query("UPDATE `plcTagMaster` SET dataType = 'Int16' WHERE tagId IN (" . implode(",", $tagLists) . ")");

        //add PLC Tags
        $this->db->query("INSERT INTO `plcTagMaster` (`tagId`, `tenantId`, `serialNo`, `plcId`, `tagName`, `tagAddress`, `dataType`, `registerType`, `readWrite`, `scaleFactor`, `offset`, `unit`, `description`, `isActive`, `createdAt`, `updatedAt`, `createdBy`, `updatedBy`) VALUES
                            (615, 1, 615, 1, 'S_ACCUMILATOR_TIME_FOR_PRESSURE', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_ACCUMILATOR_TIME_FOR_PRESSURE', 'Double', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (616, 1, 616, 1, 'S_PRINCHER_CLAMP_TIME_FOR_PRESSURE', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_PRINCHER_CLAMP_TIME_FOR_PRESSURE', 'Double', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (617, 1, 617, 1, 'S_MARKING_TIME_FOR_PRESSURE', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_MARKING_TIME_FOR_PRESSURE', 'Double', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (618, 1, 618, 1, 'S_IN_FEED_90_TIME_FOR_PRESSURE', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_IN_FEED_90_TIME_FOR_PRESSURE', 'Double', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (619, 1, 619, 1, 'T_RY_Voltage', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.T_RY_Voltage', 'Float', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (620, 1, 620, 1, 'T_YB_Voltage', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_MACHINE_IDEAL', 'Float', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),

                            (621, 1, 621, 1, 'T_BR_Voltage', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.T_BR_Voltage', 'Float', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (622, 1, 622, 1, 'T_R_Amp', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.T_R_Amp', 'Float', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (623, 1, 623, 1, 'T_Y_Amp', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.T_Y_Amp', 'Float', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (624, 1, 624, 1, 'T_B_Amp', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.T_B_Amp', 'Float', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                           
                            (625, 1, 625, 1, 'L_DAY_1', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.L_DAY_1', 'Int16', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (626, 1, 626, 1, 'L_MONTH_1', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.L_MONTH_1', 'Int16', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (627, 1, 627, 1, 'L_YEAR_1', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.L_YEAR_1', 'Int16', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (628, 1, 628, 1, 'L_DAY_2', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.L_DAY_2', 'Int16', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (629, 1, 629, 1, 'L_MONTH_2', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.L_MONTH_2', 'Int16', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (630, 1, 630, 1, 'L_YEAR_2', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.L_YEAR_2', 'Int16', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (631, 1, 631, 1, 'L_DAY_3', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.L_DAY_3', 'Int16', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (632, 1, 632, 1, 'L_MONTH_3', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.L_MONTH_3', 'Int16', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (633, 1, 633, 1, 'L_YEAR_3', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.L_YEAR_3', 'Int16', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (634, 1, 634, 1, 'L_DAY_4', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.L_DAY_4', 'Int16', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (635, 1, 635, 1, 'L_MONTH_4', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.L_MONTH_4', 'Int16', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (636, 1, 636, 1, 'L_YEAR_4', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.L_YEAR_4', 'Int16', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            
                            (637, 1, 637, 1, 'L_ENABLE1', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.L_ENABLE1', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (638, 1, 638, 1, 'L_ENABLE2', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.L_ENABLE2', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (639, 1, 639, 1, 'L_ENABLE3', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.L_ENABLE3', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (640, 1, 640, 1, 'L_ENABLE4', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.L_ENABLE4', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),

                            (641, 1, 641, 1, 'AL_PRINCHER_STRIAK', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.L_ENABLE4', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3)


                            ;");

        $lastIndex = 615;

        $this->db->query("INSERT INTO `uiTagMaster`(`uiTagId`,`tenantId`, `serialNo`, `tagId`, `tagGroupId`, `tagName`, `isActive`, `updatedAt`, `updatedBy`, `createdAt`, `createdBy`) SELECT tagId,tenantId, serialNo, tagId, 1 as tagGroupId, tagName, 1 as isActive, updatedAt, updatedBy, createdAt, createdBy FROM plcTagMaster WHERE tagId >=$lastIndex;");

        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
