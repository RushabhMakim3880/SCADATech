<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMenuConfig extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'menuConfigId' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tenantId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'orderId' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null' => true
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => '250',
                'null' => true
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => '250',
                'null' => true
            ],
            'class' => [
                'type'       => 'VARCHAR',
                'constraint' => '250',
                'null' => true
            ],
            'module' => [
                'type'       => 'VARCHAR',
                'constraint' => '250',
                'null' => true
            ],
            'permissions' => [
                'type'       => 'VARCHAR',
                'constraint' => '250',
                'null' => true
            ],
            'attributes' => [
                'type'       => 'VARCHAR',
                'constraint' => '250',
                'null' => true
            ],
            'url' => [
                'type'       => 'VARCHAR',
                'constraint' => '250',
                'null' => true
            ],
            'parentId' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'default'    => 0,
                'null' => true

            ],
            'menuLocation' => [
                'type'       => 'ENUM',
                'constraint' => ['sidebarMain', 'mobileBottom'],
                'default'    => 'sidebarMain',
                'null' => false
            ],
            'isPopup' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'isActive' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null' => true

            ],
            'isDeleted' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null' => true

            ],
            'updatedBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'updatedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'createdBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'createdAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('menuConfigId', true);
        $this->forge->addForeignKey('updatedBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');

        $this->forge->addKey('parentId');
        $this->forge->addKey('orderId');

        $this->forge->createTable('menuConfig');
    }

    public function down()
    {
        $this->forge->dropTable('menuConfig');
    }
}
