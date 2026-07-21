<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeCustomStatusActionData extends Migration
{
    public function up()
    {
        //add statusId field in customStatusActionData table 
        $fields = [
            'statusId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'followupHistoryId',
            ]
        ];
        $this->forge->addColumn('customStatusActionData', $fields);

        // add fk constraint for statusId
        $this->forge->addForeignKey('statusId', 'statusMaster', 'statusId', 'CASCADE', 'SET NULL');
        // $this->forge->addForeignKey('moduleId', 'moduleMaster', 'moduleId', 'CASCADE', 'CASCADE');
        $this->forge->processIndexes('customStatusActionData');
    }

    public function down()
    {
        //remove statusId field from customStatusActionData table
        $this->forge->dropForeignKey('customStatusActionData', 'customStatusActionData_statusId_foreign');
        $this->forge->dropForeignKey('customStatusActionData', 'customStatusActionData_moduleId_foreign');
        $this->forge->dropColumn('customStatusActionData', 'statusId');
    }
}
