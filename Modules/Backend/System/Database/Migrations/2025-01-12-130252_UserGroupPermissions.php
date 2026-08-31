<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserGroupPermissions extends Migration
{
    public function up()
    {
        //create new table
        $this->forge->addField([
            'userGroupPermissionId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'groupId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'permissionId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ]);

        $this->forge->addKey('userGroupPermissionId', true);
        $this->forge->addForeignKey('groupId', 'userGroups', 'groupId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('permissionId', 'userPermissionMaster', 'permissionId', 'CASCADE', 'CASCADE');
        $this->forge->createTable('userGroupPermissions');
    }

    public function down()
    {
        //drop table.
        $this->forge->dropTable("userGroupPermissions");
    }
}
