<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingGroups extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'groupKey'    => ['type' => 'VARCHAR', 'constraint' => 100],
            'label'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT', 'null' => true],
            'icon'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'visibility'  => ["type" => "ENUM", "constraint" => ['all', 'tenantOnly', 'saasOnly'], "default" => 'all'],
            'sortOrder'   => ['type' => 'INT', 'default' => 0],
            'isActive'    => ['type' => 'BOOLEAN', 'default' => true],
        ]);
        $this->forge->addKey('groupKey', true);
        $this->forge->createTable('settingGroups');
    }

    public function down()
    {
        $this->forge->dropTable('settingGroups');
    }
}
