<?php

namespace Modules\Backend\MachineMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class MachineOperationConfigTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'operationConfigId' => [
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
            'machineId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'operationCode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'e.g., "PUNCH_A, DRILL_TOOL_1, CUT_B"',
            ],
            'operationType' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'e.g., "punch, cut, drill, move, clamp"',
            ],
            'operationLabel' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'comment'    => 'e.g., "Round Hole Punch"',
            ],
            'positionX' => [
                'type'    => 'FLOAT',
                'null'    => true,
                'comment' => 'optional, if fixed tool on axis',
            ],
            
            'positionY' => [
                'type'    => 'FLOAT',
                'null'    => true,
                'comment' => 'optional',
            ],

            'isMovableHead' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'comment'    => '1 = yes, this tool head moves dynamically',
            ],
            'plcTriggerTag' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'plcAckTag' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'plcAdditionalData' => [
                'type'    => 'JSON',
                'null'    => true,
                'comment' => 'e.g., toolId, speed, pressure',
            ],

            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'createdAt' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updatedAt' => [
                'type' => 'DATETIME',
                'null' => false,
            ],

        ]);

        $this->forge->addKey('operationConfigId', true); // Primary key

        // add foreign keys
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');

        $this->forge->createTable('machineOperationConfig');
    }

    public function down()
    {
        $this->forge->dropTable('machineOperationConfig');
    }
}
