<?php

namespace Modules\Backend\System\Controllers;

use App\Controllers\ApiBaseController;
use Modules\Backend\System\Models\UserModel;
use Modules\Backend\System\Models\MenuConfigModel;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\UserPermissionLib;
use App\Libraries\AssetManager;
use App\Libraries\Auth;



// use OpenApi\Annotations as OA;
class MenuConfig extends ApiBaseController
{
    use ResponseTrait;

    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function save($tenantId, $menuLocation = 'sidebarMain')
    {


        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        // if (empty($jsonInput)) {
        //     return $this->fail('No menu data provided', 400);
        // }

        $menuConfigModel = new MenuConfigModel();

        // **Delete Existing Menu Before Saving Fresh**
        $menuConfigModel->where('isDeleted', 0)
            ->where("tenantId", $tenantId ? $tenantId : null)
            ->where('menuLocation', $menuLocation)
            ->delete();

        // Save new menu items with a clean approach
        $this->processMenuItems($jsonInput, null, $menuConfigModel, $tenantId ? $tenantId : null, $menuLocation);

        // Clear the cache
        service('cache')->delete($tenantId . '_' . $menuLocation . '_menuConfig');

        return $this->respond([
            'status' => true,
            'message' => 'Menu configuration saved successfully'
        ]);
    }

    /**
     * Recursively process menu items for a fresh save
     */
    private function processMenuItems($menuItems, $parentId, $menuConfigModel, $tenantId, $menuLocation = 'sidebarMain')
    {
        foreach ($menuItems as $index => $menuItem) {
            $data = [
                'tenantId'     => $tenantId,
                'orderId'      => $index + 1, // Maintain order
                'title'        => $menuItem['label'],
                'url'          => $menuItem['url'] ?? '',
                'icon'         => $menuItem['icon'] ?? '',
                'class'        => $menuItem['class'] ?? '',
                'module'       => $menuItem['module'] ?? '',
                'permissions'  => $menuItem['permissions'] ?? '',
                'attributes'   => $menuItem['attributes'] ?? null,
                'isPopup'     => $menuItem['isPopup'] ?? 0,
                'parentId'     => $parentId,
                'menuLocation' => $menuLocation,
                'isActive'     => 1,
                'isDeleted'    => 0,
                'updatedBy'    => $this->user->userId ?? null,
                'updatedAt'    => timenow()
            ];

            // Insert new menu item
            $menuConfigModel->insert($data);
            $insertedId = $menuConfigModel->getInsertID(); // Get inserted ID for parent-child linking

            // Recursively process children with updated parentId
            if (!empty($menuItem['children'])) {
                $this->processMenuItems($menuItem['children'], $insertedId, $menuConfigModel, $tenantId, $menuLocation);
            }
        }
    }

    public function get($tenantId, $menuLocation = 'sidebarMain')
    {
        $menuConfigModel = new MenuConfigModel();

        // Fetch all menu items
        $menuItems =  $menuConfigModel->where("tenantId", $tenantId ? $tenantId : null)
            ->where('menuLocation', $menuLocation)
            ->where('isDeleted', 0)
            ->orderBy('orderId', 'ASC')
            ->asArray()
            ->findAll();

        // Convert flat menu to nested structure
        $nestedMenu = $this->buildNestedMenu($menuItems);

        return $this->respond([
            'status' => true,
            'menu' => $nestedMenu
        ]);
    }

    /**
     * Convert flat menu items into a nested parent-child structure
     */
    private function buildNestedMenu($menuItems, $parentId = null)
    {
        $menuTree = [];

        foreach ($menuItems as $menuItem) {
            if ($menuItem['parentId'] == $parentId) {
                $children = $this->buildNestedMenu($menuItems, $menuItem['menuConfigId']);

                $menuTree[] = [
                    'id'         => $menuItem['menuConfigId'],
                    'label'      => $menuItem['title'],
                    'url'        => $menuItem['url'],
                    'icon'       => $menuItem['icon'],
                    'class'      => $menuItem['class'],
                    'module'     => $menuItem['module'],
                    'permissions' => $menuItem['permissions'],
                    'attributes' => $menuItem['attributes'],
                    'isPopup'    => $menuItem['isPopup'],
                    'children'   => $children
                ];
            }
        }

        return $menuTree;
    }

    public function getRoutes()
    {
        $routes = service('routes');
        $routeList = [];

        foreach ($routes->getRoutes() as $route => $handler) {

            $ignoreList = [
                'api',
                'assets',
                'auth',
                // 'samples',
                'system',
                'tools',
                'home',
                'manifest.json',
                'apidocs'
            ];
            // ignore routes starting with 'api' and default routes
            if (in_array(explode('/', $route)[0], $ignoreList)) {
                continue;
            }

            if (strpos($route, '([^/]+)') !== false) {
                continue;
            }

            if (strpos($route, '(.*)') !== false) {
                continue;
            }

            if ($route == '/') {
                continue;
            }

            // Filter only usable routes (exclude closures, API calls, etc.)
            if (is_string($handler)) {
                $routeList[] = $route;
            }
        }

        return $this->respond([
            'status' => 'success',
            'routes' => $routeList
        ]);
    }

    public function getPermissions($tenantId)
    {
        $scope = 'tenant';
        if ($tenantId) {
            $scope = 'tenant';
        } else {
            $scope = 'saas';
        }
        //get all tenant permissions from db table.
        $temp = $this->db->query("SELECT * FROM userPermissionMaster WHERE scope='$scope'")->getResult();
        $permissions = [];

        foreach ($temp as $p) {
            $permissions[$p->module][$p->permission] = 1;
        }

        return $this->respond([
            'status' => 'success',
            'permissions' => $permissions
        ]);

        return $this->respond([
            'status' => 'success',
            'routes' => $routeList
        ]);
    }

    public function restoreDefault($tenantId, $menuLocation = 'sidebarMain')
    {
        $jsonFile = ROOTPATH . $tenantId . '_' . $menuLocation . '_MenuConfig.json';

        if (!file_exists($jsonFile)) {
            if (is_cli()) {
                echo "Default menu configuration file not found\n";
                return;
            }
            return $this->fail('Default menu configuration file not found', 404);
        }

        $menuConfigModel = new MenuConfigModel();

        // **Delete Existing Menu Before Saving Fresh**
        $menuConfigModel->where('isDeleted', 0)
            ->where("tenantId", $tenantId ? $tenantId : null)
            ->where('menuLocation', $menuLocation)
            ->delete();

        // Read JSON file
        $jsonInput = file_get_contents($jsonFile);
        $menuItems = json_decode($jsonInput, true);

        // Save new menu items with a clean approach
        $this->processMenuItems($menuItems, null, $menuConfigModel, $tenantId ? $tenantId : null, $menuLocation);

        // Clear the cache
        service('cache')->delete($tenantId . '_' . $menuLocation . '_menuConfig');

        // Fetch all menu items
        $menuItems = $menuConfigModel->where("tenantId", $tenantId ? $tenantId : null)
            ->where('menuLocation', $menuLocation)
            ->where('isDeleted', 0)
            ->orderBy('orderId', 'ASC')
            ->asArray()
            ->findAll();

        // Convert flat menu to nested structure
        $nestedMenu = $this->buildNestedMenu($menuItems);

        if (is_cli()) {
            echo "Default menu configuration restored successfully\n";
            return;
        }
        return $this->respond([
            'status' => true,
            'message' => 'Default menu configuration restored successfully',
            'menu' => $nestedMenu
        ]);
    }
}
