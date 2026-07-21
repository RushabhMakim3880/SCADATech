<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmailQueueTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'emailId' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tenantId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'serialNo' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => '250',
                'default'    => '',
            ],
            'body' => [
                'type' => 'TEXT',
            ],
            'recipientEmail' => [
                'type'       => 'VARCHAR',
                'constraint' => '250',
                'default'    => '',
            ],
            'attempts' => [
                'type'       => 'TINYINT',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'isSent' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'remarks' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'userId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'sentAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'createdAt' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('emailId', true);
        $this->forge->addForeignKey('userId', 'userMaster', 'userId', 'CASCADE', 'CASCADE');
        $this->forge->addKey('attempts');
        $this->forge->addKey('isSent');
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');

        $this->forge->createTable('emailQueue');
    }

    public function down()
    {
        $this->forge->dropTable('emailQueue');
    }
}
