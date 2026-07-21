<?php

namespace Modules\Backend\OpMaster\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Libraries\UserPermissionLib;


class MachineMasterData14 extends Seeder
{
    public $priority = 26;

    public function run()
    {

        $seedName = static::class;
        $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
        if ($exists > 0) {
            return;
        }

        //add plc tags
        $this->db->query("INSERT INTO `plcTagMaster` (`tagId`, `tenantId`, `serialNo`, `plcId`, `tagName`, `tagAddress`, `dataType`, `registerType`, `readWrite`, `scaleFactor`, `offset`, `unit`, `description`, `isActive`, `createdAt`, `updatedAt`, `createdBy`, `updatedBy`) VALUES
                            (649, 1, 649, 1, 'OUT_FEED_DWN_SET_TIME', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.OUT_FEED_DWN_SET_TIME', 'Int16', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (650, 1, 650, 1, 'OUT_FEED_UP_SET_TIME', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.OUT_FEED_UP_SET_TIME', 'Int16', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3)
                            ;");

        $lastIndex = 649;

        $this->db->query("INSERT INTO `uiTagMaster`(`uiTagId`,`tenantId`, `serialNo`, `tagId`, `tagGroupId`, `tagName`, `isActive`, `updatedAt`, `updatedBy`, `createdAt`, `createdBy`) SELECT tagId,tenantId, serialNo, tagId, 1 as tagGroupId, tagName, 1 as isActive, updatedAt, updatedBy, createdAt, createdBy FROM plcTagMaster WHERE tagId >=$lastIndex;");


        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
