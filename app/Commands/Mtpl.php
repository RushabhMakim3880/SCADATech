<?php

namespace App\Commands;

use Modules\Backend\System\Controllers\MenuConfig;
use Modules\Backend\System\Models\MenuConfigModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Autoload;

class Mtpl extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'MtplTools';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'mtpl:run';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Run the MtplTools command.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'mtpl:run [arguments] [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'cleanUp' => 'Clean up project code and empty the project. WARNING: This will delete all files and folders in the project.',
        'syncTo' => 'Sync the skeleton project to the specified directory.',
        'migrate' => 'Migrate the database to the latest version through all available namespaces.',
        'seed' => 'Seed the database with initial data.',
        'reset' => 'Reset database, run migrations and seed the database.',
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--password' => 'The password to use for this command.',
        '--target' => 'The directory to sync the skeleton project to.',
    ];

    private $cleanUpStats = [
        'deleted_files' => 0,
        'deleted_dirs'  => 0,
        'skipped'       => 0,
        'not_exists'    => 0,
    ];

    private $syncStats = [
        'files_copied'    => 0, // New or modified files copied
        'files_skipped'   => 0, // Files skipped due to identical content
        'new_files'       => 0, // Newly created files
        'modified_files'  => 0, // Modified files copied
        'dirs_created'    => 0, // Newly created directories
        'samples_skipped' => 0, // Samples directories skipped
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $subcommand = $params[0] ?? null; // First argument as subcommand

        switch ($subcommand) {
            case 'cleanUp':
                $this->cleanUp();
                break;

            case 'sync':
                $this->sync();
                break;

            case 'reset':
                $this->reset();
                break;

            case 'migrate':
                CLI::write('Migrating the database...', 'green');
                $this->migrate();
                break;

            case 'seed':
                CLI::write('Seeding the database...', 'green');
                $this->seed();
                break;

            default:
                CLI::error('Invalid subcommand. Use as below');
                CLI::write('php spark mtpl:run [cleanUp|sync|reset|migrate|seed] [--option=value]', 'yellow');
                break;
        }
    }


    private function seed()
    {
        $namespaces = $this->getNamespaces();
        $seededNamespaces = [];
        $errors = [];
        $seeders = [];

        // Get type argument from CLI (default to 'default')
        $typeArg = CLI::getOption('type') ?? 'default';
        $allowedTypes = explode(',', $typeArg);

        foreach ($namespaces as $namespace => $path) {
            $path = rtrim($path, '/') . '/';
            $seederPath = $path . 'Database/Seeds';

            if (!is_dir($seederPath)) {
                continue;
            }

            $seederFiles = scandir($seederPath);
            foreach ($seederFiles as $seederFile) {
                if ($seederFile === '.' || $seederFile === '..' || pathinfo($seederFile, PATHINFO_EXTENSION) !== 'php') {
                    continue;
                }

                $seederClass = pathinfo($seederFile, PATHINFO_FILENAME);
                $seederClass = $namespace . '\\Database\\Seeds\\' . $seederClass;

                if (!class_exists($seederClass)) {
                    CLI::error("Seeder class not found: $seederClass");
                    continue;
                }

                // Default values
                $priority = 100;
                $type = 'default';

                try {
                    $reflection = new \ReflectionClass($seederClass);

                    if ($reflection->hasProperty('priority')) {
                        $priorityProperty = $reflection->getProperty('priority');
                        $priorityProperty->setAccessible(true);
                        $priority = $priorityProperty->getValue($reflection->newInstanceWithoutConstructor());
                    }

                    if ($reflection->hasProperty('type')) {
                        $typeProperty = $reflection->getProperty('type');
                        $typeProperty->setAccessible(true);
                        $type = $typeProperty->getValue($reflection->newInstanceWithoutConstructor());
                    }
                } catch (\Exception $e) {
                    CLI::error("Error retrieving priority/type for seeder: $seederClass");
                    continue;
                }

                // Skip if seeder type not in allowedTypes
                if (!in_array($type, $allowedTypes)) {
                    continue;
                }

                $seeders[] = [
                    'class' => $seederClass,
                    'priority' => $priority,
                    'type' => $type,
                ];
            }
        }

        // Sort seeders by priority
        usort($seeders, fn($a, $b) => $a['priority'] <=> $b['priority']);

        foreach ($seeders as $seeder) {
            $seederClass = $seeder['class'];
            CLI::write("Running seeder: {$seederClass} [type: {$seeder['type']}] [priority: {$seeder['priority']}]", 'yellow');

            try {
                // Run seeder via command
                command('db:seed ' . str_replace("\\", "\\\\", $seederClass));
                $seededNamespaces[] = $seederClass;

                // Optional: store in history if you enable it
                // $db->table('seeder_history')->insert([
                //     'class_name' => $seederClass,
                //     'type' => $seeder['type'],
                //     'executed_at' => date('Y-m-d H:i:s')
                // ]);
            } catch (\Exception $e) {
                CLI::error("Error running seeder: $seederClass");
                $errors[] = [
                    'seeder' => $seederClass,
                    'error' => $e->getMessage(),
                ];
            }
        }

        // Optional: auto-restore default menu config
        $db = db_connect();
        $isMenuEmpty = $db->table('menuConfig')->countAllResults() === 0;
        if ($isMenuEmpty) {
            $tenantId = 1;
            $menu = new MenuConfig();
            $menu->restoreDefault($tenantId);
        }

        // Print summary
        $this->printSeedSummary($seededNamespaces, $errors);
    }


    private function printSeedSummary(array $seededNamespaces, array $errors)
    {
        CLI::write('');
        CLI::write('Seeder Summary:', 'blue');
        CLI::write('================');
        CLI::write('Namespaces Seeded:', 'green');

        if (!empty($seededNamespaces)) {
            foreach ($seededNamespaces as $namespace) {
                CLI::write(" - $namespace", 'green');
            }
        } else {
            CLI::write("No seeders were run.", 'yellow');
        }

        if (!empty($errors)) {
            CLI::write('');
            CLI::write('Errors:', 'red');
            foreach ($errors as $error) {
                CLI::write(" - Seeder: {$error['seeder']}", 'red');
                CLI::write("   Error: {$error['error']}", 'red');
            }
        }

        CLI::write('');
        CLI::write('Seeder process completed.', 'green');
    }

    private function migrate()
    {

        // ignore full logic and use standard code to run migration across all defined namespaces
        command('migrate --all');
        return false;

        $namespaces = $this->getNamespaces();
        $migrations = [];
        $migratedNamespaces = [];
        $errors = [];

        // Collect all migration files
        foreach ($namespaces as $namespace => $path) {
            $migrationPath = rtrim($path, '/') . '/Database/Migrations';

            if (!is_dir($migrationPath)) {
                continue;
            }

            $files = glob($migrationPath . '/*.php');
            foreach ($files as $file) {
                $timestamp = $this->extractTimestamp($file);
                if ($timestamp) {
                    $migrations[$timestamp][] = [
                        'namespace' => $namespace,
                        'file' => $file
                    ];
                }
            }
        }

        // Sort migrations by timestamp
        ksort($migrations, SORT_NUMERIC);

        // Run migrations in order
        foreach ($migrations as $batch) {
            foreach ($batch as $migration) {
                $namespace = $migration['namespace'];

                if (!in_array($namespace, $migratedNamespaces)) {
                    CLI::write("Running migrations for namespace: $namespace", 'yellow');
                    try {
                        command('migrate -n ' . str_replace("\\", "\\\\", $namespace));
                        $migratedNamespaces[] = $namespace;
                    } catch (\Exception $e) {
                        CLI::error("Error running migrations for namespace: $namespace");
                        $errors[] = [
                            'namespace' => $namespace,
                            'error' => $e->getMessage(),
                        ];
                    }
                }
            }
        }

        // Print summary
        $this->printMigrationSummary($migratedNamespaces, $errors);
    }

    /**
     * Extracts timestamp from migration filename.
     */
    private function extractTimestamp($file)
    {
        if (preg_match('/(\d{4}-\d{2}-\d{2}-\d{6})/', basename($file), $matches)) {
            // Convert to a sortable numeric timestamp (YYYYMMDDHHMMSS)
            return str_replace(['-', '_'], '', $matches[1]);
        }
        return null;
    }


    private function cleanUp()
    {
        // Validate that only --passwod is provided
        $option = CLI::getOption('password');
        if (!$option) {
            CLI::error('cleanUp requires the --password option.');
            CLI::write('Usage: php spark mtpl:run cleanUp --password PASSWORD', 'yellow');
            return;
            return;
        }

        // Validate the password
        if ($option !== 'Mtpl@123') {
            CLI::error('Invalid password.');
            return;
        }

        CLI::write('Executing cleanUp...', 'green');


        // Directories to clean up
        $directories = [
            'Modules',
            'Modules/Backend',
            'Modules/Frontend',
            'public/Modules',
        ];

        // Allowed directories to keep
        $allowed = ['Backend', 'Frontend', 'Apidocs', 'System', 'Samples'];

        foreach ($directories as $directory) {
            $this->cleanupDirectory($directory, $allowed);
        }

        // Print summary
        $this->printCleanUpSummary();
    }

    private function reset()
    {
        //first check environment variable to disable reset command
        $disableReset = getenv('disableReset');
        if ($disableReset && strtolower($disableReset) === 'yes') {
            CLI::error('Reset command is disabled in the environment configuration.');
            return;
        }


        $this->showWarning('This will delete all tables from the database and reset the project. Use with caution!', 'Are you sure?');

        $confirm  = CLI::prompt('Type "yes" to confirm', null, 'required');

        if ($confirm !== 'yes') {
            CLI::write('Reset cancelled.', 'yellow');
            return;
        }

        // Validate that only --passwod is provided
        $option = CLI::getOption('password');
        if (!$option) {
            CLI::error('cleanUp requires the --password option.');
            CLI::write('Usage: php spark mtpl:run reset --password PASSWORD', 'yellow');
            return;
            return;
        }

        // Validate the password
        if ($option !== 'Mtpl@123') {
            CLI::error('Invalid password.');
            return;
        }

        CLI::write('Executing cleanUp...', 'green');

        //delete all tables from database.
        $db = db_connect();
        $forge = \Config\Database::forge();

        // Get all table names
        $tables = $db->listTables();

        if (empty($tables)) {
            CLI::write('No tables found to delete.', 'yellow');
            return;
        }

        CLI::write('Deleting all tables...', 'red');

        foreach ($tables as $table) {
            $forge->dropTable($table, true); // True to check existence before dropping
            CLI::write("Dropped table: $table", 'green');
        }

        CLI::write('Database reset complete.', 'green');


        // run migration
        CLI::write('Migrating the database...', 'green');
        $this->migrate();

        // seed the database

        CLI::write('Seeding the database...', 'green');
        $this->seed();
    }

    private function sync()
    {
        // Validate that only --target is provided
        $targetDir = CLI::getOption('target');
        if (!$targetDir) {
            CLI::error('Sync requires the --target option.');
            CLI::write('Usage: php spark sync:data --target /full/path/to/target', 'yellow');
            return;
            return;
        }

        // Normalize target directory path
        $targetDir = rtrim($targetDir, '/');

        // Validate the target directory
        if (!is_dir($targetDir)) {
            CLI::error('Target directory does not exist: ' . $targetDir);
            return;
        }

        // Define the directories and files to copy
        $toCopy = [
            'app/',
            'autoCrudTemplates/',
            'Modules/Apidocs',
            'Modules/Backend/System',
            'Modules/Backend/Users',
            'Modules/Frontend/System',
            'Modules/Frontend/Users',
            'public/assets',
            'public/Modules',
            'public/mtplPlugins',
            'public/autoPush.php',
            'env',
            'env.template',
            'composer.json',
            'composer.lock'
        ];


        // Define files to ignore
        $ignoreFiles = [
            'app/Libraries/AssetManager.php',
            'app/Libraries/Captcha.php',
            'app/Config/Routes.php',
            'app/Config/Filters.php',

        ];

        $skipFiles = [
            'app/Controllers/ProjectTools.php',
            'public/assets/css/project.css',
            'public/assets/js/project.js',
            'Modules/Backend/System/Database/Seeds/StatusMasterData.php',
            'app/Helpers/project_helper.php',
        ];

        // Initialize counters
        $filesProcessed = 0;
        $filesCopied = 0;
        $filesSkipped = 0;
        $directoriesProcessed = 0;
        $directoriesCreated = 0;
        $directoriesSkipped = 0;

        // Track ignored files for manual syncing warnings
        $manualSyncWarnings = [];

        foreach ($toCopy as $item) {
            $sourcePath = rtrim($item, '/');
            $fullSourcePath = ROOTPATH . $sourcePath;
            $fullTargetPath = $targetDir . '/' . $sourcePath;

            if (is_dir($fullSourcePath)) {
                // Recursively process directory
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($fullSourcePath, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::SELF_FIRST
                );

                foreach ($iterator as $file) {
                    $relativePath = str_replace(ROOTPATH, '', $file->getPathname());
                    $targetFile = $targetDir . '/' . $relativePath;

                    if ($file->isDir()) {
                        $directoriesProcessed++;
                        // Check if the directory already exists
                        if (!is_dir($targetFile)) {
                            mkdir($targetFile, 0777, true);
                            $directoriesCreated++;
                        } else {
                            $directoriesSkipped++;
                        }
                    } elseif ($file->isFile()) {
                        $filesProcessed++;

                        if (in_array($relativePath, $skipFiles)) {
                            // Skip files that are not to be copied
                            $filesSkipped++;
                            continue;
                        }


                        if (in_array($relativePath, $ignoreFiles)) {
                            // Check if ignored file exists in the target
                            if (!file_exists($targetFile)) {
                                copy($file->getPathname(), $targetFile); // Copy ignored file if it doesn't exist
                                $filesCopied++;
                            } elseif (md5_file($file->getPathname()) !== md5_file($targetFile)) {
                                // Warn if ignored file content differs
                                $manualSyncWarnings[] = $relativePath;
                            }
                            $filesSkipped++;
                            continue;
                        }

                        if (!file_exists($targetFile) || md5_file($file->getPathname()) !== md5_file($targetFile)) {
                            // Copy file if it doesn't exist or content differs
                            copy($file->getPathname(), $targetFile);
                            $filesCopied++;
                        } else {
                            $filesSkipped++;
                        }
                    }
                }
            } elseif (is_file($fullSourcePath)) {
                // Process single file
                $filesProcessed++;

                if (in_array($sourcePath, $skipFiles)) {
                    // Skip files that are not to be copied
                    $filesSkipped++;
                    continue;
                }

                if (in_array($sourcePath, $ignoreFiles)) {
                    // Check if ignored file exists in the target
                    if (!file_exists($fullTargetPath)) {
                        copy($fullSourcePath, $fullTargetPath); // Copy ignored file if it doesn't exist
                        $filesCopied++;
                    } elseif (md5_file($fullSourcePath) !== md5_file($fullTargetPath)) {
                        // Warn if ignored file content differs
                        $manualSyncWarnings[] = $sourcePath;
                    }
                    $filesSkipped++;
                    continue;
                }

                if (!file_exists($fullTargetPath) || md5_file($fullSourcePath) !== md5_file($fullTargetPath)) {
                    // Copy file if it doesn't exist or content differs
                    copy($fullSourcePath, $fullTargetPath);
                    $filesCopied++;
                } else {
                    $filesSkipped++;
                }
            }
        }

        // Print detailed summary
        CLI::write('Sync Completed!', 'green');
        CLI::write('Summary:', 'green');
        CLI::write("  Directories Processed: $directoriesProcessed", 'white');
        CLI::write("  Directories Created: $directoriesCreated", 'white');
        CLI::write("  Directories Skipped: $directoriesSkipped", 'white');
        CLI::write("  Files Processed: $filesProcessed", 'white');
        CLI::write("  Files Copied: $filesCopied", 'white');
        CLI::write("  Files Skipped: $filesSkipped", 'white');

        // Print manual sync warnings
        if (!empty($manualSyncWarnings)) {
            CLI::write(PHP_EOL . 'The following ignored files have different content and must be synced manually:', 'yellow');
            foreach ($manualSyncWarnings as $file) {
                CLI::write("  - $file", 'white');
            }
        }
    }

    private function cleanupDirectory(string $path, array $allowed)
    {

        $path = rtrim($path, '/');
        $path = ROOTPATH . $path;

        if (!is_dir($path)) {
            CLI::error("Directory does not exist: $path");
            $this->cleanUpStats['not_exists']++;
            return;
        }

        $items = scandir($path);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullPath = rtrim($path, '/') . '/' . $item;

            if (in_array($item, $allowed)) {
                CLI::write("Skipping: $fullPath", 'yellow');
                $this->cleanUpStats['skipped']++;
                continue;
            }

            $this->delete($fullPath);
        }
    }


    private function delete(string $path)
    {
        if (is_dir($path)) {
            $this->deleteDirectory($path);
            $this->cleanUpStats['deleted_dirs']++;
        } elseif (is_file($path)) {
            unlink($path);
            CLI::write("Deleted file: $path", 'green');
            $this->cleanUpStats['deleted_files']++;
        }
    }

    private function deleteDirectory(string $path)
    {
        $items = scandir($path);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullPath = rtrim($path, '/') . '/' . $item;

            if (is_dir($fullPath)) {
                $this->deleteDirectory($fullPath);
            } else {
                unlink($fullPath);
                CLI::write("Deleted file: $fullPath", 'green');
                $this->cleanUpStats['deleted_files']++;
            }
        }

        rmdir($path);
        CLI::write("Deleted directory: $path", 'green');
    }

    private function printCleanUpSummary()
    {
        CLI::write('');
        CLI::write('Cleanup Summary:', 'blue');
        CLI::write('================');
        CLI::write('Deleted Files:    ' . $this->cleanUpStats['deleted_files'], 'green');
        CLI::write('Deleted Folders:  ' . $this->cleanUpStats['deleted_dirs'], 'green');
        CLI::write('Skipped Items:    ' . $this->cleanUpStats['skipped'], 'yellow');
        CLI::write('Non-existent Paths: ' . $this->cleanUpStats['not_exists'], 'red');
        CLI::write('');
        CLI::write('Cleanup completed!', 'green');
    }

    private function getNamespaces(): array
    {
        $autoload = new Autoload();
        return $autoload->psr4; // Fetch all namespaces from Config\Autoload
    }

    private function printMigrationSummary(array $migratedNamespaces, array $errors)
    {
        CLI::write('');
        CLI::write('Migration Summary:', 'blue');
        CLI::write('===================');
        CLI::write('Namespaces Migrated:', 'green');

        if (!empty($migratedNamespaces)) {
            foreach ($migratedNamespaces as $namespace) {
                CLI::write(" - $namespace", 'green');
            }
        } else {
            CLI::write("No migrations were run.", 'yellow');
        }

        if (!empty($errors)) {
            CLI::write('');
            CLI::write('Errors:', 'red');
            foreach ($errors as $error) {
                CLI::write(" - Namespace: {$error['namespace']}", 'red');
                CLI::write("   Error: {$error['error']}", 'red');
            }
        }

        CLI::write('');
        CLI::write('Migration process completed.', 'green');
    }


    private function showWarning($title, $description)
    {
        CLI::write("
        ░██████╗████████╗░█████╗░██████╗░
        ██╔════╝╚══██╔══╝██╔══██╗██╔══██╗
        ╚█████╗░░░░██║░░░██║░░██║██████╔╝
        ░╚═══██╗░░░██║░░░██║░░██║██╔═══╝░
        ██████╔╝░░░██║░░░╚█████╔╝██║░░░░░
        ╚═════╝░░░░╚═╝░░░░╚════╝░╚═╝░░░░░", "red");
        CLI::newLine();
        CLI::write(str_repeat('=', 80), 'green');
        CLI::write("📌  " . strtoupper($title), 'cyan'); // Title in cyan
        CLI::write(str_repeat('-', 80), 'green');
        CLI::write($description, 'yellow'); // Description in yellow
        CLI::write(str_repeat('=', 80), 'green');
        CLI::newLine();

        // Pause execution until the user presses Enter
        // CLI::prompt("Press Enter to continue...", null);
    }
}
