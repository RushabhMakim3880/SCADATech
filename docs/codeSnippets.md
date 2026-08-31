# Code Snippets 

A ready refference code pieces to copy/paste directly from here for faster development.

## Migration Code

### Create New Migration File.

**filename** = create_tableName, edit_tableName, clearly tell what you are doing.  
**moduleName** = name of your folder inside Modules/backend/ in which migration file belongs to.

```bash
php spark make:migration filename --namespace Modules\\Backend\\moduleName
```

for faster development you can always copy below part to paste into your migration file, make sure to add/remove any field that is not required.

```php
public function up()
    {
        $this->forge->addField([
            'primaryId' => [ //change this primary key
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
            [
                //add more fileds here
            ],
            'isActive' => [ //make sure to keep or remove this.
                'type'  => 'BOOLEAN',
                'null'  => false,
                'default'   => false,
            ],
            'isDeleted' => [ //make sure to keep or remove this.
                'type'  => 'BOOLEAN',
                'null'  => false,
                'default'   => false,
            ],
            'updatedAt' => [
                'type' => 'DATETIME',
                'null' => false,
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
        $this->forge->addKey('primaryId', true);
        
        //add normal indexes
        $this->forge->addKey('isDeleted');

        //add foreign key
        $this->forge->addForeignKey('tenantId', 'tenantMaster', 'tenantId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('updatedBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('createdBy', 'userMaster', 'userId', 'CASCADE', 'RESTRICT');

        //finally create table
        $this->forge->createTable('quotationMaster');
    }

    public function down()
    {
        //to delete table on reverse or down grade.
        $this->forge->dropTable('quotationMaster');
    }
```


### Manage FK in Seperate Migration File.

> To avoid migration run timing issue due to FK constraits, lets keep all migration files of table creation for tables and index definationonly and fk addition as seperate files.  

> In future if we again add more FK, we will create new migratino file for that, avoid editing existing files.

**filename** = add-fk-tableName (clearly says, that you are adding fk only)  
**moduleName** = name of your folder inside Modules/backend/ in which migration file belongs to.

```bash
php spark make:migration add-fk-tableName --namespace Modules\\Backend\\moduleName
```


```php
public function up()
{
    //disable checking for existing data match to avoid getting error
    $this->db->disableForeignKeyChecks();

    // Add foreign key for customerId → customerMaster.customerId
    $this->forge->addForeignKey('customerId', 'customerMaster', 'customerId', 'CASCADE', 'CASCADE');
    $this->forge->addForeignKey('salesPersonId', 'employeeMaster', 'employeeId', 'SET NULL', 'CASCADE');
    $this->forge->processIndexes('orderMaster'); // Call once per table after all FK additions

    //re enable fk checks.
    $this->db->enableForeignKeyChecks();
}

public function down()
{
    //disable checking for existing data match to avoid getting error
    $this->db->disableForeignKeyChecks();

    // Drop foreign keys (use actual FK names if specified, or let CI4 auto-name based on convention)
    $this->forge->dropForeignKey('orderMaster', 'orderMaster_customerId_foreign');
    $this->forge->dropForeignKey('orderMaster', 'orderMaster_salesPersonId_foreign');

    //re enable fk checks.
    $this->db->enableForeignKeyChecks();
}
```