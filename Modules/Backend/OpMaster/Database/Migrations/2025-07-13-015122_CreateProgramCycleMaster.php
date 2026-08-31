<?php

namespace Modules\Backend\OpMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgramCycleMaster extends Migration
{
    public function up()
    {

        $this->forge->addField([
            'cycleId' => [
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
            'startedOn' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'completedOn' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'executedBy' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'machineSetup' => [
                'type'       => 'JSON',
                'null'       => false,
            ],
            'updatedAt' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updatedBy' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'createdAt' => [
                'type'       => 'DATETIME',
                'null'       => false,
            ],
            'createdBy' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ]
        ]);

        //add primary index
        $this->forge->addKey('cycleId', true);
        //add foreign key
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('executedBy', 'userMaster', 'userId',  'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('updatedBy', 'userMaster', 'userId',  'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId',  'CASCADE', 'RESTRICT');
        $this->forge->createTable('programCycleMaster');
    }

    public function down()
    {
        $this->forge->dropTable('programCycleMaster', true);
    }
}
