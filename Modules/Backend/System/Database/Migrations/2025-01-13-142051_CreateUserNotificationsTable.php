<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserNotificationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'userNotificationId' => [
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
            'notificationId'          => [
                'type'           => 'INT',
                'unsigned'       => true,
            ],
            'userId'             => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'readAt'             => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'deliveredWhatsapp'  => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'deliveredWebpush'   => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'deliveredSse'       => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'createdAt'          => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
            'updatedAt'          => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
        ]);

        $this->forge->addKey('userNotificationId', true);
        $this->forge->addForeignKey('userId', 'userMaster', 'userId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('notificationId', 'notifications', 'notificationId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');

        $this->forge->createTable('userNotifications');
    }

    public function down()
    {
        $this->forge->dropTable('userNotifications');
    }
}
