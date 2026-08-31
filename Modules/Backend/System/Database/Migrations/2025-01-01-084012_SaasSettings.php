<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class SaasSettings extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'featureKey' => ['type' => 'VARCHAR', 'constraint' => 100],
            'value'      => ['type' => 'TEXT', 'null' => true],
            'updatedAt'  => ['type' => 'DATETIME', null => true],
        ]);
        $this->forge->addKey('featureKey', true);
        $this->forge->addForeignKey('featureKey', 'featureRegistry', 'featureKey', 'CASCADE', 'CASCADE');
        $this->forge->createTable('saasSettings');
    }

    public function down()
    {
        $this->forge->dropTable('saasSettings');
    }
}
