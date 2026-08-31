<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFieldControlsTable extends Migration
{
    public function up()
    {

        $this->forge->addField([
            'fieldId' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tenantId' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'moduleName' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'fieldKey' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'isVisible' => [
                'type'    => 'TINYINT',
                'default' => 1,
            ],
            'isMasked' => [
                'type'    => 'TINYINT',
                'default' => 0,
            ],
            'isRequired' => [
                'type'    => 'TINYINT',
                'default' => 0,
            ],
            'maskedFor' => [
                'type'    => 'VARCHAR',
                'constraint' => 255,
                'null'    => true,
                'default' => null,
            ],
            'createdAt' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
            'updatedAt' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
            'createdBy' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'updatedBy' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ]
        ]);

        $this->forge->addKey('fieldId', true);


        // Add foreign key constraint for tenantId
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        // Add foreign key constraint for createdBy
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'CASCADE', 'SET NULL');
        // Add foreign key constraint for updatedBy
        $this->forge->addForeignKey('updatedBy', 'userMaster', 'userId', 'CASCADE', 'SET NULL');

        $this->forge->createTable('fieldControls', true);
    }

    public function down()
    {
        $this->forge->dropTable('fieldControls', true);
    }
}
