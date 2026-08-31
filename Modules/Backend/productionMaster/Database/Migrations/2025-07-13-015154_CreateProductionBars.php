<?php

namespace Modules\Backend\productionMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductionBars extends Migration
{
    public function up()
    {
        // barId
        // tenantId
        // serialNo
        // cycleId
        // barLength

        $this->forge->addField([
            'barId' => [
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
            'cycleId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'barLength' => [
                'type'       => 'FLOAT',
                'null'       => false,
            ]
        ]);

        //add primary index
        $this->forge->addKey('barId', true);

        //add foreign key
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('cycleId', 'programCycleMaster', 'cycleId', 'CASCADE', 'CASCADE');

        $this->forge->createTable('productionBars');
    }

    public function down()
    {
        //drop table
        $this->forge->dropTable('productionBars', true);
    }
}
