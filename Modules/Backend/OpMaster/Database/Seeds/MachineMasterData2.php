<?php

namespace Modules\Backend\OpMaster\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MachineMasterData2 extends Seeder
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
(579, 1, 579, 1, 'S_MARK1', 'ns=4;s=|var|Inovance-ARM-Linux.Application.RADHE.S_MARK1', 'String', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2026-07-27 16:07:36', '2026-07-27 16:07:36', 2, 2),
(580, 1, 580, 1, 'S_MARK2', 'ns=4;s=|var|Inovance-ARM-Linux.Application.RADHE.S_MARK2', 'String', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2026-07-27 16:07:36', '2026-07-27 16:07:36', 2, 2),
(581, 1, 581, 1, 'S_MARK3', 'ns=4;s=|var|Inovance-ARM-Linux.Application.RADHE.S_MARK3', 'String', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2026-07-27 16:07:36', '2026-07-27 16:07:36', 2, 2),
(582, 1, 582, 1, 'S_MARK4', 'ns=4;s=|var|Inovance-ARM-Linux.Application.RADHE.S_MARK4', 'String', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2026-07-27 16:07:36', '2026-07-27 16:07:36', 2, 2)
                            ;");

        $lastIndex = 579;

        $this->db->query("INSERT INTO `uiTagMaster`(`uiTagId`,`tenantId`, `serialNo`, `tagId`, `tagGroupId`, `tagName`, `isActive`, `updatedAt`, `updatedBy`, `createdAt`, `createdBy`) SELECT tagId,tenantId, serialNo, tagId, 1 as tagGroupId, tagName, 1 as isActive, updatedAt, updatedBy, createdAt, createdBy FROM plcTagMaster WHERE tagId >=$lastIndex;");

        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
