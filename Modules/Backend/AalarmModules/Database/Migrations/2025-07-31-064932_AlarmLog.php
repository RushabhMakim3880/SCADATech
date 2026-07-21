<?php

namespace Modules\Backend\AalarmModules\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlarmLog extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'logId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tenantId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'serialNo' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'alarmId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'uiTagId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'alarmType' => [
                'type'       => 'ENUM',
                'constraint' => ['lo', 'lolo', 'hi', 'hihi'],
                'null'       => true,
            ],
            'triggerValue' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'triggerTime' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'resolveTime' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'isResolved' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
            ],
        ]);

        //add primary index
        $this->forge->addKey('logId', true);


        //add foreign key
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('alarmId', 'AlarmConfig', 'alarmId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('uiTagId', 'uiTagMaster', 'uiTagId', 'CASCADE', 'RESTRICT');

        $this->forge->createTable('AlarmLog');
    }

    public function down()
    {
        $this->forge->dropTable('AlarmLog');
    }
}
