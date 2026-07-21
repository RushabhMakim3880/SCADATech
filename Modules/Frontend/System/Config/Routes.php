<?php
$routes->group("system", ["namespace" => "\Modules\Frontend\System\Controllers"], function ($routes) {
    $routes->get("totpSetup", "System::totpSetup");
    $routes->get("addLogoBg", "System::addLogoBg");  // Login & Background
    // manageGroupPermissions
    $routes->get("manageGroupPermissions", "System::manageGroupPermissions");
    $routes->get("uploadapk", "System::uploadapk");
});


//CustomStatusMaster
$routes->group("customStatusMaster", ['filter' => 'cache', "namespace" => '\Modules\Frontend\System\Controllers'], function ($routes) {
    $routes->get("addCustomStatus", "CustomStatusMaster::addCustomStatus");
    $routes->get("editCustomStatus/(:any)", "CustomStatusMaster::editCustomStatus/$1");
    $routes->get("manageCustomStatus", "CustomStatusMaster::manageCustomStatus");
});

//LocationMaster
$routes->group("locationMaster", ['filter' => 'cache', "namespace" => '\Modules\Frontend\System\Controllers'], function ($routes) {
    $routes->get("addLocationMaster", "LocationMaster::addLocationMaster");
    $routes->get("editLocationMaster/(:any)", "LocationMaster::editLocationMaster/$1");
    $routes->get("manageLocationMaster", "LocationMaster::manageLocationMaster");
});

//StatusMaster
$routes->group("statusMaster", ['filter' => 'cache', "namespace" => '\Modules\Frontend\System\Controllers'], function ($routes) {
    $routes->get("addStatus", "StatusMaster::addStatus");
    $routes->get("editStatus/(:any)", "StatusMaster::editStatus/$1");
    $routes->get("manageStatus", "StatusMaster::manageStatus");
});

//TenantMaster
$routes->group("tenantMaster", ['filter' => 'cache', "namespace" => '\Modules\Frontend\System\Controllers'], function ($routes) {
    $routes->get("addTenant", "TenantMaster::addTenant");
    $routes->get("editTenant/(:any)", "TenantMaster::editTenant/$1");
    $routes->get("manageTenant", "TenantMaster::manageTenant");
});

//Users
$routes->group("users", ['filter' => 'cache', "namespace" => '\Modules\Frontend\System\Controllers'], function ($routes) {
    $routes->get("addUser", "Users::addUser");
    $routes->get("editUser/(:any)", "Users::editUser/$1");
    $routes->get("manageUsers", "Users::manageUsers");
});

//MenuConfig
$routes->group("menuConfig", ['filter' => 'cache', "namespace" => '\Modules\Frontend\System\Controllers'], function ($routes) {
    $routes->get("addMenuConfig", "MenuConfig::addMenuConfig");
    $routes->get("appConfig", "MenuConfig::appConfig");
});


//add route for /docs
$routes->group("docs", ["namespace" => "\Modules\Frontend\System\Controllers"], function ($routes) {
    $routes->get("", "System::docs");
    $routes->get('view', 'System::viewDocs');
    $routes->get('view/(:any)', 'System::viewDocs/$1');
    $routes->get('view/(:any)/(:any)', 'System::viewDocs/$1/$2');
    $routes->get('view/(:any)/(:any)/(:any)', 'System::viewDocs/$1/$2/$3');
    $routes->get('view/(:any)/(:any)/(:any)/(:any)', 'System::viewDocs/$1/$2/$3/$4');
    $routes->get('view/(:any)/(:any)/(:any)/(:any)/(:any)', 'System::viewDocs/$1/$2/$3/$4/$5');
    // Add more levels if needed
});

//add route for /docs
$routes->group("userGuide", ["namespace" => "\Modules\Frontend\System\Controllers"], function ($routes) {
    $routes->get("", "System::userGuide");
    $routes->get('view', 'System::viewUserGuide');
    $routes->get('view/(:any)', 'System::viewUserGuide/$1');
    $routes->get('view/(:any)/(:any)', 'System::viewUserGuide/$1/$2');
    $routes->get('view/(:any)/(:any)/(:any)', 'System::viewUserGuide/$1/$2/$3');
    $routes->get('view/(:any)/(:any)/(:any)/(:any)', 'System::viewUserGuide/$1/$2/$3/$4');
    $routes->get('view/(:any)/(:any)/(:any)/(:any)/(:any)', 'System::viewUserGuide/$1/$2/$3/$4/$5');
    // Add more levels if needed
});

//Trusted Devices
$routes->group("trustedDevice", ['filter' => 'cache', "namespace" => '\Modules\Frontend\System\Controllers'], function ($routes) {
    $routes->get("manageTrustedDevice", "TrustedDevice::manageTrustedDevice");
});
