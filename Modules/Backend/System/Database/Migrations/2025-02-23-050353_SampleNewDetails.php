<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class SampleNewDetails extends Migration
{
    public function up()
    {
        $this->forge->addField(
            [
                'sampleNewDetailId' => [
                    'type'           => 'INT',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'newSampleId' => [
                    'type'           => 'INT',
                    'unsigned'       => true,
                ],
                "itemId" => [
                    'type' => 'INT',
                    'unsigned' => true,
                ],
                "cityId" => [
                    'type'           => 'INT',
                    'unsigned'       => true,
                ],
                "discription" => [
                    'type' => 'VARCHAR',
                    'constraint' => '250',
                    'null' => true
                ],
                "districtId" => [
                    'type'           => 'INT',
                    'unsigned'       => true,
                ],
            ]
        );

        $this->forge->addKey('sampleNewDetailId', true);
        $this->forge->addForeignKey('newSampleId', 'newSampleTable', 'newSampleId', 'CASCADE', 'CASCADE');

        $this->forge->createTable('sampleNewDetails');
    }

    public function down()
    {
        $this->forge->dropTable('sampleNewDetails');
    }
}
