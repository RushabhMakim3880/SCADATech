<?php

$routes->group('api/system', ['namespace' => '\Modules\Backend\System\Controllers'], function ($routes) {
    $routes->get('dashboardTemplate/(:any)', 'System::dashboardTemplate/$1');
    $routes->post('dashboardTemplate/(:any)', 'System::dashboardTemplate/$1');
    // dashboardLayout
    $routes->get('dashboardLayout/(:any)', 'System::dashboardLayout/$1');
    $routes->post('dashboardLayout/(:any)', 'System::dashboardLayout/$1');

    // dashboardData
    $routes->post('dashboardData', 'System::dashboardData');

    // dashboardTemplates
    $routes->get('dashboardTemplates', 'System::dashboardTemplates');

    $routes->get('getAppConfig/(:any)', 'System::getAppConfig/$1');
    $routes->post('saveAppConfig/(:any)', 'System::saveAppConfig/$1');

    $routes->get('getProjectName', 'System::getProjectName');
    $routes->post('getProjectName', 'System::getProjectName');


    // Logo & Background

    $routes->get('getLogoAndBg', 'System::getLogoAndBg');
    $routes->post('getLogoAndBg', 'System::getLogoAndBg');

    $routes->get('uploadapk', 'System::uploadapk');
    $routes->post('uploadapk', 'System::uploadapk');

    // resetManageTableColumnSettings
    $routes->get('resetManageTableColumnSettings/(:any)', 'System::resetManageTableColumnSettings/$1');

    // loginCheck
    $routes->get('loginCheck', 'System::loginCheck');

    // testApi
    $routes->get('testApi/(:any)', 'System::testApi/$1'); // Test API
});


$routes->group('api/auth', ['namespace' => '\Modules\Backend\System\Controllers'], function ($routes) {
    // $routes->post('register', 'Auth::register');
    $routes->post('login', 'Auth::login');
    $routes->get('logout', 'Auth::logout');
    $routes->post('resetPassword', 'Auth::resetPassword');
    $routes->post('verifyTotp', 'Auth::verifyTotp');
    $routes->get('refreshToken', 'Auth::refreshToken');
});


$routes->group('api/users', ['namespace' => '\Modules\Backend\System\Controllers'], function ($routes) {
    $routes->get('getList', 'Users::getList');         // List users
    $routes->post('getList', 'Users::getList');         // List users

    $routes->get('get/(:any)', 'Users::get/$1'); // Get user
    // save
    $routes->post('save/(:any)', 'Users::save/$1'); // Save user

    // delete
    $routes->post('delete/(:any)', 'Users::delete/$1'); // Delete user

    // groups
    $routes->get('groups', 'Users::groups'); // List groups

    // getDataTableColumns
    $routes->get('getDataTableColumns/(:any)', 'Users::getDataTableColumns/$1'); // Get data table
    // getDataTableData
    $routes->post('getDataTableData', 'Users::getDataTableData'); // Get data table

    // saveUserSettings
    $routes->post('saveUserSettings/(:any)', 'Users::saveUserSettings/$1'); // Save user settings

    // testDropDown
    $routes->post('testDropDown', 'Users::testDropDown'); // Test dropdown

    // resetLock
    $routes->get('resetLock/(:any)', 'Users::resetLock/$1'); // Reset lock

    // singleSignonToken
    $routes->get('singleSignonToken/(:any)', 'Users::singleSignonToken/$1');
    // twoFaToken
    $routes->get('twoFaToken/(:any)', 'Users::twoFaToken/$1');
    // userActive
    $routes->get('changeUserStatus/(:any)', 'Users::changeUserStatus/$1');

    // getGroups
    $routes->get('getGroups', 'Users::getGroups');

    // loadGroupPermissions
    $routes->get('loadGroupPermissions/(:any)', 'Users::loadGroupPermissions/$1');

    // saveGroupPermissions
    $routes->post('saveGroupPermissions', 'Users::saveGroupPermissions');

    //get user name
    $routes->get('getUsersList', 'Users::getUsersList');

    $routes->get('getSalesUsersList', 'Users::getSalesUsersList');
});


$routes->group('api/MenuConfig', ['namespace' => '\Modules\Backend\System\Controllers'], function ($routes) {
    $routes->get('getList', 'MenuConfig::getList');         // List users
    $routes->post('getList', 'MenuConfig::getList');         // List users

    $routes->get('get/(:segment)/(:segment)', 'MenuConfig::get/$1/$2');
    $routes->post('save/(:segment)/(:segment)', 'MenuConfig::save/$1/$2');

    // getRoutes
    $routes->get('getRoutes', 'MenuConfig::getRoutes'); // Get data table

    // getPermissions
    $routes->get('getPermissions/(:any)', 'MenuConfig::getPermissions/$1'); // Get data table

    // restoreDefault
    $routes->get('restoreDefault/(:segment)/(:segment)', 'MenuConfig::restoreDefault/$1/$2'); // Get data table
});

