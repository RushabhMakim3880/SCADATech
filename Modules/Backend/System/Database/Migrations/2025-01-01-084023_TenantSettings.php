<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class TenantSettings extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'tenantId'   => ['type' => 'INT', 'unsigned' => true],
            'featureKey' => ['type' => 'VARCHAR', 'constraint' => 100],
            'value'      => ['type' => 'TEXT', 'null' => true],
            'updatedAt'  => ['type' => 'DATETIME', null => true],
        ]);
        $this->forge->addKey(['tenantId', 'featureKey'], true);
        $this->forge->addForeignKey('featureKey', 'featureRegistry', 'featureKey', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tenantSettings');
    }

    public function down()
    {
        $this->forge->dropTable('tenantSettings');
    }
}
