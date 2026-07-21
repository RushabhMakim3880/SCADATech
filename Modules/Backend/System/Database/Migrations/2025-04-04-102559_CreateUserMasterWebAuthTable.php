<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserMasterWebAuthTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'webAuthId' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'userId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'credentialId' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'publicKey' => [
                'type' => 'TEXT',
            ],
            'signCount' => [
                'type' => 'INT',
                'default' => 0,
                'null' => true
            ],
            'fmt' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'aaguid' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'fingerprint' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'deviceName' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'darkIcon' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'lightIcon' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'lastUsedAt' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'createdAt' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updatedAt' => [
                'type' => 'DATETIME',
                'null' => true
            ],
        ]);

        $this->forge->addKey('webAuthId', true);
        $this->forge->addForeignKey('userId', 'userMaster', 'userId', 'CASCADE', 'CASCADE');
        $this->forge->createTable('userMasterWebAuth');
    }

    public function down()
    {
        $this->forge->dropTable('userMasterWebAuth');
    }
}
