<?php

namespace Modules\Backend\System\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserData extends Seeder
{
    public $priority = 3;

    public function run()
    {

        $seedName = static::class;
        $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
        if ($exists > 0) {
            return;
        }

        //add saas level groups.
        // User Groups
        $groupData = [];
        $groupData[] = [
            "isDefault" => 1,
            "isAdmin" => 1,
            "tenantId" => null,
            "groupName" => "Saas Admin",
            "users" => [
                [
                    "tenantId" => null,
                    "serialNo" => 1,
                    "username" => "superSaasAdmin",
                    "password" => password_hash("Mtpl@652$#Saas", PASSWORD_DEFAULT),
                    "firstName" => "Saas",
                    "lastName" => "Admin",
                    "email" => "saasAdmin@datenics.com",
                    "mobile" => "1234567890",
                    "passwordExpiryTime" => date("Y-m-d H:i:s", strtotime("+90 days")),
                    "updatedAt" => date("Y-m-d H:i:s"),
                    "createdAt" => date("Y-m-d H:i:s")
                ]
            ]
        ];

        $groupData[] = [
            "isDefault" => 1,
            "isAdmin" => 0,
            "tenantId" => null,
            "groupName" => "Saas Manager"
        ];

        $groupData[] = [
            "isDefault" => 1,
            "isAdmin" => 0,
            "tenantId" => null,
            "groupName" => "Saas User"
        ];

        $groupData[] = [
            "isDefault" => 1,
            "isAdmin" => 1,
            "tenantId" => 1,
            "groupName" => "Admin",
            "users" => [
                [
                    "tenantId" => 1,
                    "serialNo" => 0,
                    "username" => "systemAdmin",
                    "password" => password_hash("password", PASSWORD_DEFAULT),
                    "firstName" => "Admin",
                    "lastName" => "user",
                    "email" => "systemAdmin@datenics.com",
                    "mobile" => "1234567890",
                    "groupId" => null,
                    "passwordExpiryTime" => date("Y-m-d H:i:s", strtotime("+90 days")),
                    "updatedAt" => date("Y-m-d H:i:s"),
                    "createdAt" => date("Y-m-d H:i:s")
                ],
                [
                    "tenantId" => 1,
                    "serialNo" => 1,
                    "username" => "admin",
                    "password" => password_hash("password", PASSWORD_DEFAULT),
                    "firstName" => "Admin",
                    "lastName" => "user",
                    "email" => "adminUser@datenics.com",
                    "mobile" => "1234567890",
                    "groupId" => null,
                    "passwordExpiryTime" => date("Y-m-d H:i:s", strtotime("+90 days")),
                    "updatedAt" => date("Y-m-d H:i:s"),
                    "createdAt" => date("Y-m-d H:i:s")
                ]
            ],
        ];

        $groupData[] = [
            "isDefault" => 1,
            "isAdmin" => 0,
            "tenantId" => 1,
            "groupName" => "Supervisor",
            "users" => [
                [
                    "tenantId" => 1,
                    "serialNo" => 2,
                    "username" => "supervisor",
                    "password" => password_hash("password", PASSWORD_DEFAULT),
                    "firstName" => "Supervisor",
                    "lastName" => "User",
                    "email" => "supervisor@datenics.com",
                    "mobile" => "1234567890",
                    "groupId" => null,
                    "passwordExpiryTime" => date("Y-m-d H:i:s", strtotime("+90 days")),
                    "updatedAt" => date("Y-m-d H:i:s"),
                    "createdAt" => date("Y-m-d H:i:s")
                ]
            ],
        ];

        $groupData[] = [
            "isDefault" => 1,
            "isAdmin" => 0,
            "tenantId" => 1,
            "groupName" => "Maintainance",
            "users" => [
                [
                    "tenantId" => 1,
                    "serialNo" => 3,
                    "username" => "maintainance",
                    "password" => password_hash("password", PASSWORD_DEFAULT),
                    "firstName" => "Maintainance",
                    "lastName" => "User",
                    "email" => "maintainance@datenics.com",
                    "mobile" => "1234567890",
                    "groupId" => null,
                    "passwordExpiryTime" => date("Y-m-d H:i:s", strtotime("+90 days")),
                    "updatedAt" => date("Y-m-d H:i:s"),
                    "createdAt" => date("Y-m-d H:i:s")
                ]
            ],
        ];

        $groupData[] = [
            "isDefault" => 1,
            "isAdmin" => 0,
            "tenantId" => 1,
            "groupName" => "Operator",
            "users" => [
                [
                    "tenantId" => 1,
                    "serialNo" => 3,
                    "username" => "operator",
                    "password" => password_hash("password", PASSWORD_DEFAULT),
                    "firstName" => "Operator",
                    "lastName" => "User",
                    "email" => "operator@datenics.com",
                    "mobile" => "1234567890",
                    "groupId" => null,
                    "passwordExpiryTime" => date("Y-m-d H:i:s", strtotime("+90 days")),
                    "updatedAt" => date("Y-m-d H:i:s"),
                    "createdAt" => date("Y-m-d H:i:s")
                ]
            ],
        ];

        foreach ($groupData as $group) {
            $users = $group['users'] ?? [];
            unset($group['users']);
            $this->db->table('userGroups')->insert($group);
            $groupId = $this->db->insertID();
            foreach ($users as $user) {
                $user['groupId'] = $groupId;
                $this->db->table('userMaster')->insert($user);
            }
        }

        //         $query = 'INSERT INTO `userMaster` (`userId`, `groupId`, `tenantId`, `serialNo`, `username`, `password`, `firstName`, `lastName`, `email`, `mobile`, `singleSignonToken`, `2FaToken`, `resetPasswordToken`, `failedAttempts`, `lockoutUntil`, `passwordExpiryTime`, `isActive`, `lastLoginTime`, `lastActiveTime`, `updatedBy`, `createdBy`, `updatedAt`, `createdAt`) VALUES
        // -- (3, 6, 1, 3, \'Minakshi\', \'$2y$10$e89MichLDW5OK9tzjrQgXO6M4jMRM.A4pgIj8fm81W4WvYukpGFyW\', \'Minakshi\', \'Patel\', \'m@gmail.com\', \'+917894561235\', NULL, NULL, NULL, 0, NULL, \'2025-09-23 11:55:54\', 1, NULL, NULL, NULL, 2, NULL, \'2025-06-25 11:55:54\'),
        // (4, 6, 1, 4, \'Keshvi\', \'$2y$10$so0uLPNdcJb3zZzvllwdHOzGLM/pBsW7uujbgjC2T0i709wDezNB2\', \'Keshvi\', \'Mehta\', \'k@gmail.com\', \'+911234569872\', NULL, NULL, NULL, 0, NULL, \'2025-09-23 11:56:36\', 1, NULL, NULL, NULL, 2, NULL, \'2025-06-25 11:56:36\'),
        // (5, 6, 1, 5, \'Mona\', \'$2y$10$maUA.ZoqEJ8bme4aFA6CPObblxS5A7FXqBkQ05KTNxO74u9wHZKEa\', \'Mona\', \'Patel\', \'mona@gmail.com\', \'+918527419635\', NULL, NULL, NULL, 0, NULL, \'2025-09-23 11:57:27\', 1, NULL, NULL, NULL, 2, NULL, \'2025-06-25 11:57:27\'),
        // (6, 6, 1, 6, \'Keyur\', \'$2y$10$eDD6RLylGd.CB/B5RX9svui7yCArPxLuIv5wYGA7DgWax5DQa.Jdy\', \'Keyur\', \'Mohitra\', \'keyur@gmail.com\', \'+919638527415\', NULL, NULL, NULL, 0, NULL, \'2025-09-23 11:59:08\', 1, NULL, NULL, NULL, 2, NULL, \'2025-06-25 11:59:08\');';
        //         $this->db->query($query);

        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
