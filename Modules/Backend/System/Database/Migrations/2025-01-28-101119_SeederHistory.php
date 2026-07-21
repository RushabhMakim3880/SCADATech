<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeederHistory extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'seedId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'seedName' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'runAt' => [
                'type' => 'DATETIME',
                'null' => false,
                'default' => '0000-00-00 00:00:00',
            ],
        ]);
        $this->forge->addKey('seedId', true);
        $this->forge->createTable('seedHistory');
    }

    public function down()
    {
        $this->forge->dropTable('seedHistory');
    }
}
