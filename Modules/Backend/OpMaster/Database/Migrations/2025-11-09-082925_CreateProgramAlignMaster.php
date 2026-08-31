<?php

namespace Modules\Backend\OpMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgramAlignMaster extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'programId' => [
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
            'fullProgram' => [
                'type' => 'JSON',
                'null' => false,
            ],
            'machineSetup' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'completedCycles' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'totalItems' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'totalOperations' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'DA1' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'DA2' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'DA3' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'DB1' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'DB2' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'DB3' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'Marking1' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'Marking2' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'Marking3' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'Marking4' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'cuttings' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'updatedBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'updatedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'createdAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'createdBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ]
        ]);

        //add primary index
        $this->forge->addKey('programId', true);

        //add foreign key
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('updatedBy', 'userMaster', 'userId', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'SET NULL', 'CASCADE');

        $this->forge->createTable('programAlignMaster');
    }

    public function down()
    {
        $this->forge->dropTable('programAlignMaster');
    }
}
