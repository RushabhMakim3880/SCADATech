<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class FeatureRegistry extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'featureKey'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'groupKey'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'label'          => ['type' => 'VARCHAR', 'constraint' => 255],
            'description'    => ['type' => 'TEXT', 'null' => true],
            'dataType'       => ['type' => 'ENUM', 'constraint' => ['string', 'text', 'int', 'float', 'bool', 'json', 'list'], 'default' => 'string'],
            'inputType'      => ['type' => 'ENUM', 'constraint' => ['text', 'textarea', 'number', 'select', 'checkbox', 'radio', 'switch'], 'default' => 'text'],
            'options'        => ['type' => 'TEXT', 'null' => true],
            'defaultValue'   => ['type' => 'TEXT', 'null' => true],
            'isCustomizable' => ['type' => 'BOOLEAN', 'default' => true],
            'isVisible'      => ['type' => 'BOOLEAN', 'default' => true],
            'isJsSide'       => ['type' => 'BOOLEAN', 'default' => false],
            'sortOrder'      => ['type' => 'INT', 'default' => 0],
        ]);
        $this->forge->addKey('featureKey', true);
        $this->forge->addForeignKey('groupKey', 'settingGroups', 'groupKey', 'CASCADE', 'CASCADE');
        $this->forge->createTable('featureRegistry');
    }

    public function down()
    {
        $this->forge->dropTable('featureRegistry');
    }
}
