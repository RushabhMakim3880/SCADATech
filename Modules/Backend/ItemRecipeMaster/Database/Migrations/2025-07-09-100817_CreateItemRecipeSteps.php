<?php

namespace Modules\Backend\ItemRecipeMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateItemRecipeSteps extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'itemRecipeStepId' => [
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
            'itemRecipeId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'opType' => [
                'type'       => 'ENUM',
                'constraint' => ['Punching', 'Marking', 'Cutting'],
                'default'    => 'Punching',
                'null'       => false,
            ],
            'side' => [
                'type'       => 'ENUM',
                'constraint' => ['N/A', 'A', 'B'],
                'default'    => 'N/A',
                'null'       => false,
            ],
            'opValue' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'xPos' => [
                'type'       => 'DECIMAL',
                'constraint' => '18,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'yPos' => [
                'type'       => 'DECIMAL',
                'constraint' => '18,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'measurementType' => [
                'type'       => 'ENUM',
                'constraint' => ['Absolute', 'Incremental'],
                'default'    => 'Absolute',
                'null'       => false,
            ]

        ]);

        //add primary index
        $this->forge->addKey('itemRecipeStepId', true);

        //add foreign key
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');

        $this->forge->createTable('itemRecipeSteps');
    }

    public function down()
    {
        $this->forge->dropTable('itemRecipeSteps');
    }
}
