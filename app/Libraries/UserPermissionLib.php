<?php

namespace App\Libraries;

use Config\Autoload;
use CodeIgniter\Database\BaseConnection;
use App\Libraries\Auth;
use App\Libraries\SettingManager;
use App\Libraries\Tenant;

class UserPermissionLib
{
    protected static ?BaseConnection $db = null;
    protected static ?object $user = null;
    protected static array $permissionsCache = [];
    protected static array $modulePermissions = [];

    private static function initDb()
    {
        if (self::$db === null) {
            self::$db = \Config\Database::connect();
        }
    }

    public static function setUser($user)
    {
        self::initDb();
        self::$user = $user;
        self::$user->groupName = self::$db->table("userGroups")
            ->select("groupName")
            ->where("groupId", $user->groupId)
            ->get()
            ->getRow()
            ->groupName ?? "NoGroup";
    }

    public static function getUserPermissions($userId)
    {
        self::initDb();

        // Return cached permissions if available
        if (isset(self::$permissionsCache[$userId])) {
            return self::$permissionsCache[$userId];
        }

        $user = self::$db->table("userMaster")->where("userId", $userId)->get()->getRow();
        $userGroup = self::$db->table("userGroups")->where("groupId", $user->groupId)->get()->getRow();
        $userScope = is_null($userGroup->tenantId) ? "saas" : "tenant";

        // Load permissions into memory (if not already loaded)
        self::loadModulePermissions();
        self::syncPermissionsToDatabase();

        // Only fetch permissions from memory, not DB
        $allPermissions = [];
        foreach (self::$modulePermissions as $module => $data) {
            if ($data['scope'] === $userScope) {
                foreach ($data['permissions'] as $permission) {
                    $allPermissions[$module][$permission] = 0;
                }
            }
        }

        // Fetch assigned group permissions in a single query
        $groupPermissions = self::$db->table("userGroupPermissions")
            ->join("userPermissionMaster", "userGroupPermissions.permissionId = userPermissionMaster.permissionId")
            ->where("userGroupPermissions.groupId", $user->groupId)
            ->where("userPermissionMaster.scope", $userScope)
            ->get()
            ->getResultArray();

        foreach ($groupPermissions as $perm) {
            $allPermissions[$perm['module']][$perm['permission']] = 1;
        }

        // Admins automatically get all permissions within their scope
        if ($userGroup->isAdmin) {
            foreach ($allPermissions as $module => &$perms) {
                foreach ($perms as $permKey => $value) {
                    $perms[$permKey] = 1;
                }
            }
        }

        // Cache user permissions
        self::$permissionsCache[$userId] = $allPermissions;
        return $allPermissions;
    }

    public static function userCanDo($module, $permission, $userId = 0)
    {
        //check if module exists in featureRegistry
        $tenantId = Tenant::id();
        $config = SettingManager::getAllResolvedSettings($tenantId);
        if (isset($config['moduleEnabled_' . $module]) && !$config['moduleEnabled_' . $module]) {
            return false;
        }


        if ($module === "devMode" && $permission === "devMode" && $userId === 0) {
            $user = Auth::user();
            if ($user->jwtData->devModeEnabled) {
                return true;
            }
        }

        if (is_array($permission)) {
            foreach ($permission as $p) {
                if (self::userHasPermission($module, $p, $userId)) {
                    return true;
                }
            }
            return false;
        } else {
            return self::userHasPermission($module, $permission, $userId);
        }
    }

    private static function userHasPermission($module, $permission, $userId = 0)
    {
        $userId = $userId ?: self::$user->userId;
        $permissions = self::getUserPermissions($userId);

        return isset($permissions[$module][$permission]) && $permissions[$module][$permission] === 1;
    }

    private static function loadModulePermissions()
    {
        if (!empty(self::$modulePermissions)) {
            return;
        }

        $autoload = new Autoload();
        $namespaces = $autoload->psr4;

        foreach ($namespaces as $namespace => $path) {
            $configPath = rtrim($path, '/') . '/Config/Permissions.php';
            if (file_exists($configPath)) {
                $modulePermissions = require $configPath;
                self::$modulePermissions = array_merge(self::$modulePermissions, $modulePermissions);
            }
        }
    }

