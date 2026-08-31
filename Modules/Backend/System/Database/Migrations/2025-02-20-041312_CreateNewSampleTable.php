<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNewSampleTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'newSampleId' => [
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
            'serialNo' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'sampleDate' => [
                'type'       => 'Date',
                'null' => true
            ],
            'newSampleName' => [
                'type' => 'VARCHAR',
                'constraint' => '250',
                'null' => true
            ],
            'priority' => [
                'type' => 'ENUM',
                'constraint' => ['hot', 'cold', 'warm'],
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'locationId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'colorCode' => [
                'type'       => 'VARCHAR',
                'constraint' => '250',
                'null' => true
            ],
            'iconCode' => [
                'type'       => 'VARCHAR',
                'constraint' => '250',
                'null' => true
            ],

            'isActive' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null' => true

            ],
            'category' => [
                'type' => 'ENUM',
                'constraint' => ['one', 'two', 'three'],
            ],
            'timepicker' => [
                'type' => 'time',
                'null' => true
            ],
            'dateTime' => [
                'type' => 'datetime',
                'null' => true
            ],
            "checkboxes" => [
                'type' => 'varchar',
                'constraint' => '250',
                'null' => true
            ],
            "radios" => [
                'type' => 'varchar',
                'constraint' => '250',
                'null' => true
            ],
            "simpleDropdown" => [
                'type' => 'int',
                'constraint' => 11,
                'null' => true
            ],
            "simpleDropdownMultiple" => [
                'type' => 'varchar',
                'constraint' => '250',
                'null' => true
            ],
            'description' => [
                'type' => 'VARCHAR',
                'constraint' => '250',
                'null' => true
            ],
            'isDeleted' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null' => true

            ],
            'updatedBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'updatedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'createdBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'createdAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('newSampleId', true);
        $this->forge->addForeignKey('updatedBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');

        $this->forge->addKey('locationId');

        $this->forge->createTable('newSampleTable');
    }

    public function down()
    {
        $this->forge->dropTable('newSampleTable');
    }
}
