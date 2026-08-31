<?php

namespace Modules\Backend\AalarmModules\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlarmConfig extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'alarmId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tenantId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'serialNo' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'uiTagId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'loloTheresold' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'loTheresold' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'hiTheresold' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'hihiTheresold' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'message' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'isActive' => [
                'type'  => 'BOOLEAN',
                'null'  => false,
                'default'   => false,
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

        //add primary index
        $this->forge->addKey('alarmId', true);

        //add foreign key
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('updatedBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('uiTagId', 'uiTagMaster', 'uiTagId', 'CASCADE', 'RESTRICT');

        $this->forge->createTable('AlarmConfig');
    }

    public function down()
    {
        $this->forge->dropTable('AlarmConfig');
    }
}
