<?php

namespace Modules\Backend\PlcMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFkMachineOperationConfig extends Migration
{
     public function up()
    {
        $this->forge->addForeignKey('machineId', 'machineMaster', 'machineId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('plcTriggerTag', 'plcTagMaster', 'tagId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('plcAckTag', 'plcTagMaster', 'tagId', 'CASCADE', 'RESTRICT');
        $this->forge->processIndexes('machineOperationConfig');
    }

    public function down()
    {
         $this->forge->dropForeignKey('machineOperationConfig', 'machineOperationConfig_machineId_foreign');
         $this->forge->dropForeignKey('machineOperationConfig', 'machineOperationConfig_plcTriggerTag_foreign');
         $this->forge->dropForeignKey('machineOperationConfig', 'machineOperationConfig_plcAckTag_foreign');
    }
}
