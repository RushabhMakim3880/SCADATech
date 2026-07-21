<?php

namespace Modules\Backend\productionMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductionUnits extends Migration
{
    public function up()
    {
        //unitId
        // tenantId
        // serialNo
        // itemRecipeId
        // jobId	
        // mode (Semi, Auto)
        // producedAt
        // producedBy

        $this->forge->addField([
            'unitId' => [
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
            'jobId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'mode' => [
                'type'       => "ENUM('Semi', 'Auto')",
                'default'    => 'Semi',
            ],
            'producedAt' => [
                'type'       => 'DATETIME',
                'null'       => false,
            ],
            'producedBy' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ]
        ]);


        //add primary index
        $this->forge->addKey('unitId', true);

        //add foreign key
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('itemRecipeId', 'itemRecipeMaster', 'itemRecipeId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('jobId', 'productionJobCards', 'jobId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('producedBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');

        //create table
        $this->forge->createTable('productionUnits');
    }

    public function down()
    {
        //drop table
        $this->forge->dropTable('productionUnits', true);
    }
}
