<?php

namespace Modules\Backend\OpMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePauseReasons extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'pauseReasonId' => [
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
            'label' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'isActive' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'unsigned' => true,
                'default' => 1,
            ],
            'createdAt' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'createdBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'updatedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updatedBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ]
        ]);

        //add primary index
        $this->forge->addKey('pauseReasonId', true);

        //add foreign key
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('updatedBy', 'userMaster', 'userId', 'SET NULL', 'CASCADE');

        $this->forge->createTable('pauseReasons');
    }

    public function down()
    {
        $this->forge->dropTable('pauseReasons');
    }
}
