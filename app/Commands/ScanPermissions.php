<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Autoload;

class ScanPermissions extends BaseCommand
{
    protected $group       = 'custom';
    protected $name        = 'scan:permissions';
    protected $description = 'Scans project files to list module-wise permissions.';
    protected $modulePermissions = [];
    protected $phpFiles = [];
    protected $namespaces = [];
    protected $permissionsNotUsed = [];
    protected $permissionsNotDefined = [];
    protected $moduleNotInFeatureRegistry = [];

    public function run(array $params)
    {
        // first scan all Config/Permissions.php files to load existing module permissions
        $this->loadModulePermissions();

        // now scan code to find usage of all permissions to compare and find differences.
        $scanPaths = [
            ROOTPATH . 'app',
            ROOTPATH . 'Modules',
        ];
        $this->scanPhpFiles($scanPaths);

        $permissions = [];
        $permissionsNotDefined = [];

        foreach ($this->phpFiles as $file) {
            $content = file_get_contents($file);

            preg_match_all('/UserPermissionLib::userCanDo\([\'"]([^\'"]+)[\'"],\s*[\'"]([^\'"]+)[\'"]\)/', $content, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $module = $match[1];
                $permission = $match[2];

                $nameSpace = $this->getNamespace($file);

                if (!$this->isPermissionExists($module, $permission)) {
                    $permissionsNotDefined[$nameSpace][$module][$permission][] = $file;
                }
                $permissions[$nameSpace][$module][$permission][] = $file;
            }
        }

        $this->findUnusedPermissions($permissions);
        $this->checkModuleFeatures();

        if (empty($permissionsNotDefined) and empty($this->permissionsNotUsed) and empty($this->moduleNotInFeatureRegistry)) {
            $this->showIntro('Congratulations!', 'No new/missing/unused permissions found in the project.');
            return;
        }

        CLI::write("\n" . str_repeat('-', 80), 'green');
        CLI::write("Permissions Used in code but not defined in Permissions.php files", 'green');
        CLI::write(str_repeat('-', 80) . "\n", 'green');

        if (empty($permissionsNotDefined)) {
            CLI::write("No new permissions found in the code.", 'cyan');
        }

        foreach ($permissionsNotDefined as $namespace => $modulePermissions) {
            CLI::write("Namespace: $namespace", 'cyan');
            foreach ($modulePermissions as $module => $perms) {
                CLI::write("  - Module: $module", 'green');
                foreach ($perms as $perm => $files) {
                    CLI::write("      - $perm", 'yellow');
                    foreach ($files as $file) {
                        CLI::write("          - $file", 'light_gray');
                    }
                }
            }
        }

        CLI::write("\n" . str_repeat('-', 80), 'green');
        CLI::write("Permissions defined in Permissions.php files but not used in code", 'green');
        CLI::write(str_repeat('-', 80) . "\n", 'green');

        if (empty($this->permissionsNotUsed)) {
            CLI::write("No unused permissions found in the configuration.", 'cyan');
        }

        foreach ($this->permissionsNotUsed as $namespace => $modulePermissions) {
            CLI::write("Namespace: $namespace", 'cyan');
            foreach ($modulePermissions as $module => $perms) {
                CLI::write("  - Module: $module", 'green');
                foreach ($perms as $perm) {
                    CLI::write("      - $perm", 'yellow');
                }
            }
        }


        CLI::write("\n" . str_repeat('-', 80), 'green');
        CLI::write("Modules not registered in featureRegistry", 'green');
        CLI::write(str_repeat('-', 80) . "\n", 'green');
        if (empty($this->moduleNotInFeatureRegistry)) {
            CLI::write("All modules are registered in featureRegistry.", 'cyan');
        }
        foreach ($this->moduleNotInFeatureRegistry as $module) {
            CLI::write("  - $module", 'yellow');
        }
        CLI::write("\n" . str_repeat('=', 80), 'green');
    }

    private function isPermissionExists($module, $permission)
    {
        return in_array($permission, $this->modulePermissions[$module]["permissions"] ?? []);
    }

    private function getNamespace($file)
    {
        foreach ($this->namespaces as $namespace => $path) {
            if (strpos($file, $path) === 0) {
                return $namespace;
            }
        }
    }

    private function scanPhpFiles($scanPaths)
    {
        foreach ($scanPaths as $path) {
            $this->getPHPFiles($path);
        }
    }

    private function getPHPFiles($path)
    {
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        $files = [];

        foreach ($rii as $file) {
            if (!$file->isDir() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        $this->phpFiles = array_merge($this->phpFiles, $files);

        return $files;
    }

    private function loadModulePermissions()
    {
        $autoload = new Autoload();
        $this->namespaces = $autoload->psr4;

        foreach ($this->namespaces as $path) {
            $configPath = rtrim($path, '/') . '/Config/Permissions.php';
            if (file_exists($configPath)) {
                $newModulePermissions = require $configPath;
                $this->modulePermissions = array_merge($this->modulePermissions, $newModulePermissions);
            }
        }
    }

    private function findUnusedPermissions($usedPermissions)
    {
        $autoload = new Autoload();
        $this->namespaces = $autoload->psr4;

        foreach ($this->namespaces as $namespace => $path) {
            $configPath = rtrim($path, '/') . '/Config/Permissions.php';
            if (file_exists($configPath)) {
                $newModulePermissions = require $configPath;

                foreach ($newModulePermissions as $module => $permissions) {
                    foreach ($permissions['permissions'] as $permission) {
                        if (!isset($usedPermissions[$namespace][$module][$permission])) {
                            $this->permissionsNotUsed[$namespace][$module][$permission] = $permission;
                        }
                    }
                }
            }
        }
    }

    private function checkModuleFeatures()
    {
        $db = db_connect();
        $modules = $db->query("SELECT DISTINCT module FROM userPermissionMaster WHERE scope = 'tenant'")->getResultArray();
        foreach ($modules as $module) {
            $moduleName = $module['module'];

            //check if module exists in featureRegistry ?
            $exists = $db->query("SELECT * FROM featureRegistry WHERE featureKey = ? AND groupKey=?", ["moduleEnabled_" . $moduleName, "modules"])->getRow();

            if (!$exists) {
                $this->moduleNotInFeatureRegistry[$moduleName] = $moduleName;
            }
        }
    }

    private function showIntro($title, $description)
    {
        CLI::write("
        ░█████╗░██╗░░░░░██╗░░░░░  ░██████╗░░█████╗░░█████╗░██████╗░
        ██╔══██╗██║░░░░░██║░░░░░  ██╔════╝░██╔══██╗██╔══██╗██╔══██╗
        ███████║██║░░░░░██║░░░░░  ██║░░██╗░██║░░██║██║░░██║██║░░██║
        ██╔══██║██║░░░░░██║░░░░░  ██║░░╚██╗██║░░██║██║░░██║██║░░██║
        ██║░░██║███████╗███████╗  ╚██████╔╝╚█████╔╝╚█████╔╝██████╔╝
        ╚═╝░░╚═╝╚══════╝╚══════╝  ░╚═════╝░░╚════╝░░╚════╝░╚═════╝░", "cyan");
        CLI::newLine();
        CLI::write(str_repeat('=', 80), 'green');
        CLI::write("👍  " . strtoupper($title), 'cyan'); // Title in cyan
        CLI::write(str_repeat('-', 80), 'green');
        CLI::write($description, 'cyan'); // Description in yellow
        CLI::write(str_repeat('=', 80), 'green');
        CLI::newLine();

        // Pause execution until the user presses Enter
        // CLI::prompt("Press Enter to continue...", null);
    }
}
