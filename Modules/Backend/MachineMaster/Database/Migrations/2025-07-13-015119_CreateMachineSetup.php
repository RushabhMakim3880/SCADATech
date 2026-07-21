<?php

namespace Modules\Backend\MachineMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMachineSetup extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'machineSetupId' => [
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
            'machineDetailId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'childId' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'value' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ]
        ]);

        //add primary index
        $this->forge->addKey('machineSetupId', true);

        //add foreign key
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('machineDetailId', 'machineDetails', 'machineDetailId', 'CASCADE', 'CASCADE');
        $this->forge->createTable('machineSetup');
    }

    public function down()
    {
        $this->forge->dropTable('machineSetup', true);
    }
}
