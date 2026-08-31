<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class ProductPlans extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'planId'     => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'description' => ['type' => 'TEXT', 'null' => true],
            'basePrice'  => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'isActive'   => ['type' => 'BOOLEAN', 'default' => true],
        ]);
        $this->forge->addKey('planId', true);
        $this->forge->createTable('productPlans');
    }

    public function down()
    {
        $this->forge->dropTable('productPlans');
    }
}
