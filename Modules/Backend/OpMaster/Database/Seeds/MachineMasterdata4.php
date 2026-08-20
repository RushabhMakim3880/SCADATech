<?php

namespace Modules\Backend\OpMaster\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MachineMasterdata4 extends Seeder
{
    public $priority = 15;

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
        (586, 1, 586, 1, 'S_DA1_PUNCH_COUNT', 'ns=4;s=|var|Inovance-ARM-Linux.Application.RADHE.S_DA1_PUNCH_COUNT', 'Float', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2026-07-27 16:07:36', '2026-07-27 16:07:36', 2, 2),
        (587, 1, 587, 1, 'S_DA2_PUNCH_COUNT', 'ns=4;s=|var|Inovance-ARM-Linux.Application.RADHE.S_DA2_PUNCH_COUNT', 'Float', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2026-07-27 16:07:36', '2026-07-27 16:07:36', 2, 2),
        (588, 1, 588, 1, 'S_DA3_PUNCH_COUNT', 'ns=4;s=|var|Inovance-ARM-Linux.Application.RADHE.S_DA3_PUNCH_COUNT', 'Float', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2026-07-27 16:07:36', '2026-07-27 16:07:36', 2, 2),
        (589, 1, 589, 1, 'S_DB1_PUNCH_COUNT', 'ns=4;s=|var|Inovance-ARM-Linux.Application.RADHE.S_DB1_PUNCH_COUNT', 'Float', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2026-07-27 16:07:36', '2026-07-27 16:07:36', 2, 2),
        (590, 1, 590, 1, 'S_DB2_PUNCH_COUNT', 'ns=4;s=|var|Inovance-ARM-Linux.Application.RADHE.S_DB2_PUNCH_COUNT', 'Float', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2026-07-27 16:07:36', '2026-07-27 16:07:36', 2, 2),
        (591, 1, 591, 1, 'S_DB3_PUNCH_COUNT', 'ns=4;s=|var|Inovance-ARM-Linux.Application.RADHE.S_DB3_PUNCH_COUNT', 'Float', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2026-07-27 16:07:36', '2026-07-27 16:07:36', 2, 2);");

        $lastIndex = 586;

        $this->db->query("INSERT INTO `uiTagMaster`(`uiTagId`,`tenantId`, `serialNo`, `tagId`, `tagGroupId`, `tagName`, `isActive`, `updatedAt`, `updatedBy`, `createdAt`, `createdBy`) SELECT tagId,tenantId, serialNo, tagId, 1 as tagGroupId, tagName, 1 as isActive, updatedAt, updatedBy, createdAt, createdBy FROM plcTagMaster WHERE tagId >=$lastIndex;");

        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
