<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserSettings extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'userSettingsId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tenantId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'userId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'key' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'value' => [
                'type' => 'TEXT',
            ]
        ]);

        $this->forge->addKey('userSettingsId', true);
        $this->forge->addForeignKey('userId', 'userMaster', 'userId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->createTable('userSettings');
    }

    public function down()
    {
        $this->forge->dropTable('userSettings');
    }
}
