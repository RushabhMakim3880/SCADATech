<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeRefreshTokens extends Migration
{
    public function up()
    {
        //add new field to refreshTokens table
        $fields = [
            'deviceToken'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ];
        $this->forge->addColumn('refreshTokens', $fields);

        //add foreign key constraint to refreshTokens table
        $this->forge->addForeignKey('deviceToken', 'trustedDevices', 'deviceToken', 'CASCADE', 'CASCADE');

        $this->forge->processIndexes('refreshTokens');
    }


    public function down()
    {
        //remove foreign key constraint from refreshTokens table
        $this->forge->dropForeignKey('refreshTokens', 'refreshTokens_deviceToken_foreign');

        //remove deviceToken field from refreshTokens table
        $this->forge->dropColumn('refreshTokens', 'deviceToken');
    }
}
