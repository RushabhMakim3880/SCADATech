<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class TenantPlans extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'tenantId'  => ['type' => 'INT', 'unsigned' => true],
            'planId'    => ['type' => 'INT', 'unsigned' => true],
            'startDate' => ['type' => 'DATE', 'null' => true],
            'endDate'   => ['type' => 'DATE', 'null' => true],
            'isTrial'   => ['type' => 'BOOLEAN', 'default' => false],
        ]);
        $this->forge->addKey('tenantId', true);
        $this->forge->addForeignKey('planId', 'productPlans', 'planId', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tenantPlans');
    }

    public function down()
    {
        $this->forge->dropTable('tenantPlans');
    }
}