    public static function syncPermissionsToDatabase()
    {
        self::initDb();
        self::loadModulePermissions();

        $existingPermissions = self::$db->table("userPermissionMaster")->get()->getResultArray();
        $existingPermissionsMap = [];

        foreach ($existingPermissions as $perm) {
            $existingPermissionsMap[$perm['module']][$perm['permission']] = $perm;
        }

        // Insert new permissions from config
        foreach (self::$modulePermissions as $module => $moduleData) {

            $module = str_replace("_saas", "", $module); // Remove _saas suffix for consistency
            $module = str_replace("_tenant", "", $module); // Remove _saas suffix for consistency

            $scope = $moduleData['scope'];
            foreach ($moduleData['permissions'] as $permission) {
                if (!isset($existingPermissionsMap[$module][$permission])) {
                    self::$db->table("userPermissionMaster")->insert([
                        "scope" => $scope,
                        "module" => $module,
                        "moduleName" => ucwords(strtolower($module)),
                        "permission" => $permission,
                        "permissionName" => ucwords(strtolower($permission)),
                    ]);
                }
            }
        }

        // Delete old permissions that are not in config
        foreach ($existingPermissionsMap as $module => $permissions) {
            foreach ($permissions as $permission => $data) {
                if (!isset(self::$modulePermissions[$module]) || !in_array($permission, self::$modulePermissions[$module]['permissions'])) {
                    self::$db->table("userPermissionMaster")
                        ->where("module", $module)
                        ->where("permission", $permission)
                        ->delete();
                }
            }
        }
    }
    public static function getAssignableUsers($module)
    {
        self::initDb();
        $tenantId = Tenant::id();

        $users = self::$db->table("userMaster")
            ->select("userId, userName, groupId,firstName,lastName,email,mobile")
            ->where("tenantId", $tenantId)
            ->get()
            ->getResultArray();

        $assignableUsers = [];
        foreach ($users as $user) {
            $canOperateAll = self::userCanDo($module, 'operateAll', $user['userId']);
            $canOperateAssigned = self::userCanDo($module, 'operateAssigned', $user['userId']);
            // Add user if they have either or both permissions
            if ($canOperateAll || $canOperateAssigned) {
                $assignableUsers[] = $user;
            }
        }
        // debug($assignableUsers);
        // die;
        return $assignableUsers;
    }
    public static function canEdit($entity, $module)
    {
        $userId = self::$user->userId;

        if (
            UserPermissionLib::userCanDo($module, 'editAll', $userId) ||
            UserPermissionLib::userCanDo($module, 'edit', $userId)
        ) {
            return true;
        }

        // Look for any field ending with 'createdBy'
        $createdBy = null;
        foreach ($entity as $key => $value) {
            if (str_ends_with($key, 'createdBy')) {
                $createdBy = $value;
                break;
            }
        }

        if (
            UserPermissionLib::userCanDo($module, 'editOwn', $userId) &&
            $createdBy == $userId
        ) {
            return true;
        }

        return false;
    }
    public static function canDelete($entity, $module)
    {
        $userId = self::$user->userId;

        if (
            UserPermissionLib::userCanDo($module, 'deleteAll', $userId) ||
            UserPermissionLib::userCanDo($module, 'delete', $userId)
        ) {
            return true;
        }

        // Look for any field ending with 'createdBy'
        $createdBy = null;
        foreach ($entity as $key => $value) {
            if (str_ends_with($key, 'createdBy')) {
                $createdBy = $value;
                break;
            }
        }

        if (
            UserPermissionLib::userCanDo($module, 'deleteOwn', $userId) &&
            $createdBy == $userId
        ) {
            return true;
        }

        return false;
    }
    public static function canAdd($module)
    {
        $userId = self::$user->userId;

        if (
            UserPermissionLib::userCanDo($module, 'add', $userId)
        ) {
            return true;
        }

        return false;
    }
    public static function canView($entity, $module)
    {
        $userId = self::$user->userId;

        if (
            UserPermissionLib::userCanDo($module, 'viewAll', $userId) ||
            UserPermissionLib::userCanDo($module, 'view', $userId)
        ) {
            return true;
        }

        // Dynamically detect createdBy field (like Q_createdBy, etc.)
        $createdBy = null;
        foreach ($entity as $key => $value) {
            if (str_ends_with($key, 'createdBy')) {
                $createdBy = $value;
                break;
            }
        }

        if (
            UserPermissionLib::userCanDo($module, 'viewOwn', $userId) &&
            $createdBy == $userId
        ) {
            return true;
        }

        return false;
    }
}
