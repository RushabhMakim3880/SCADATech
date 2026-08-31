<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use Throwable;

class SeedAutoOrder extends BaseCommand
{
    protected $group       = 'Seeder';
    protected $name        = 'seed:order';
    protected $description = 'Outputs grouped list of tables based on foreign key dependencies for safe seeder execution.';

    public function run(array $params)
    {
        try {
            $db = Database::connect();
            $tables = array_filter($db->listTables(), function ($table) {
                return !in_array($table, ['migrations', 'seedHistory']);
            });

            $dependencies = [];

            // Build dependency map
            foreach ($tables as $table) {
                $dependencies[$table] = [];
                $fks = $db->getForeignKeyData($table);
                if ($fks) {
                    foreach ($fks as $fk) {
                        $refTable = $fk->foreign_table_name;
                        // Skip self-referencing foreign keys (e.g., parent_id in same table)
                        if ($refTable !== $table && !in_array($refTable, ['migrations', 'seedHistory'])) {
                            $dependencies[$table][] = $refTable;
                        }
                    }
                }
            }

            // Debug: print dependency map
            CLI::write("\nForeign Key Dependency Map:", 'light_gray');
            foreach ($dependencies as $table => $refs) {
                $list = empty($refs) ? '-' : implode(', ', $refs);
                CLI::write(" - {$table} => {$list}", 'light_gray');
            }

            // Topological sort with grouping
            $groups = [];
            $visited = [];
            while (count($visited) < count($dependencies)) {
                $group = [];
                foreach ($dependencies as $table => $refs) {
                    if (in_array($table, $visited)) {
                        continue;
                    }
                    $unresolved = array_filter($refs, fn($ref) => !in_array($ref, $visited));
                    if (empty($unresolved)) {
                        $group[] = $table;
                    }
                }
                if (empty($group)) {
                    // Circular dependency detected
                    $remaining = array_diff(array_keys($dependencies), $visited);
                    CLI::write("\n⚠️  Circular dependency detected among these tables:", 'red');
                    foreach ($remaining as $tbl) {
                        CLI::write(" - {$tbl} depends on: " . implode(', ', $dependencies[$tbl]), 'red');
                    }
                    CLI::write("\n🔁 Forcing these into last group to break cycle.\n", 'yellow');
                    $groups[] = array_values($remaining);
                    break;
                }
                $groups[] = $group;
                $visited = array_merge($visited, $group);
            }

            // Output final ordered groups
            CLI::write("\nSeeder Execution Order (Grouped by dependency):\n", 'green');
            foreach ($groups as $i => $group) {
                CLI::write("Group " . ($i + 1) . ":", 'yellow');
                foreach ($group as $table) {
                    CLI::write(" - {$table}", 'white');
                }
                CLI::write('');
            }
        } catch (Throwable $e) {
            CLI::error("Error: " . $e->getMessage());
        }
    }
}
