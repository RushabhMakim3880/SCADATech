<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class DashboardMaster extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'dashboardId' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tenantId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'uid' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'dashboardName' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'layout' => [
                'type' => 'TEXT',
            ],
            'updatedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'createdAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'createdBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],

        ]);
        $this->forge->addKey('dashboardId', true);
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'SET NULL');

        $this->forge->createTable('dashboardLayouts');
    }

    public function down()
    {
        $this->forge->dropTable('dashboardLayouts');
    }
}
