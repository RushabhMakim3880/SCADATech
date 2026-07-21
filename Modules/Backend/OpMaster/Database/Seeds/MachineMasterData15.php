<?php

namespace Modules\Backend\OpMaster\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Libraries\UserPermissionLib;


class MachineMasterData15 extends Seeder
{
    public $priority = 27;

    public function run()
    {

        $seedName = static::class;
        $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
        if ($exists > 0) {
            return;
        }

        //add plc tags
        $this->db->query("INSERT INTO `plcTagMaster` (`tagId`, `tenantId`, `serialNo`, `plcId`, `tagName`, `tagAddress`, `dataType`, `registerType`, `readWrite`, `scaleFactor`, `offset`, `unit`, `description`, `isActive`, `createdAt`, `updatedAt`, `createdBy`, `updatedBy`) VALUES
                            (651, 1, 651, 1, 'S_Y1_BYPASS', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_Y1_BYPASS', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (652, 1, 652, 1, 'S_Y2_BYPASS', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_Y2_BYPASS', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (653, 1, 653, 1, 'S_Y3_BYPASS', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_Y3_BYPASS', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (654, 1, 654, 1, 'S_Y4_BYPASS', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_Y4_BYPASS', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (655, 1, 655, 1, 'S_X1_BYPASS', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_X1_BYPASS', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3)
                            ;");

        $lastIndex = 651;
        $this->db->query("INSERT INTO `uiTagMaster`(`uiTagId`,`tenantId`, `serialNo`, `tagId`, `tagGroupId`, `tagName`, `isActive`, `updatedAt`, `updatedBy`, `createdAt`, `createdBy`) SELECT tagId,tenantId, serialNo, tagId, 1 as tagGroupId, tagName, 1 as isActive, updatedAt, updatedBy, createdAt, createdBy FROM plcTagMaster WHERE tagId >=$lastIndex;");


        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
