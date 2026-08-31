<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRefreshTokens extends Migration
{
    public function up()
    {
        // refreshTokens table
        $this->forge->addField([
            'tokenId'     => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'userId'      => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'tenantId'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'serialNo' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'jti'         => ['type' => 'CHAR', 'constraint' => 32, 'null' => false],
            'singleSignonToken'         => ['type' => 'CHAR', 'constraint' => 32, 'null' => false],
            'deviceInfo'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'ipAddress'   => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'rememberMe'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'expiresAt'   => ['type' => 'DATETIME', 'null' => false],
            'createdAt'   => ['type' => 'DATETIME', 'null' => true]
        ]);
        $this->forge->addKey('tokenId', true);
        $this->forge->addKey('jti', false, true); // unique index
        $this->forge->addKey('userId');
        $this->forge->addKey('tenantId');



        // Add foreign key constraints
        $this->forge->addForeignKey('userId', 'userMaster', 'userId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');

        $this->forge->createTable('refreshTokens', true);
    }

    public function down()
    {
        $this->forge->dropTable('refreshTokens', true);
    }
}
