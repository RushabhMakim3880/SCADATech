<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class FKinStatusMaster extends Migration
{
    public function up()
    {
        $this->forge->addForeignKey('departmentId', 'departmentMaster', 'departmentId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('moduleId', 'moduleMaster', 'moduleId', 'CASCADE', 'CASCADE');

        $this->forge->processIndexes('statusMaster');
    }

    public function down()
    {
        $this->forge->dropForeignKey('statusMaster', 'statusMaster_departmentId_foreign');
        $this->forge->dropForeignKey('statusMaster', 'statusMaster_moduleId_foreign');
    }
}
