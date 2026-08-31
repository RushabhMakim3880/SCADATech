<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class LocationMaster extends Migration
{
    public function up()
    {
        //locationId, locationName, locationType (Enum: Country, State, District, Taluka), parentLocationId
        $this->forge->addField([
            'locationId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'locationName' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'locationType' => [
                'type' => 'ENUM',
                'constraint' => ['Country', 'State', 'District', 'Taluka'],
                'default' => 'Country',
            ],
            'parentLocationId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('locationId', true);
        $this->forge->addForeignKey('parentLocationId', 'locationMaster', 'locationId', 'CASCADE', 'CASCADE');
        $this->forge->createTable('locationMaster');
    }

    public function down()
    {
        $this->forge->dropTable('locationMaster');
    }
}
