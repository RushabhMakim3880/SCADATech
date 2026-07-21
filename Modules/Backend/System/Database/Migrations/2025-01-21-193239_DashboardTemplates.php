<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class DashboardTemplates extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'templateId' => [
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
            'widgetName' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'htmlTemplate' => [
                'type' => 'TEXT',
            ],
            'dataSource' => [
                'type' => 'JSON',
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
        $this->forge->addKey('templateId', true);
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'SET NULL');
        $this->forge->createTable('dashboardTemplates');
    }

    public function down()
    {
        $this->forge->dropTable('dashboardTemplates');
    }
}
