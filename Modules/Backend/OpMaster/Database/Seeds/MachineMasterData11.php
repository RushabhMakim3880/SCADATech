<?php

namespace Modules\Backend\OpMaster\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MachineMasterData11 extends Seeder
{
    public $priority = 24;

    public function run()
    {

        $seedName = static::class;
        $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
        if ($exists > 0) {
            return;
        }

        $this->db = \Config\Database::connect();

        //fix tag mapping.
        $this->db->query("UPDATE `plcTagMaster` SET tagAddress = 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.S_SYSTEM_FAULT' WHERE tagId=643");
        $this->db->query("UPDATE `plcTagMaster` SET tagAddress = 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.T_YB_Voltage' WHERE tagId=620");
        $this->db->query("UPDATE `plcTagMaster` SET tagAddress = 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_PRINCHER_STRIAK' WHERE tagId=641");

        $this->db->query("UPDATE `plcTagMaster` SET dataType = 'Boolean' WHERE tagId=643");
        $this->db->query("UPDATE `plcTagMaster` SET dataType = 'Float' WHERE tagId=620");
        $this->db->query("UPDATE `plcTagMaster` SET dataType = 'Boolean' WHERE tagId=641");

        //rename admin group to systemAdmin
        // $this->db->query("UPDATE userGroups SET groupName='systemAdmin' WHERE groupId=4");

        //add new group admin
        // $groupData = [
        //     "isDefault" => 0,
        //     "isAdmin" => 0,
        //     "tenantId" => 1,
        //     "groupName" => "Admin",
        // ];

        // $this->db->table('userGroups')->insert($groupData);

        // $insertId = $this->db->insertID();

        // update admin user to this group.
        // $this->db->query("UPDATE userMaster SET groupId=$insertId WHERE userId=3");

        //default permissions for admin, supervisor, operator, maintainance

        //for admin
        // $groupPermissions = [
        //     $insertId => [27, 28, 29, 30, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 81, 82, 83, 84, 85],
        //     "5" => [33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 81, 82, 83, 84, 85],
        //     "6" => [27, 28, 29, 30, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 81, 82, 83, 84, 85],
        //     "7" => [27, 28, 29, 30, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 81, 82, 83, 84, 85],
        // ];

        // foreach ($groupPermissions as $groupId => $permissions) {

        //     foreach ($permissions as $p) {
        //         $alreadyExists = $this->db->table('userGroupPermissions')->where(['groupId' => $groupId, 'permissionId' => $p])->countAllResults();

        //         if ($alreadyExists == 0) {
        //             $this->db->table('userGroupPermissions')->insert([
        //                 'groupId' => $groupId,
        //                 'permissionId' => $p,
        //             ]);
        //         }
        //     }
        // }

        //add plc tags
        // $this->db->query("INSERT INTO `plcTagMaster` (`tagId`, `tenantId`, `serialNo`, `plcId`, `tagName`, `tagAddress`, `dataType`, `registerType`, `readWrite`, `scaleFactor`, `offset`, `unit`, `description`, `isActive`, `createdAt`, `updatedAt`, `createdBy`, `updatedBy`) VALUES
        //                     (644, 1, 644, 1, 'AL_Y1_TORQUE_REACHED', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_Y1_TORQUE_REACHED', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
        //                     (645, 1, 645, 1, 'AL_Y2_TORQUE_REACHED', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_Y2_TORQUE_REACHED', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
        //                     (646, 1, 646, 1, 'AL_Y3_TORQUE_REACHED', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_Y3_TORQUE_REACHED', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3),
        //                     (647, 1, 647, 1, 'AL_Y4_TORQUE_REACHED', 'ns=4;s=|var|LicOS-PAC-MC512.Application.GVL.AL_Y4_TORQUE_REACHED', 'Boolean', 'holding', 'readwrite', 1, 0, NULL, NULL, 1, '2025-09-16 09:16:35', '2025-09-16 09:16:35', 3, 3)
        //                     ;");

        // $lastIndex = 644;

        // $this->db->query("INSERT INTO `uiTagMaster`(`uiTagId`,`tenantId`, `serialNo`, `tagId`, `tagGroupId`, `tagName`, `isActive`, `updatedAt`, `updatedBy`, `createdAt`, `createdBy`) SELECT tagId,tenantId, serialNo, tagId, 1 as tagGroupId, tagName, 1 as isActive, updatedAt, updatedBy, createdAt, createdBy FROM plcTagMaster WHERE tagId >=$lastIndex;");


        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
