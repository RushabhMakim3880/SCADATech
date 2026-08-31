<?php

$routes->group('api/setting', ['namespace' => '\Modules\Backend\Setting\Controllers'], function ($routes) {

    $routes->get('getSetting/(:any)', 'Setting::getSetting/$1');
    $routes->post('saveSetting/(:any)', 'Setting::saveSetting/$1');
    $routes->get('getCompanyMasterSetting', 'Setting::getCompanyMasterSetting');
    $routes->get('getCompanyMasterSetting/(:any)', 'Setting::getCompanyMasterSetting/$1');
    $routes->post('saveCompanyMasterSetting', 'Setting::saveCompanyMasterSetting');
    $routes->post('saveCompanyMasterSetting/(:any)', 'Setting::saveCompanyMasterSetting/$1');

    // DataTable and CRUD routes for companyMasterSettings
    $routes->get('getDataTableColumns/(:any)', 'Setting::getDataTableColumns/$1');
    $routes->post('getDataTableData', 'Setting::getDataTableData');
    $routes->get('getCompanyMasterSettingById/(:any)', 'Setting::getCompanyMasterSettingById/$1');
    $routes->post('saveCompanyMasterSettingItem', 'Setting::saveCompanyMasterSettingItem');
    $routes->post('saveCompanyMasterSettingItem/(:any)', 'Setting::saveCompanyMasterSettingItem/$1');
    $routes->delete('deleteCompanyMasterSetting/(:any)', 'Setting::deleteCompanyMasterSetting/$1');
    $routes->post('deleteCompanyMasterSetting/(:any)', 'Setting::deleteCompanyMasterSetting/$1');
});
