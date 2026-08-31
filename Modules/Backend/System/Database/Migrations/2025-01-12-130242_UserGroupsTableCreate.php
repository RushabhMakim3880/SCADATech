<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserGroupsTableCreate extends Migration
{
    public function up()
    {
        //create new table
        $this->forge->addField([
            'groupId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tenantId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'serialNo' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'isDefault' => [
                'type' => 'BOOLEAN',
                'default' => 0,
            ],
            'isAdmin' => [
                'type' => 'BOOLEAN',
                'default' => 0,
            ],
            'groupName' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);

        $this->forge->addKey('groupId', true);
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');

        $this->forge->createTable('userGroups');
    }

    public function down()
    {
        //drop table.
        $this->forge->dropTable("userGroups");
    }
}
