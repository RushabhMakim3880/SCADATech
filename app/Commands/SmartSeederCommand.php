<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\App;

class SmartSeederCommand extends BaseCommand
{
    protected $group       = 'Generators';
    protected $name        = 'make:smartseeder';
    protected $description = 'Automatically creates Seeder with sample data based on table structure';

    public function run(array $params)
    {
        helper('inflector');

        $db = db_connect();

        // 1. Ask table name
        $table = CLI::prompt('Enter MySQL table name');

        if (!$db->tableExists($table)) {
            CLI::error("Table '$table' does not exist.");
            return;
        }

        $priority = CLI::prompt('Enter priority (default: 100)', null, 'required');

        if (!is_numeric($priority)) {
            $priority = 100;
        } else {
            $priority = (int) $priority;
        }

        // 2. Ask module from Modules/Backend
        $modulesPath = ROOTPATH . 'Modules/Backend/';
        $modules = array_values(array_filter(scandir($modulesPath), fn($dir) => is_dir($modulesPath . $dir) && !in_array($dir, ['.', '..'])));

        $module = CLI::prompt('Select a module', $modules);

        if (!in_array($module, $modules)) {
            CLI::error("Invalid module selected.");
            return;
        }

        $seedPath = $modulesPath . $module . '/Database/Seeds/';
        if (!is_dir($seedPath)) {
            mkdir($seedPath, 0755, true);
        }

        $fields = $db->getFieldData($table);

        $foreignKeys = $db->query("
            SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = '{$table}'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ")->getResultArray();

        $fkMap = [];
        foreach ($foreignKeys as $fk) {
            $fkMap[$fk['COLUMN_NAME']] = [
                'table'  => $fk['REFERENCED_TABLE_NAME'],
                'column' => $fk['REFERENCED_COLUMN_NAME'],
            ];
        }

        // Ensure foreign tables have tenantId=1 records
        foreach ($fkMap as $fk) {
            $hasTenant = $db->fieldExists('tenantId', $fk['table']);
            $query = $db->table($fk['table']);
            if ($hasTenant) {
                $query->where('tenantId', 1);
            }
            $count = $query->countAllResults();

            if ($count < 1) {
                CLI::error("Foreign table '{$fk['table']}' has no records for tenantId = 1. Please create its seeder or insert sample data.");
                return;
            }
        }

        $records = [];
        $sampleCount = 5;

        foreach (range(1, $sampleCount) as $i) {
            $row = ['tenantId' => 1];

            foreach ($fields as $field) {
                $name = $field->name;
                $type = strtolower($field->type);

                if ($field->primary_key) continue;
                if ($name === 'tenantId') continue;

                // Foreign key logic
                if (isset($fkMap[$name])) {
                    $fkTable  = $fkMap[$name]['table'];
                    $fkColumn = $fkMap[$name]['column'];

                    $fkQuery = $db->table($fkTable)->select($fkColumn);
                    if ($db->fieldExists('tenantId', $fkTable)) {
                        $fkQuery->where('tenantId', 1);
                    }
                    $fkIds = $fkQuery->get()->getResultArray();

                    $row[$name] = $fkIds[($i - 1) % count($fkIds)][$fkColumn];
                    continue;
                }

                // Smart field-type-based data
                if (in_array($type, ['tinyint', 'boolean'])) {
                    $row[$name] = (stripos($name, 'deleted') !== false) ? 0 : 1;
                } elseif (preg_match('/int/', $type)) {
                    $row[$name] = rand(1, 10);
                } elseif (in_array($type, ['decimal'])) {
                    $row[$name] = number_format(rand(100, 1000) / 10, 2);
                } elseif (in_array($type, ['date'])) {
                    $row[$name] = date('Y-m-d');
                } elseif (in_array($type, ['datetime', 'timestamp'])) {
                    $row[$name] = date('Y-m-d H:i:s');
                } elseif (in_array($type, ['time'])) {
                    $row[$name] = date('H:i:s');
                } elseif ($this->isEnumColumn($db, $table, $name)) {
                    $row[$name] = $this->getRandomEnumValue($db, $table, $name);
                } elseif (in_array($type, ['json'])) {
                    $row[$name] = json_encode([]);
                } elseif (in_array($type, ['text', 'mediumtext', 'longtext'])) {
                    $row[$name] = "Sample Text for $name";
                } elseif (in_array($type, ['varchar', 'char'])) {
                    if (preg_match('/mobile|contact|phone|whatsapp/i', $name)) {
                        $row[$name] = '9' . rand(100000000, 999999999);
                    } else {
                        $row[$name] = "Sample " . printable($name) . " $i";
                    }
                } else {
                    $row[$name] = "Sample " . printable($name) . " $i";
                }
            }

            $records[] = $row;
        }

        $seederClass = ucfirst(camelize($table)) . 'Seeder';
        $seederFile  = $seedPath . $seederClass . '.php';

        $content = "<?php

namespace Modules\\Backend\\$module\\Database\\Seeds;

use CodeIgniter\\Database\\Seeder;

class $seederClass extends Seeder
{
    public \$priority = $priority;

    public function run()
    {

        \$seedName = static::class;
        \$exists = \$this->db->table('seedHistory')->where('seedName', \$seedName)->countAllResults();
        if (\$exists > 0) {
            return;
        }


        \$data = " . var_export($records, true) . ";

        \$this->db->table('$table')->insertBatch(\$data);

        // Record this seeder in seedHistory
        \$this->db->table(\"seedHistory\")->insert(['seedName' => \$seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
";
        file_put_contents($seederFile, $content);
        CLI::write("Seeder created: $seederFile", 'green');
    }

    private function isEnumColumn($db, $table, $column): bool
    {
        $enumRow = $db->query("
            SELECT COLUMN_TYPE 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = '{$table}' 
            AND COLUMN_NAME = '{$column}'
        ")->getRow();

        return $enumRow && stripos($enumRow->COLUMN_TYPE, 'enum(') !== false;
    }

    private function getRandomEnumValue($db, $table, $column)
    {
        $enumRow = $db->query("
            SELECT COLUMN_TYPE 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = '{$table}' 
            AND COLUMN_NAME = '{$column}'
        ")->getRow();

        if ($enumRow && preg_match("/^enum\((.*)\)$/i", $enumRow->COLUMN_TYPE, $matches)) {
            $options = array_map(fn($v) => trim($v, " '"), explode(',', $matches[1]));
            return $options[array_rand($options)];
        }

        return null;
    }
}
