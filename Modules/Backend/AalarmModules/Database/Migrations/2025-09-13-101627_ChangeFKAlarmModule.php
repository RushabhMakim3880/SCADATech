<?php

namespace Modules\Backend\AalarmModules\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeFKAlarmModule extends Migration
{
    public function up()
    {
        //in AlarmConfig table forgeign key uiTagId to reference uiTagMaster table, replace from RESTRICT to CASCADE on delete, using forge.
        $this->forge->dropForeignKey('AlarmConfig', 'AlarmConfig_uiTagId_foreign');
        $this->forge->addForeignKey('uiTagId', 'uiTagMaster', 'uiTagId', 'CASCADE', 'CASCADE');
        $this->forge->processIndexes('AlarmConfig');

        //in AlarmLog table forgeign key uiTagId to reference uiTagMaster table, replace from RESTRICT to CASCADE on delete, using forge.
        $this->forge->dropForeignKey('AlarmLog', 'AlarmLog_uiTagId_foreign');
        $this->forge->dropForeignKey('AlarmLog', 'AlarmLog_alarmId_foreign');


        $this->forge->addForeignKey('uiTagId', 'uiTagMaster', 'uiTagId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('alarmId', 'AlarmConfig', 'alarmId', 'CASCADE', 'CASCADE');
        $this->forge->processIndexes('AlarmLog');
    }

    public function down()
    {
        //in AlarmConfig table forgeign key uiTagId to reference uiTagMaster table, replace from CASCADE to RESTRICT on delete, using forge.
        $this->forge->dropForeignKey('AlarmConfig', 'AlarmConfig_uiTagId_foreign');
        $this->forge->addForeignKey('uiTagId', 'uiTagMaster', 'uiTagId', 'RESTRICT', 'RESTRICT');
        $this->forge->processIndexes('AlarmConfig');

        //in AlarmLog table forgeign key uiTagId to reference uiTagMaster table, replace from CASCADE to RESTRICT on delete, using forge.
        $this->forge->dropForeignKey('AlarmLog', 'AlarmLog_uiTagId_foreign');
        $this->forge->dropForeignKey('AlarmLog', 'AlarmLog_alarmId_foreign');

        $this->forge->addForeignKey('uiTagId', 'uiTagMaster', 'uiTagId', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('alarmId', 'AlarmConfig', 'alarmId', 'RESTRICT', 'RESTRICT');

        // processIndex table

        $this->forge->processIndexes('AlarmLog');
    }
}
