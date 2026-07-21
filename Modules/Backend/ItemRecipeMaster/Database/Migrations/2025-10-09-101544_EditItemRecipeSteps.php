<?php

namespace Modules\Backend\ItemRecipeMaster\Database\Migrations;

use CodeIgniter\Database\Migration;

class EditItemRecipeSteps extends Migration
{
    public function up()
    {
        // add new field ordId to itemRecipeSteps after itemRecipeId
        $fields = [
            'ordId' => [
                'type' => 'INT',
                'constraint' => 11,
                'after' => 'itemRecipeId',
                'null' => false,
                'default' => 0,
                'unsigned' => true,
            ],
        ];
        $this->forge->addColumn('itemRecipeSteps', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('itemRecipeSteps', 'ordId');
    }
}
