<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSerialNumbersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'serialId' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tenantId' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, "null" => true],
            'tableName' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'serialNumber' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'default'    => 0,
            ],
            'updatedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'createdAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('serialId', true);
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');

        $this->forge->createTable('serialNumbers');
    }

    public function down()
    {
        $this->forge->dropTable('serialNumbers');
    }
}
