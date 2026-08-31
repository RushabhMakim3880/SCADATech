<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class PushNotification extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'wpId' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
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
            'endpoint' => [
                'type'       => 'TEXT',
                'null'       => false,
            ],
            'publicKey' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'authToken' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'isValid' => [
                'type'       => 'BOOLEAN',
                'default'    => true,
            ],
            'createdAt' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updatedAt' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('wpId', true); // Primary key
        $this->forge->addForeignKey('userId', 'userMaster', 'userId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');

        $this->forge->createTable('pushNotification');
    }

    public function down()
    {
        $this->forge->dropTable('pushNotification');
    }
}
