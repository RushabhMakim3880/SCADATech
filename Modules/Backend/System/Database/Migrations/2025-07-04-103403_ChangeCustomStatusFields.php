<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeCustomStatusFields extends Migration
{
    public function up()
    {
        //change fieldType in customStatusFields table
        $fields = [
            'fieldType' => [
                'type' => 'ENUM',
                'constraint' => ['text', 'select', 'number', 'date', 'checkbox', 'radio', 'textarea'],
                'default' => 'text',
                'null' => false,
            ]
        ];
        $this->forge->modifyColumn('customStatusFields', $fields);
    }

    public function down()
    {
        //
    }
}
