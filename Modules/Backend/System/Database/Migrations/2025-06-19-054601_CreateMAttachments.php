<?php

namespace Modules\Backend\System\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMAttachments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'attachmentId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'tenantId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'serialNo' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => false,
            ],
            'filePath' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => false,
            ],
            'size' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'parentType' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'parentId' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'childId' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'isDeleted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
            ],
            'extension' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
            ],
            'documentType' => [
                'type' => 'ENUM',
                'constraint' => ['image', 'document', 'media', 'archive', 'other'],
                'null' => false,
            ],
            'createdBy' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'uploadTime' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'deletedTime' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('attachmentId', true); // Primary key
        $this->forge->createTable('mAttachments');

        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->forge->dropTable('mAttachments');
    }
}
