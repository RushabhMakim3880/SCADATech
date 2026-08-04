<?php

namespace Modules\Backend\Setting\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCompanyMasterSettings extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'companySettingsId' => [
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
            'serialNo' => [
                'type'     => 'INT',
                'unsigned' => true,
                'default'  => 0,
            ],
            'companyId' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 1,
            ],
            'key' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => false,
            ],
            'value' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('companySettingsId', true);
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->createTable('companyMasterSettings', true);
    }

    public function down()
    {
        $this->forge->dropTable('companyMasterSettings', true);
    }
}
