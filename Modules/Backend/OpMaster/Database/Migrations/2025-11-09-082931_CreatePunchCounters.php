<?php

namespace Modules\Backend\OpMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePunchCounters extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'punchId' => [
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
            'itemRecipeId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'punchCount' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'startHour' => [
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
        $this->forge->addKey('punchId', true);
        //add foreign key
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('userId', 'userMaster', 'userId', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('programId', 'programAlignMaster', 'programId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('itemRecipeId', 'itemRecipeMaster', 'itemRecipeId', 'CASCADE', 'CASCADE');

        $this->forge->createTable('punchCounters');
    }

    public function down()
    {
        $this->forge->dropTable('punchCounters');
    }
}
