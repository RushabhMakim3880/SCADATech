<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDepartmentMaster extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'departmentId' => [ //change this primary key
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
            'departmentName' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'updatedBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'createdAt' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'createdBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
        ]);

        //add primary index
        $this->forge->addKey('departmentId', true);


        //add foreign key
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('updatedBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');

        //finally create table
        $this->forge->createTable('departmentMaster');
    }

    public function down()
    {
        $this->forge->dropTable('departmentMaster');
    }
}
