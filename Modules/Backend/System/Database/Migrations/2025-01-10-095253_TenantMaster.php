<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class TenantMaster extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'tenantId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'subDomain' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'customDomain' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'tenantName' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'companyName' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'mobile' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'companyAddress' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'locationId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'tenantType' => [
                'type' => 'ENUM',
                'constraint' => ['clientLive', 'clientTrial', 'clientFree', 'personalLive', 'personalDemo'],
                'default' => 'clientLive',
            ],
            'isActive' => [
                'type' => 'BOOLEAN',
                'default' => 1,
            ],
            'createdBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'createdOn' => [
                'type' => 'DATETIME',
            ],
            'updatedBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'updatedOn' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);

        $this->forge->addKey('tenantId', true);
        $this->forge->addForeignKey('locationId', 'locationMaster', 'locationId', 'CASCADE', 'SET NULL');
        // $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');
        // $this->forge->addForeignKey('updatedBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');

        $this->forge->createTable('tenantMaster');
    }

    public function down()
    {
        $this->forge->dropTable('tenantMaster');
    }
}
