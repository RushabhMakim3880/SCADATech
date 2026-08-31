<?php

namespace Modules\Backend\AalarmModules\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSolutionToAlarmConfig extends Migration
{
    public function up()
    {
        $fields = [
            'solution' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'message'
            ]
        ];
        $this->forge->addColumn('AlarmConfig', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('AlarmConfig', 'solution');
    }
}
