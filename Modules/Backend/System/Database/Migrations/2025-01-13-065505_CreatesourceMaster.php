<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatesourceMaster extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'sourceId' => [
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
            'serialNo' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'sourceName' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'isActive' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'null'    => false,
            ],
            'updatedAt' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updatedBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'createdAt' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'createdBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
        ]);

        $this->forge->addKey('sourceId', true); // Primary key
        $this->forge->addKey('isActive', false); // Index

        // add foreign keys
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('updatedBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');

        $this->forge->createTable('sourceMaster'); // create table

    }

    public function down()
    {
        $this->forge->dropTable('sourceMaster');
    }
}
