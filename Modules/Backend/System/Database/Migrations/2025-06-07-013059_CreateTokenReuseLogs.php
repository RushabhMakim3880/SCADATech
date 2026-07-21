<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTokenReuseLogs extends Migration
{
    public function up()
    {
        // tokenReuseLogs table
        $this->forge->addField([
            'logId'       => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'userId'      => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'tenantId'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'serialNo' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'reusedJti'   => ['type' => 'CHAR', 'constraint' => 32, 'null' => false],
            'ipAddress'   => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'deviceInfo'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'createdAt'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('logId', true);
        $this->forge->addKey('userId');
        $this->forge->addKey('reusedJti');

        // Add foreign key constraints
        $this->forge->addForeignKey('userId', 'userMaster', 'userId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');

        $this->forge->createTable('tokenReuseLogs', true);
    }

    public function down()
    {
        $this->forge->dropTable('tokenReuseLogs', true);
    }
}
