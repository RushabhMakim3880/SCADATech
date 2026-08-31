<?php

namespace Modules\Backend\Setting\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTenantCompanySettings extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'tenantSettingId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tenantId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'key' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => false,
            ],
            'value' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => false,
            ],
        ]);

        $this->forge->addKey('tenantSettingId', true);

        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tenantCompanySettings');
    }

    public function down()
    {
        $this->forge->dropTable('tenantCompanySettings');
    }
}
