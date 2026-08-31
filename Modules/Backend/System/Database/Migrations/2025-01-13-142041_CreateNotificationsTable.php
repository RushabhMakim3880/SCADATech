<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'notificationId'          => [
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
            'type'        => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'title'       => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'message'     => [
                'type'       => 'VARCHAR',
                'constraint' => '1000',
                'null'       => true,
            ],
            'data'        => [
                'type'    => 'JSON',
                'null'    => true,
            ],
            'createdBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'createdAt'  => [
                'type'    => 'DATETIME',
                'null'    => false,
            ]
        ]);
        $this->forge->addKey('notificationId', true);
        $this->forge->addKey('type');
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');


        $this->forge->createTable('notifications');
    }

    public function down()
    {
        $this->forge->dropTable('notifications');
    }
}
