<?php

namespace Modules\Backend\OpMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgramCycleOperations extends Migration
{
    public function up()
    {
        //operationId
        // tenantId
        // serialNo
        // cycleDetailsId
        // itemRecipeStepId

        $this->forge->addField([
            'operationId' => [
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
            'cycleDetailsId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'itemRecipeStepId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ]
        ]);

        //add primary index
        $this->forge->addKey('operationId', true);

        //add foreign key
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('cycleDetailsId', 'programCycleDetails', 'cycleDetailsId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('itemRecipeStepId', 'itemRecipeSteps', 'itemRecipeStepId', 'CASCADE', 'CASCADE');

        $this->forge->createTable('programCycleOperations');
    }

    public function down()
    {
        //drop table
        $this->forge->dropTable('programCycleOperations', true);
    }
}
