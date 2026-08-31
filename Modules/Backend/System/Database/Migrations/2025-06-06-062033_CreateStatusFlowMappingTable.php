<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStatusFlowMappingTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'statusMappingId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'statusId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'dependsOn' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'passedThrough' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'permisionRequired' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'roleRequired' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('statusMappingId', true);

        $this->forge->addForeignKey('statusId', 'statusMaster', 'statusId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('dependsOn', 'statusMaster', 'statusId', 'CASCADE', 'RESTRICT');

        $this->forge->createTable('statusFlowMapping');
    }

    public function down()
    {
        $this->forge->dropTable('statusFlowMapping');
    }
}
