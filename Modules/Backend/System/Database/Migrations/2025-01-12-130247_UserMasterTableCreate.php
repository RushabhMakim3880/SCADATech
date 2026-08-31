<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserMasterTableCreate extends Migration
{
    public function up()
    {
        //create new table.
        $this->forge->addField([
            'userId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'groupId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
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
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'firstName' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'lastName' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'mobile' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
            ],
            'singleSignonToken' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            '2FaToken' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'resetPasswordToken' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'failedAttempts' => [
                'type' => 'TINYINT',
                'constraint' => 5,
                'default' => 0,
            ],
            'lockoutUntil' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'passwordExpiryTime' => [
                'type' => 'DATETIME',
            ],
            'isActive' => [
                'type' => 'BOOLEAN',
                'default' => 1,
            ],
            'lastLoginTime' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'lastActiveTime' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updatedBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'createdBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'updatedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'createdAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);

        // add keys
        $this->forge->addKey('userId', true);
        $this->forge->addUniqueKey('username');
        $this->forge->addUniqueKey('email');


        // add foreign keys
        $this->forge->addForeignKey('groupId', 'userGroups', 'groupId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('updatedBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');

        //create table
        $this->forge->createTable('userMaster');
    }

    public function down()
    {
        //drop table
        $this->forge->dropTable('userMaster');
    }
}
