<?php

namespace Modules\Backend\Master\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomStatusActionDataTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'statusActionId' => [
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
            'recordId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],

            // 'module' => [
            //     'type' => 'VARCHAR',
            //     'constraint' => 255,
            //     'null' => false,
            // ],

            // change module varchar field to moduleId foreign key to moduleMaster
            'moduleId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            
            'fieldId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'fieldValue' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'followupHistoryId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'isActive' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'null'    => false,
            ],
            'isDeleted' => [
                'type'    => 'BOOLEAN',
                'null'    => false,
                'default'  => false,
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

        $this->forge->addKey('statusActionId', true); // Primary key
        $this->forge->addKey('isActive', false);
        $this->forge->addKey('isDeleted', false);

        // add foreign keys
        $this->forge->addForeignKey('fieldId', 'customStatusFields', 'fieldId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('updatedBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');

        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');

        $this->forge->createTable('customStatusActionData');
    }

    public function down()
    {
        $this->forge->dropTable('customStatusActionData');
    }
}
