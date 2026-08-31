<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateModuleMaster extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'moduleId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tenantId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'serialNo' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'moduleName' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'moduleDescription' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'createdAt' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updatedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('moduleId', true);
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');

        $this->forge->createTable('moduleMaster');
    }

    public function down()
    {
        $this->forge->dropTable('moduleMaster', true);
    }
}
