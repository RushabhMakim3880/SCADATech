<?php

namespace Modules\Backend\PlcMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class PlcRuntimeLogTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'plcRuntimeLogId' => [
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
            'tagId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],

            'operationType' => [
                'type'       => 'ENUM',
                'constraint' => ['read', 'write'],
                'null'       => false,
            ],

            'value' => [
                'type'    => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],

            'responseTimeMs' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'comment'    => 'Response time in milliseconds',
            ],

            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['success', 'timeout', 'error'],
                'null'       => false,
            ],

            'errorMessage' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'triggeredBy' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => "e.g., 'nodejs-engine', 'manual-ui'",
            ],

            'createdAt' => [
                'type' => 'DATETIME',
                'null' => false,
            ],


        ]);

        $this->forge->addKey('plcRuntimeLogId', true); // Primary key

        // add foreign keys
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('plcId', 'plcMaster', 'plcId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tagId', 'plcTagMaster', 'tagId', 'CASCADE', 'CASCADE');


        $this->forge->createTable('plcRuntimeLog');
    }

    public function down()
    {
        $this->forge->dropTable('plcRuntimeLog');
    }
}
