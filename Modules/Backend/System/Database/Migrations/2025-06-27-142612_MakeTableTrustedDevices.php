<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeTableTrustedDevices extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'deviceId'      => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'tenantId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'serialNo' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'userId'        => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'unsigned' => true,
            ],
            'deviceToken'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'userAgent'     => ['type' => 'TEXT'],
            'ipAddress'     => ['type' => 'VARCHAR', 'constraint' => 45],
            'isApproved'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'expiresAt'     => ['type' => 'DATETIME', 'null' => true],
            'approvedBy'    => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'unsigned' => true,
            ],
            'approvedAt'    => ['type' => 'DATETIME', 'null' => true],
            'lastUsedAt'    => ['type' => 'DATETIME', 'null' => true],
            'createdAt'     => ['type' => 'DATETIME'],
            'updatedAt'     => ['type' => 'DATETIME'],
        ]);

        $this->forge->addKey('deviceId', true);
        $this->forge->addKey('deviceToken');
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('userId', 'userMaster', 'userId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('approvedBy', 'userMaster', 'userId', 'SET NULL', 'CASCADE');

        $this->forge->createTable('trustedDevices');
    }

    public function down()
    {
        $this->forge->dropTable('trustedDevices');
    }
}
