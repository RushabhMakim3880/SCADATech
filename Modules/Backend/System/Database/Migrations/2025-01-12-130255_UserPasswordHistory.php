<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserPasswordHistory extends Migration
{
    public function up()
    {
        //create new table
        $this->forge->addField([
            'historyId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'createdAt' => [
                'type' => 'DATETIME'
            ]
        ]);

        $this->forge->addKey('historyId', true);
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey("userId", "userMaster", "userId", "CASCADE", "CASCADE");

        $this->forge->createTable('userPasswordHistory');
    }

    public function down()
    {
        //drop table.
        $this->forge->dropTable("userPasswordHistory");
    }
}
