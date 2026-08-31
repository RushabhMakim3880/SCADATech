<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use App\Libraries\Tenant;
use App\Libraries\Auth;


class BottomMenu extends BaseConfig
{
    private static $cachedMenu = null; // Static property to retain data

    public function __construct()
    {
        parent::__construct();

        // If already loaded in memory, reuse it
        if (self::$cachedMenu !== null) {
            foreach (self::$cachedMenu as $key => $value) {
                $this->$key = $value;
            }
            return;
        }

        // Load from cache
        $cache = Services::cache();
        $tenantId = Tenant::id();
        $cachedData = $cache->get((int)$tenantId . '_mobileBottom_menuConfig');

        if ($cachedData) {
            self::$cachedMenu = $cachedData;
        } else {
            // Load from DB if not in cache
            $db = db_connect();
            if (is_null($tenantId)) {
                $menu = $db->query("SELECT * FROM menuConfig WHERE tenantId IS NULL AND menuLocation='mobileBottom' ORDER BY orderId ASC")->getResultArray();
            } else {
                $menu = $db->query("SELECT * FROM menuConfig WHERE tenantId = $tenantId AND menuLocation='mobileBottom' ORDER BY orderId ASC")->getResultArray();
            }

            $prefixMenus = [];
            $dynamicDashboard = [];

            //add default prefix menus
            $prefixMenus[] = [
                'id'         => 0,
                'title'      => "Home",
                'url'        => base_url(),
                'icon'       => "fa fa-home",
                'class'      => '',
                // 'module'     => '',
                // 'permissions' => '',
                'attributes' => '',
                'children'   => $dynamicDashboard
            ];

            $mainMenus = $this->buildNestedMenu($menu);

            $suffixMenus = [];

            // $suffixMenus[] = [
            //     'id'         => 0,
            //     'title'      => "Logout",
            //     'url'        => "#",
            //     'icon'       => "fa fa-sign-out",
            //     'class'      => 'appLogOut',
            //     'attributes' => '',
            //     'children'   => []
            // ];

            $menuData['items'] = array_merge($prefixMenus, $mainMenus, $suffixMenus);
            $menuData['items'] = json_decode(json_encode($menuData['items']));

            // Cache the configuration for 1 hour
            $cache->save((int)$tenantId . '_mobileBottom_menuConfig', $menuData, 3600);

            self::$cachedMenu = $menuData;
        }

        // Assign config values to object properties
        foreach (self::$cachedMenu as $key => $value) {
            $this->$key = $value;
        }
    }

    private function buildNestedMenu($menuItems, $parentId = null)
    {
        $menuTree = [];

        foreach ($menuItems as $menuItem) {
            if ($menuItem['parentId'] == $parentId) {
                $children = $this->buildNestedMenu($menuItems, $menuItem['menuConfigId']);

                $url = $menuItem['url'];
                //if url is not # or javascript:; then add base url
                if ($url == '' or $url == 'javascript:;' or $url == '#' or is_null($url)) {
                    $url = 'javascript:;';
                } else {
                    $url = base_url($url);
                }

                $temp = [
                    'id'         => $menuItem['menuConfigId'],
                    'title'      => $menuItem['title'],
                    'url'        => $url,
                    'icon'       => $menuItem['icon'],
                    'class'      => $menuItem['class'],
                    'module'     => $menuItem['module'],
                    'isPopup'    => $menuItem['isPopup'] ?? false,
                    'permissions' => explode(',', $menuItem['permissions']),
                    'attributes' => $menuItem['attributes'],
                    'children'   => $children
                ];

                if (empty($menuItem['module']) || empty($menuItem['permissions'])) {
                    unset($temp['module']);
                    unset($temp['permissions']);
                }

                $menuTree[] = $temp;
            }
        }

        return $menuTree;
    }
}