$routes->group('api/locationMaster', ['namespace' => '\Modules\Backend\System\Controllers'], function ($routes) {

    $routes->get('getDataTableColumns/(:any)', 'LocationMaster::getDataTableColumns/$1'); // Get data table
    $routes->post('getDataTableData', 'LocationMaster::getDataTableData'); // Get data table
    $routes->get('get/(:any)', 'LocationMaster::get/$1'); // Get user
    $routes->post('save/(:any)', 'LocationMaster::save/$1'); // Save user

    $routes->post('getLocations/(:any)', 'LocationMaster::getLocations/$1');
    $routes->post('getLocations', 'LocationMaster::getLocations');

    // getCurrencyMasterList
    $routes->get('getCurrencyMasterList', 'LocationMaster::getCurrencyMasterList');
});

$routes->group('api/StatusMaster', ['namespace' => '\Modules\Backend\System\Controllers'], function ($routes) {


    $routes->get('getItem', 'StatusMaster::getItem');

    $routes->get('get/(:any)', 'StatusMaster::get/$1');
    $routes->post('save/(:any)', 'StatusMaster::save/$1');

    $routes->get('getStatusList/(:any)', 'StatusMaster::getStatusList/$1');

    // getDataTableColumns
    $routes->get('getDataTableColumns/(:any)', 'StatusMaster::getDataTableColumns/$1'); // Get data table
    // getDataTableData
    $routes->post('getDataTableData', 'StatusMaster::getDataTableData'); // Get data table
});
$routes->group('api/customStatusMaster', ['namespace' => '\Modules\Backend\System\Controllers'], function ($routes) {
    //get Party Dropdown
    $routes->get('getStatusData', 'CustomStatusMaster::getStatusData');

    $routes->get('getDataTableColumns/(:any)', 'CustomStatusMaster::getDataTableColumns/$1'); // Get data table
    $routes->post('getDataTableData', 'CustomStatusMaster::getDataTableData'); // Get data table
    $routes->get('get/(:any)', 'CustomStatusMaster::get/$1'); // Get user
    $routes->post('save/(:any)', 'CustomStatusMaster::save/$1'); // Save user

    $routes->get('changeStatus/(:any)', 'CustomStatusMaster::changeStatus/$1'); // change Status
    $routes->get('changePrimaryStatus/(:any)', 'CustomStatusMaster::changePrimaryStatus/$1'); // change Primary

});


$routes->group('api/tenantMaster', ['namespace' => '\Modules\Backend\System\Controllers'], function ($routes) {
    $routes->get('getLocation', 'TenantMaster::getLocation');
    $routes->post('getLocation', 'TenantMaster::getLocation');

    $routes->get('getDataTableColumns/(:any)', 'TenantMaster::getDataTableColumns/$1'); // Get data table
    $routes->post('getDataTableData', 'TenantMaster::getDataTableData'); // Get data table
    $routes->get('get/(:any)', 'TenantMaster::get/$1'); // Get user
    $routes->post('save/(:any)', 'TenantMaster::save/$1'); // Save user
    // $routes->(['GET'], 'getLocation/(:any)', 'TenantMaster::getLocation/$1'); // change Status
    $routes->get('changeStatus/(:any)', 'TenantMaster::changeStatus/$1'); // change Primary
    $routes->post('changeStatus/(:any)', 'TenantMaster::changeStatus/$1'); // change Primary

    $routes->get('tenantDropdown', 'TenantMaster::tenantDropdown');
});


$routes->group('api/webAuth', ['namespace' => '\Modules\Backend\System\Controllers'], function ($routes) {
    $routes->post('register', 'Webauth::register'); // Get data table
    $routes->post('verify', 'Webauth::verify'); // Get data table

    $routes->post('loginStart', 'Webauth::loginStart'); // Get data table
    $routes->post('login', 'Webauth::login'); // Get data table
});

$routes->group('api/GlobalSearch', ['namespace' => '\Modules\Backend\System\Controllers'], function ($routes) {
    $routes->post('/', 'GlobalSearch::index');
});

$routes->group('api/trustedDevice', ['namespace' => '\Modules\Backend\System\Controllers'], function ($routes) {
    $routes->get('getDataTableColumns/(:any)', 'TrustedDevice::getDataTableColumns/$1');

    $routes->post('getDataTableData', 'TrustedDevice::getDataTableData');

    $routes->get('deleteTrustedDevice/(:any)', 'TrustedDevice::deleteTrustedDevice/$1');

    $routes->get('changeTrustedDeviceStatus/(:any)', 'TrustedDevice::changeTrustedDeviceStatus/$1'); // change status
});
