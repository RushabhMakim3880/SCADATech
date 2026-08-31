<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuoteMasterTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'quoteId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tenantId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'quotes' => [
                'type' => 'LONGTEXT',
                'null' => false,
            ],
            'author' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'group' => [
                'type' => 'ENUM',
                'constraint' => ['admin', 'staff'],
                'null' => false,
            ],
        ]);

        $this->forge->addKey('quoteId', true); // Primary key

        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('quoteMaster');
    }

    public function down()
    {
        $this->forge->dropTable('quoteMaster');
    }
}
