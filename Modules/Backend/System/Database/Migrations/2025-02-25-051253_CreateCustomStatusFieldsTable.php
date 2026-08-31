<?php

namespace Modules\Backend\Master\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomStatusFieldsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'fieldId' => [
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
            'statusId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'fieldName' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'fieldType' => [
                'type' => 'ENUM',
                'constraint' => ['text', 'dropdown', 'number', 'date'],
                'null' => false,
            ],
            'fieldOptions' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'isRequired' => [
                'type'  => 'BOOLEAN',
                'null'  => false,
                'default'  => false,
            ],
            'isActive' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'null'    => false,
            ],
            'isDeleted' => [
                'type'       => 'BOOLEAN',
                'null'       => false,
                'default'    => false,
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

        $this->forge->addKey('fieldId', true); // Primary key
        $this->forge->addKey('isActive', false);
        $this->forge->addKey('isDeleted', false);


        // add foreign keys
        $this->forge->addForeignKey('statusId', 'statusMaster', 'statusId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('updatedBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');

        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');

        $this->forge->createTable('customStatusFields');
    }

    public function down()
    {
        $this->forge->dropTable('customStatusFields');
    }
}
