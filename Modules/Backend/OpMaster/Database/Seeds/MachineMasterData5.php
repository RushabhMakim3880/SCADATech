<?php

namespace Modules\Backend\OpMaster\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MachineMasterData5 extends Seeder
{
    public $priority = 18;

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
                            (609, 1, 609, 1, 'AL_ANGLE_SLOW_OR_REFERENCE_SENSOR_SENSED', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_ANGLE_SLOW_OR_REFERENCE_SENSOR_SENSED', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (610, 1, 610, 1, 'AL_6_METER_PHOTO_SENSOR_IS_SENSED', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_6_METER_PHOTO_SENSOR_IS_SENSED', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (611, 1, 611, 1, 'S_AUTO_CYCLE_RUUNING', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_AUTO_CYCLE_RUUNING', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (612, 1, 612, 1, 'S_MANUAL_MACHINE_RUNNING', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_MANUAL_MACHINE_RUNNING', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (613, 1, 613, 1, 'S_AUTO_CYCLE_PAUSE', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_AUTO_CYCLE_PAUSE', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
                            (614, 1, 614, 1, 'S_MACHINE_IDEAL', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_MACHINE_IDEAL', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3)
                            ;");

        $lastIndex = 609;

        $this->db->query("INSERT INTO `uiTagMaster`(`uiTagId`,`tenantId`, `serialNo`, `tagId`, `tagGroupId`, `tagName`, `isActive`, `updatedAt`, `updatedBy`, `createdAt`, `createdBy`) SELECT tagId,tenantId, serialNo, tagId, 1 as tagGroupId, tagName, 1 as isActive, updatedAt, updatedBy, createdAt, createdBy FROM plcTagMaster WHERE tagId >=$lastIndex;");

        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
