<?php

namespace Modules\Backend\OpMaster\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MachineMasterData9 extends Seeder
{
    public $priority = 22;

    public function run()
    {

        $seedName = static::class;
        $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
        if ($exists > 0) {
            return;
        }

        $this->db = \Config\Database::connect();

        //fix data types
        // $tagLists = [406];
        // $this->db->query("UPDATE `plcTagMaster` SET dataType = 'Int16' WHERE tagId IN (" . implode(",", $tagLists) . ")");

        //add PLC Tags
        $this->db->query("INSERT INTO `plcTagMaster` (`tagId`, `tenantId`, `serialNo`, `plcId`, `tagName`, `tagAddress`, `dataType`, `registerType`, `readWrite`, `scaleFactor`, `offset`, `unit`, `description`, `isActive`, `createdAt`, `updatedAt`, `createdBy`, `updatedBy`) VALUES
                            (642, 1, 642, 1, 'IN_PASSWORD', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.IN_PASSWORD', 'Int16', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (643, 1, 643, 1, 'S_SYSTEM_FAULT', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.L_PASSWORD_1', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3)
                            ;");

        $lastIndex = 642;

        $this->db->query("INSERT INTO `uiTagMaster`(`uiTagId`,`tenantId`, `serialNo`, `tagId`, `tagGroupId`, `tagName`, `isActive`, `updatedAt`, `updatedBy`, `createdAt`, `createdBy`) SELECT tagId,tenantId, serialNo, tagId, 1 as tagGroupId, tagName, 1 as isActive, updatedAt, updatedBy, createdAt, createdBy FROM plcTagMaster WHERE tagId >=$lastIndex;");

        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
