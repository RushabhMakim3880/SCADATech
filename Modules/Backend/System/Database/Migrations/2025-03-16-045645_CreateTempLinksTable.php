<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTempLinksTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'auto_increment' => true, 'unsigned' => true],
            'token'       => ['type' => 'VARCHAR', 'constraint' => 12, 'unique' => true], // Short Unique Token
            'originalUrl' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false], // Target URL
            'payload'     => ['type' => 'JSON', 'null' => true], // Additional Data (JSON Encoded)
            'payloadHash' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true], // Hash of Payload
            'expiresAt'   => ['type' => 'DATETIME', 'null' => false], // Expiry Time
            'createdAt'   => ['type' => 'DATETIME', 'null' => false], // Creation Time
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('tempLinks');
    }

    public function down()
    {
        $this->forge->dropTable('tempLinks');
    }
}
