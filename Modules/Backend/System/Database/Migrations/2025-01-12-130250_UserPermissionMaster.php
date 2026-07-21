<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserPermissionMaster extends Migration
{
    public function up()
    {
        //create new table
        $this->forge->addField([
            'permissionId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'scope' => [
                'type' => 'ENUM',
                'constraint' => ['saas', 'tenant'],
                'default' => 'tenant',
            ],
            'module' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'moduleName' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'permission' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'permissionName' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
        ]);

        $this->forge->addKey('permissionId', true);

        $this->forge->createTable('userPermissionMaster');
    }

    public function down()
    {
        //drop table.
        $this->forge->dropTable("userPermissionMaster");
    }
}
