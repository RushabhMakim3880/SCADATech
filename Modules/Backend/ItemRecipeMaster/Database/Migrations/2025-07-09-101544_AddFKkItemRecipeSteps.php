<?php

namespace Modules\Backend\ItemRecipeMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFKkItemRecipeSteps extends Migration
{
    public function up()
    {
        $this->forge->addForeignKey('itemRecipeId', 'itemRecipeMaster', 'itemRecipeId', 'CASCADE', 'RESTRICT');
        // $this->forge->addForeignKey('operationConfigId', 'machineOperationConfig', 'operationConfigId', 'CASCADE', 'RESTRICT');
        $this->forge->processIndexes('itemRecipeSteps');
    }

    public function down()
    {
        $this->forge->dropForeignKey('itemRecipeSteps', 'itemRecipeSteps_itemRecipeId_foreign');
        // $this->forge->dropForeignKey('itemRecipeSteps', 'itemRecipeSteps_operationConfigId_foreign');
    }
}
