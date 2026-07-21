<?php

namespace Modules\Backend\UiTagMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class UiTagMasterTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'uiTagId' => [
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
            'tagId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tagGroupId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tagName' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'minValue' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
            ],
            'maxValue' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
            ],
            'isActive' => [
                'type'   => 'TINYINT',
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

        $this->forge->addKey('uiTagId', true); // Primary key
        $this->forge->addKey('isActive', false);


        // add foreign keys
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('updatedBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('tagId', 'plcTagMaster', 'tagId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tagGroupId', 'plcTagGroupMaster', 'tagGroupId', 'CASCADE', 'CASCADE');



        $this->forge->createTable('uiTagMaster');
    }

    public function down()
    {
        $this->forge->dropTable('uiTagMaster');
    }
}
