<?php

namespace Modules\Backend\OpMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgramCycleDetails extends Migration
{
    public function up()
    {
        //cycleDetailsId
        // tenantId
        // serialNo
        // cycleId
        // itemRecipeId
        // quantity (int)

        $this->forge->addField([
            'cycleDetailsId' => [
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
            'itemRecipeId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'quantity' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ]
        ]);

        //add primary index
        $this->forge->addKey('cycleDetailsId', true);

        //add foreign key
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('cycleId', 'programCycleMaster', 'cycleId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('itemRecipeId', 'itemRecipeMaster', 'itemRecipeId', 'CASCADE', 'CASCADE');

        //create table
        $this->forge->createTable('programCycleDetails');
    }

    public function down()
    {
        //drop table
        $this->forge->dropTable('programCycleDetails', true);
    }
}
