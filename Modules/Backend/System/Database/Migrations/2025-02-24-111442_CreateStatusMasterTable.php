<?php

namespace Modules\Backend\Master\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStatusMasterTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'statusId' => [
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
            'moduleId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'statusName' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],

            'statusType' => [
                'type' => 'ENUM',
                'constraint' => ['Fresh', 'Open', 'Won', 'Lost'],
                'null' => false,
            ],
            'isDefaultEntry' => [
                'type' => 'BOOLEAN',
                'null'  => false,
                'default'   => false,
            ],
            'isEditable' => [
                'type'  => 'BOOLEAN',
                'null'   => false,
                'default'   => true,
            ],
            'isAction' => [
                'type'   => 'BOOLEAN',
                'null'    => false,
                'default'  => false,
            ],
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'textColor' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'bgColor' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'sequence' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'unsigned' => true,
            ],
            'departmentId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'isActive' => [
                'type'   => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'null'    => false,
            ],
            'isSystemManaged' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'null' => false,
            ],
            'isDeleted' => [
                'type'  => 'BOOLEAN',
                'null'  => false,
                'default' => false,
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

        $this->forge->addKey('statusId', true); // Primary key
        $this->forge->addKey('isActive', false);
        $this->forge->addKey('isDeleted', false);

        // add foreign keys
        $this->forge->addForeignKey('updatedBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');

        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');

        $this->forge->createTable('statusMaster');

        // add foreign keys
    }

    public function down()
    {
        $this->forge->dropTable('statusMaster');
    }
}
