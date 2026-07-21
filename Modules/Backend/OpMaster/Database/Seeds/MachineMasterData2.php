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
                            (558, 1, 558, 1, 'S_PRINCHER_AT_ZERO_1', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_PRINCHER_AT_ZERO_1', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-08-02 15:19:13', '2025-08-02 15:19:13', 2, 2),
                            (559, 1, 559, 1, 'S_SERVO_RUNNING', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_SERVO_RUNNING', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-08-02 15:19:13', '2025-08-02 15:19:13', 2, 2)
                            ;");

        $lastIndex = 558;

        $this->db->query("INSERT INTO `uiTagMaster`(`uiTagId`,`tenantId`, `serialNo`, `tagId`, `tagGroupId`, `tagName`, `isActive`, `updatedAt`, `updatedBy`, `createdAt`, `createdBy`) SELECT tagId,tenantId, serialNo, tagId, 1 as tagGroupId, tagName, 1 as isActive, updatedAt, updatedBy, createdAt, createdBy FROM plcTagMaster WHERE tagId >=$lastIndex;");

        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
