<?php

namespace Modules\Backend\OpMaster\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Libraries\UserPermissionLib;


class MachineMasterData12 extends Seeder
{
    public $priority = 24;

    public function run()
    {

        $seedName = static::class;
        $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
        if ($exists > 0) {
            return;
        }

        //empty permission table.
        $this->db->query("DELETE FROM userPermissionMaster WHERE 1");

        UserPermissionLib::syncPermissionsToDatabase();

        //default permissions for admin(8), supervisor(5), maintainance(6), operator(7)

        //for admin
        $permissions = [];
        $permissions['OpHoming'] = [
            // 'homingSpeed' => [8, 6,5, 7],
            'proxyWear' => [8, 6],
            'wear' => [8, 6, 5, 7],
            // 'homePosition' => [8, 6, 5, 7],
        ];

        $permissions['OpSettings'] = [
            'distance' => [8, 6, 5],
            'punchSelection' => [8, 6, 5, 7],
            'barDetails' => [8, 6, 5, 7],
            'scrapDetails' => [8, 6, 5, 7],
            'princherSpeed' => [8, 6, 5],
            // 'servoAccSettings' => [8, 6, 5, 7],
            'oilTemperature' => [8, 6],
            'lubSettings' => [8, 6],
            'masterSettings' => [8, 6],
            'accumulatorPressureSettings' => [8, 6],
            'markingPressureSettings' => [8, 6, 5, 7],
            'princherPressureSettings' => [8, 6],
            'inFeedPressureSettings' => [8, 6],
        ];

        $permissions['OpAuto'] = [
            'autoSpeed' => [8, 6],
        ];


        $permissions['AalarmModules'] = [
            // 'add' => [8, 6, 5, 7],
            // 'edit' => [8, 6, 5, 7],
            // 'view' => [8, 6, 5, 7],
            // 'delete' => [8, 6, 5, 7],
            // 'manage' => [8, 6, 5, 7],
        ];

        $permissions['AlarmLog'] = [
            // 'add' => [8, 6, 5, 7],
            // 'edit' => [8, 6, 5, 7],
            'view' => [8, 6, 5, 7],
            // 'delete' => [8, 6, 5, 7],
            // 'manage' => [8, 6, 5, 7],
        ];

        $permissions['ItemRecipeMaster'] = [
            'add' => [8, 6, 5, 7],
            'edit' => [8, 6, 5, 7],
            'view' => [8, 6, 5, 7],
            'delete' => [8, 6, 5, 7],
            'manage' => [8, 6, 5, 7],
        ];

        $permissions['jobCards'] = [
            'add' => [8, 6, 5, 7],
            'edit' => [8, 6, 5, 7],
            'view' => [8, 6, 5, 7],
            'delete' => [8, 6, 5, 7],
            'manage' => [8, 6, 5, 7],
        ];

        $permissions['userMaster'] = [
            'add' => [8],
            'edit' => [8],
            'viewOwn' => [8],
            'viewAll' => [8]
        ];

        foreach ($permissions as $module => $perms) {
            foreach ($perms as $perm => $groups) {

                // find permission id
                $p = $this->db->query("SELECT permissionId FROM userPermissionMaster WHERE module='$module' AND permission='$perm'")->getRow();
                if ($p) {
                    foreach ($groups as $groupId) {

                        $exists = $this->db->table('userGroupPermissions')
                            ->where('groupId', $groupId)
                            ->where('permissionId', $p->permissionId)
                            ->countAllResults();
                        if ($exists == 0) {
                            $this->db->table('userGroupPermissions')->insert([
                                'permissionId' => $p->permissionId,
                                'groupId' => $groupId
                            ]);
                        }
                    }
                }
            }
        }


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
