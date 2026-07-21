<?php

namespace Modules\Backend\OpMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductionMaster extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'productionId' => [
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
            'programId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'jobId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'quantityProduced' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'startedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'completedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'userId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ]
        ]);

        //add primary index
        $this->forge->addKey('productionId', true);

        //add foreign key
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('programId', 'programAlignMaster', 'programId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('jobId', 'productionJobCards', 'jobId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('userId', 'userMaster', 'userId', 'SET NULL', 'CASCADE');

        $this->forge->createTable('productionMaster');
    }

    public function down()
    {
        $this->forge->dropTable('productionMaster');
    }
}
