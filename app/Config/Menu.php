<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use App\Libraries\Tenant;
use App\Libraries\Auth;


class Menu extends BaseConfig
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
        // $cachedData = $cache->get((int)$tenantId . '_sidebarMain_menuConfig');
        $cachedData = false;

        if ($cachedData) {
            self::$cachedMenu = $cachedData;
        } else {
            // Load from DB if not in cache
            $db = db_connect();
            if (is_null($tenantId)) {
                $menu = $db->query("SELECT * FROM menuConfig WHERE tenantId IS NULL AND menuLocation='sidebarMain' ORDER BY orderId ASC")->getResultArray();
            } else {
                $menu = $db->query("SELECT * FROM menuConfig WHERE tenantId = $tenantId AND menuLocation='sidebarMain' ORDER BY orderId ASC")->getResultArray();
            }

            $prefixMenus = [];
            $dynamicDashboard = [];

            $db = db_connect();
            $dashboards = $db->table("dashboardLayouts")->where('tenantId', $tenantId)->get()->getResult();
            foreach ($dashboards as $d) {
                $json = json_decode($d->layout);
                if ($json and count($json) > 0) {
                    $dynamicDashboard[] = [
                        'title' => $d->dashboardName,
                        'icon'  => 'fa fa-dashboard',
                        'class' => '',
                        'url'   => '/home/viewDashboard/' . $d->uid,
                        'children' => []
                    ];
                }
            }

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

            $suffixMenus[] = [
                'title' => 'Manage Users',
                'icon'  => 'fa fa-users',
                'class' => '',
                'module' => 'userMaster',
                'permissions' => ['viewAll', 'add', 'view', 'edit', 'delete', 'deleteAll'],
                'url'   => '#',
                'children' => [
                    [
                        'title' => 'Add User',
                        'icon'  => 'fa fa-user-plus',
                        'url'   => base_url('users/addUser'),
                        'module' => 'userMaster',
                        'permissions' => ['add'],
                        'children' => []
                    ],
                    [
                        'title' => 'Manage Users',
                        'icon'  => 'fa fa-cogs',
                        'module' => 'userMaster',
                        'permissions' => ['viewAll', 'view', 'edit', 'delete', 'deleteAll'],
                        'url'   => base_url('users/manageUsers'),
                        'children' => []
                    ],
                    [
                        'title' => 'Groups Permissions',
                        'icon'  => 'fa fa-user-cog',
                        'module' => 'userMaster',
                        'permissions' => ['managePermission'],
                        'url'   => base_url('system/manageGroupPermissions'),
                        'children' => []

                    ]
                ]
            ];



            $suffixMenus[] = [
                'title' => 'Samples',
                'icon'  => 'fa fa-dashboard',
                'class' => '',
                'module' => 'superSaasAdmin',
                'permissions' => ['sampleDashboard'],
                'attributes' => '',
                'url'   => 'javascript:;',
                'children' => [
                    [
                        'title' => 'Dashboard',
                        'icon'  => 'fa fa-dashboard',
                        'url'   => base_url('samples'),
                        'children' => []
                    ],
                    [
                        'title' => 'SSE Live Stream Test',
                        'icon'  => 'fa fa-cogs',
                        'url'   => base_url('samples/sse'),
                        'children' => []
                    ],
                ]
            ];

            $suffixMenus[] = [
                'title' => 'Super Admin Tools',
                'icon'  => 'fa fa-cogs',
                'class' => '',
                'module' => 'superSaasAdmin',
                'permissions' => ['dynamicDashboard', 'sendNotification', 'manageMenuConfig', 'manageAppConfig', 'manageBranding', 'manageLocationMaster', 'manageStatus', 'manageCustomStatusFields', 'uploadapk'],
                'url'   => '#',
                'children' => [
                    [
                        'title' => 'Dashboard Designer',
                        'icon'  => 'fa fa-users',
                        'module' => 'superSaasAdmin',
                        'permissions' => ['dynamicDashboard'],
                        'url'   => base_url('home/dashboardDesigner'),
                        'children' => []
                    ],
                    [
                        'title' => 'View Dashboard',
                        'icon'  => 'fa fa-cogs',
                        'module' => 'superSaasAdmin',
                        'permissions' => ['dynamicDashboard'],
                        'url'   => base_url('home/viewDashboard/1'),
                        'children' => []
                    ],
                    [
                        'title' => 'Send Notification',
                        'icon'  => 'fa fa-cogs',
                        'module' => 'superSaasAdmin',
                        'permissions' => ['sendNotification'],
                        'url'   => base_url('home/sendNotification'),
                        'children' => []
                    ],
                    [
                        'title' => 'Menu Configuration',
                        'icon'  => 'fas fa-chevron-circle-down',
                        'module' => 'superSaasAdmin',
                        'permissions' => ['manageMenuConfig'],
                        'url'   => base_url('menuConfig/addMenuConfig'),
                        'children' => []

                    ],

                    [
                        'title' => 'App Config',
                        'icon'  => 'fa fa-cogs',
                        'module' => 'superSaasAdmin',
                        'permissions' => ['manageAppConfig'],
                        'url'   => base_url('menuConfig/appConfig'),
                        'children' => []
                    ],
                    [
                        'title' => 'Logo & BG',
                        'icon'  => 'far fa-images',
                        'module' => 'superSaasAdmin',
                        'permissions' => ['manageBranding'],
                        'url'   => base_url('system/addLogoBg'),
                        'children' => []
                    ],
                    [
                        'title' => 'Location Master',
                        'icon'  => 'fas fa-map-marker-alt',
                        'module' => 'superSaasAdmin',
                        'permissions' => ['manageLocationMaster'],
                        'url'   => base_url('locationMaster/manageLocationMaster'),
                        'children' => []
                    ],
                    [
                        'title' => 'Status Master',
                        'icon'  => 'fas fa-map-marker-alt',
                        'module' => 'superSaasAdmin',
                        'permissions' => ['manageStatus'],
                        'url'   => base_url('statusMaster/manageStatus'),
                        'children' => []
                    ],
                    [
                        'title' => 'Custom Field Master',
                        'icon'  => 'fas fa-map-marker-alt',
                        'module' => 'superSaasAdmin',
                        'permissions' => ['manageCustomStatusFields'],
                        'url'   => base_url('customStatusMaster/manageCustomStatus'),
                        'children' => []
                    ],
                    [
                        'title' => 'Tenant Master',
                        'icon'  => 'fas fa-map-marker-alt',
                        'module' => 'tenantMaster',
                        'permissions' => ['view'],
                        'url'   => base_url('tenantMaster/manageTenant'),
                        'children' => []
                    ],

                    [
                        'title' => 'Upload APK',
                        'icon'  => 'fa fa-upload',
                        'module' => 'superSaasAdmin',
                        'permissions' => ['uploadapk'],
                        'url'   => base_url('system/uploadapk'),
                        'children' => []
                    ],
                ]
            ];

            // menu for download apk
            $tenantId = Auth::user()->tenantId;

            $uploadPath = ROOTPATH . 'public/uploads/app';
            if (is_dir($uploadPath) && count(glob($uploadPath . '/*.apk')) > 0) {
                $suffixMenus[] = [
                    'id'         => 0,
                    'title'      => "Download APK",
                    'url'        => base_url('uploads/app/launchpad.apk'),
                    'icon'       => "fa fa-download",
                    'class'      => '',
                    'attributes' => '',
                    'children'   => []
                ];
            }

            $suffixMenus[] = [
                'id'         => 0,
                'title'      => "Logout",
                'url'        => "#",
                'icon'       => "fa fa-sign-out",
                'class'      => 'appLogOut',
                'attributes' => '',
                'children'   => []
            ];

            $menuData['items'] = array_merge($prefixMenus, $mainMenus, $suffixMenus);
            $menuData['items'] = json_decode(json_encode($menuData['items']));

            // Cache the configuration for 1 hour
            // $cache->save((int)$tenantId . '_sidebarMain_menuConfig', $menuData, 3600);

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
