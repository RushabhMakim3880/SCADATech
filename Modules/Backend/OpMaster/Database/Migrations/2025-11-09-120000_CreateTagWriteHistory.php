<?php

namespace Modules\Backend\OpMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTagWriteHistory extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'tagId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'comment' => 'FK to uiTagMaster.uiTagId',
            ],
            'value' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'userId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'writeTime' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('tagId');
        $this->forge->addKey('userId');
        $this->forge->addKey('writeTime');

        $this->forge->createTable('tagWriteHistory');
    }
    public function down()
    {
        $this->forge->dropTable('tagWriteHistory');
    }
}
