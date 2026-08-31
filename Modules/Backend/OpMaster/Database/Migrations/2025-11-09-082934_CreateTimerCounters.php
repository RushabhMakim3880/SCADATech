<?php

namespace Modules\Backend\OpMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTimerCounters extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'timerId' => [
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
            'programId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'state' => [
                'type' => 'ENUM',
                'constraint' => ['RUNNING', 'PAUSED', 'IDLE', 'MANUAL_OP'],
                'null' => true,
            ],
            'startTime' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'endTime' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'durationInMinutes' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'reasonId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'userId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ]
        ]);

        //add primary index
        $this->forge->addKey('timerId', true);
        //add foreign key
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('userId', 'userMaster', 'userId', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('programId', 'programAlignMaster', 'programId', 'CASCADE', 'CASCADE');

        $this->forge->createTable('timerCounters');
    }

    public function down()
    {
        $this->forge->dropTable('timerCounters');
    }
}
