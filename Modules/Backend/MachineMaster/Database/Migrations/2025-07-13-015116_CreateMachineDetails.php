<?php

namespace Modules\Backend\MachineMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMachineDetails extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'machineDetailId' => [
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
            'headName' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'headType' => [
                'type'       => 'ENUM',
                'constraint' => ['Punching', 'Marking', 'Cutting'],
                'default'    => 'Punching',
                'null'       => false,
            ],
            'xPosition' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 0.00,
            ],
            'holdDownX' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 0.00,
            ],
            'side' => [
                'type'       => 'ENUM',
                'constraint' => ['N/A', 'A', 'B'],
                'default'    => 'N/A',
                'null'       => false,
            ],
            'markingCassets' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ]
        ]);

        //add primary index
        $this->forge->addKey('machineDetailId', true);

        //add foreign key
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('machineId', 'machineMaster', 'machineId', 'CASCADE', 'CASCADE');
        $this->forge->createTable('machineDetails');
    }

    public function down()
    {
        $this->forge->dropTable('machineDetails', true);
    }
}
