<?php

namespace Modules\Backend\PlcMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class PlcTagGroupMasterTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'tagGroupId' => [
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
            'plcId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'groupName' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'comment'    => 'e.g., "Punch Control", "Alarm Status"',
            ],
             'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
          
            'createdAt' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updatedAt' => [
                'type' => 'DATETIME',
                'null' => false,
            ],

        ]);

        $this->forge->addKey('tagGroupId', true); // Primary key

        // add foreign keys
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('plcId', 'plcMaster', 'plcId', 'CASCADE', 'CASCADE');


        $this->forge->createTable('plcTagGroupMaster');
    }

    public function down()
    {
        $this->forge->dropTable('plcTagGroupMaster');
    }
}
