<?php

namespace Modules\Backend\OpMaster\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Libraries\UserPermissionLib;


class MachineMasterData16 extends Seeder
{
    public $priority = 28;

    public function run()
    {

        $seedName = static::class;
        $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
        if ($exists > 0) {
            return;
        }

        //add plc tags
        $this->db->query("INSERT INTO `plcTagMaster` (`tagId`, `tenantId`, `serialNo`, `plcId`, `tagName`, `tagAddress`, `dataType`, `registerType`, `readWrite`, `scaleFactor`, `offset`, `unit`, `description`, `isActive`, `createdAt`, `updatedAt`, `createdBy`, `updatedBy`) VALUES
                            (656, 1, 656, 1, 'S_PRINCHER_A_M_MM', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_PRINCHER_A_M_MM', 'Double', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (657, 1, 657, 1, 'S_PRINCHER_A_M_CMD', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_PRINCHER_A_M_CMD', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (658, 1, 658, 1, 'S_AUTO_PRINCHER_RETURN_SPEED', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_AUTO_PRINCHER_RETURN_SPEED', 'Double', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (659, 1, 659, 1, 'S_EDGE_FINDER_AUTO_FWD_SPEED', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_EDGE_FINDER_AUTO_FWD_SPEED', 'Double', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3)
                            ;");

        $lastIndex = 656;
        $this->db->query("INSERT INTO `uiTagMaster`(`uiTagId`,`tenantId`, `serialNo`, `tagId`, `tagGroupId`, `tagName`, `isActive`, `updatedAt`, `updatedBy`, `createdAt`, `createdBy`) SELECT tagId,tenantId, serialNo, tagId, 1 as tagGroupId, tagName, 1 as isActive, updatedAt, updatedBy, createdAt, createdBy FROM plcTagMaster WHERE tagId >=$lastIndex;");


        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
