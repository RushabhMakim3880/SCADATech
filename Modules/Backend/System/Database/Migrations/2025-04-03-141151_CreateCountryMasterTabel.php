<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCountryMasterTabel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'countryId'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'locationId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'countryName' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'phoneCode' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'iso2Code' => [
                'type'       => 'VARCHAR',
                'constraint' => 2,
            ],
            'iso3Code' => [
                'type'       => 'VARCHAR',
                'constraint' => 3,
            ],
            'currencyCode' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'currencySymbol' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            "currencyName" => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'flagEmoji' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
            ]
        ]);


        $this->forge->addKey('countryId', true);
        $this->forge->addForeignKey('locationId', 'locationMaster', 'locationId', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('countryMaster');
    }

    public function down()
    {
        $this->forge->dropTable('countryMaster');
    }
}
