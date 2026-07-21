<?php
$routes->group('api/AalarmModules', ['filter' => 'jwt', 'namespace' => '\Modules\Backend\AalarmModules\Controllers'], function ($routes) {
    // getDataTableColumns ,getDataTableData
    $routes->get('getDataTableColumns/(:any)', 'AalarmModules::getDataTableColumns/$1'); // Get data table
    $routes->post('getDataTableData', 'AalarmModules::getDataTableData'); // Get data table
    // get ,save
    $routes->get('get/(:any)', 'AalarmModules::get/$1'); // Get user
    $routes->post('save/(:any)', 'AalarmModules::save/$1'); // Save user
    $routes->get('delete/(:any)', 'AalarmModules::delete/$1'); // Get user

    $routes->get('toggleIsActive/(:any)', 'AalarmModules::toggleIsActive/$1'); // Toggle isActive

});


$routes->group('api/AlarmLog', ['filter' => 'jwt', 'namespace' => '\Modules\Backend\AalarmModules\Controllers'], function ($routes) {
    // getDataTableColumns ,getDataTableData
    $routes->get('getDataTableColumns/(:any)', 'AlarmLog::getDataTableColumns/$1'); // Get data table
    $routes->post('getDataTableData', 'AlarmLog::getDataTableData'); // Get data table
});
