<?php

namespace Modules\Backend\OpMaster\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Libraries\UserPermissionLib;


class MachineMasterData17 extends Seeder
{
    public $priority = 29;

    public function run()
    {

        $seedName = static::class;
        $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
        if ($exists > 0) {
            return;
        }

        //add plc tags
        $this->db->query("INSERT INTO `plcTagMaster` (`tagId`, `tenantId`, `serialNo`, `plcId`, `tagName`, `tagAddress`, `dataType`, `registerType`, `readWrite`, `scaleFactor`, `offset`, `unit`, `description`, `isActive`, `createdAt`, `updatedAt`, `createdBy`, `updatedBy`) VALUES
                            (660, 1, 660, 1, 'CS1_SET_SP', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.CS1_SET_SP', 'Double', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (661, 1, 661, 1, 'CS2_SET_SP', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.CS2_SET_SP', 'Double', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (662, 1, 662, 1, 'CS3_SET_SP', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.CS3_SET_SP', 'Double', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (663, 1, 663, 1, 'CS4_SET_SP', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.CS4_SET_SP', 'Double', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3)
                            ;");

        $lastIndex = 660;
        $this->db->query("INSERT INTO `uiTagMaster`(`uiTagId`,`tenantId`, `serialNo`, `tagId`, `tagGroupId`, `tagName`, `isActive`, `updatedAt`, `updatedBy`, `createdAt`, `createdBy`) SELECT tagId,tenantId, serialNo, tagId, 1 as tagGroupId, tagName, 1 as isActive, updatedAt, updatedBy, createdAt, createdBy FROM plcTagMaster WHERE tagId >=$lastIndex;");

        // update ui tag min max values
        $this->db->query("UPDATE `uiTagMaster` SET `minValue`='0', `maxValue`='220' WHERE uiTagId=660");
        $this->db->query("UPDATE `uiTagMaster` SET `minValue`='0', `maxValue`='220' WHERE uiTagId=661");
        $this->db->query("UPDATE `uiTagMaster` SET `minValue`='0', `maxValue`='220' WHERE uiTagId=662");
        $this->db->query("UPDATE `uiTagMaster` SET `minValue`='0', `maxValue`='220' WHERE uiTagId=663");

        $this->db->query("UPDATE `uiTagMaster` SET `minValue`='0', `maxValue`='1' WHERE uiTagId=617");
        $this->db->query("UPDATE `uiTagMaster` SET `minValue`='0', `maxValue`='1' WHERE uiTagId=615");
        $this->db->query("UPDATE `uiTagMaster` SET `minValue`='0', `maxValue`='1' WHERE uiTagId=616");
        $this->db->query("UPDATE `uiTagMaster` SET `minValue`='0', `maxValue`='2' WHERE uiTagId=618");

        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
