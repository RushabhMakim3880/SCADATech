<?php

namespace Modules\Backend\jobCards\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductionJobCards extends Migration
{
    public function up()
    {
        //jobId
        // tenantId
        // serialNo
        // itemRecipeId
        // requiredQuantity
        // completedQuantity
        // status: enum (waiting, started, completed, cancelled, partiallyCompleted)
        // startedAt,
        // completedAt,
        // updatedAt,
        // updatedBy,
        // createdAt,
        // createdBy.

        $this->forge->addField([
            'jobId' => [
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
            'requiredQuantity' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'completedQuantity' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'status' => [
                'type'       => "ENUM('waiting', 'started', 'completed', 'cancelled', 'partiallyCompleted')",
                'default'    => 'waiting',
            ],
            'startedAt' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'completedAt' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updatedAt' => [
                'type'       => "DATETIME",
                'null'       => true,
            ],
            'updatedBy' => [
                'type'       => "INT",
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'createdAt' => [
                'type'       => "DATETIME",
                'null'       => true,
            ],
            'createdBy' => [
                'type'       => "INT",
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        //add primary index
        $this->forge->addKey('jobId', true);

        //add foreign key
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('itemRecipeId', 'itemRecipeMaster', 'itemRecipeId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('updatedBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');

        $this->forge->createTable('productionJobCards');
    }

    public function down()
    {
        //drop table
        $this->forge->dropTable('productionJobCards', true);
    }
}
