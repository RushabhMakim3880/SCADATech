<?php

namespace Modules\Backend\PlcMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class PlcMasterTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'plcId' => [
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
            'plcName' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'comment'    => 'e.g., "Main PLC"',
            ],
            'protocol' => [
                'type'       => 'ENUM',
                'constraint' => ['modbus-tcp', 'opc-ua', 'mqtt', 'custom'],
                'null'       => false,
            ],
            'ipAddress' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],
            'port' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'modbusDeviceId' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'Applicable for Modbus protocol',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 1,
                'comment'    => '1 = active, 0 = inactive',
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

        $this->forge->addKey('plcId', true); // Primary key

        // add foreign keys
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('machineId', 'machineMaster', 'machineId', 'CASCADE', 'CASCADE');


        $this->forge->createTable('plcMaster');
    }

    public function down()
    {
        $this->forge->dropTable('plcMaster');
    }
}
