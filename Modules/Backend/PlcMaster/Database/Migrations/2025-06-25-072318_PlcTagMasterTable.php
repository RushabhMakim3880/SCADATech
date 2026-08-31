<?php

namespace Modules\Backend\PlcMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class PlcTagMasterTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'tagId' => [
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
            'plcId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],

            'tagName' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'comment'    => 'e.g., "punchTrigger", "headXPos"',
            ],

            'tagAddress' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => false,
                'comment'    => 'e.g., "1000" or "HR40001"',
            ],

            'dataType' => [
                'type'       => 'ENUM',
                'constraint' => ["Boolean", "SByte", "Byte", "Int16", "UInt16", "Int32", "UInt32", "Int64", "UInt64", "Float", "Double", "String", "DateTime"],
                'null'       => false,
            ],

            'registerType' => [
                'type'       => 'ENUM',
                'constraint' => ['coil', 'holding', 'input', 'discrete', 'variable'],
                'null'       => false,
                'comment'    => 'Protocol-specific register type',
            ],

            'readWrite' => [
                'type'       => 'ENUM',
                'constraint' => ['read', 'write', 'readwrite'],
                'null'       => false,
            ],

            'scaleFactor' => [
                'type'    => 'FLOAT',
                'null'    => false,
                'default' => 1.0,
            ],

            'offset' => [
                'type'    => 'FLOAT',
                'null'    => false,
                'default' => 0.0,
            ],

            'unit' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'comment'    => 'e.g., mm, bar, etc.',
            ],

            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'isActive' => [
                'type'   => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
            ],

            'createdAt' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updatedAt' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'createdBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'updatedBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],

        ]);

        $this->forge->addKey('tagId', true); // Primary key

        // add foreign keys
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('plcId', 'plcMaster', 'plcId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('updatedBy', 'userMaster', 'userId', 'CASCADE', 'CASCADE');


        $this->forge->createTable('plcTagMaster');
    }

    public function down()
    {
        $this->forge->dropTable('plcTagMaster');
    }
}
