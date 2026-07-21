<?php

namespace Modules\Backend\MachineMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class MachineMasterTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'machineId' => [
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
            'machineCode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'e.g., "MACH01"',
            ],
            'machineName' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'comment'    => 'e.g., "Hydraulic Puncher A"',
            ],
            'machineType' => [
                'type'       => 'ENUM',
                'constraint' => ['SKIPPER', 'Other'],
                'default'    => 'SKIPPER',
                'null'       => false,
            ],
            'isActive' => [
                'type'       => 'TINYINT',
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
            'updatedBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'createdBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('machineId', true); // Primary key
        $this->forge->addUniqueKey('machineCode'); // Unique Constraint

        // add foreign keys
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');

        $this->forge->createTable('machineMaster');
    }

    public function down()
    {
        $this->forge->dropTable('machineMaster');
    }
}
