<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class PlanFeatures extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'planId'     => ['type' => 'INT', 'unsigned' => true],
            'featureKey' => ['type' => 'VARCHAR', 'constraint' => 100],
            'value'      => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey(['planId', 'featureKey'], true);
        $this->forge->addForeignKey('planId', 'productPlans', 'planId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('featureKey', 'featureRegistry', 'featureKey', 'CASCADE', 'CASCADE');
        $this->forge->createTable('planFeatures');
    }

    public function down()
    {
        $this->forge->dropTable('planFeatures');
    }
}
