<?php
$routes->group('api/MachineMaster', ['filter' => 'jwt', 'namespace' => '\Modules\Backend\MachineMaster\Controllers'], function ($routes) {
    // getDataTableColumns ,getDataTableData
    $routes->get('getDataTableColumns/(:any)', 'MachineMaster::getDataTableColumns/$1'); // Get data table
    $routes->post('getDataTableData', 'MachineMaster::getDataTableData'); // Get data table
    // get ,save
    $routes->get('get/(:any)', 'MachineMaster::get/$1'); // Get user
    $routes->post('save/(:any)', 'MachineMaster::save/$1'); // Save user
    $routes->get('delete/(:any)', 'MachineMaster::delete/$1'); // Get user
    $routes->get('changeStatus/(:any)', 'MachineMaster::changeStatus/$1'); // change Status
    $routes->get('getMachineList', 'MachineMaster::getMachineList'); // Get Machine Type List

    // getMachineSetup
    $routes->get("getMachineSetup", "MachineMaster::getMachineSetup");
    $routes->post("saveMachineSetup", "MachineMaster::saveMachineSetup");
});

$routes->group('api/MachineOperationConfig', ['filter' => 'jwt', 'namespace' => '\Modules\Backend\MachineMaster\Controllers'], function ($routes) {
    // getDataTableColumns ,getDataTableData
    $routes->get('getDataTableColumns/(:any)', 'MachineOperationConfig::getDataTableColumns/$1'); // Get data table
    $routes->post('getDataTableData', 'MachineOperationConfig::getDataTableData'); // Get data table
    // get ,save
    $routes->get('get/(:any)', 'MachineOperationConfig::get/$1'); // Get user
    $routes->post('save/(:any)', 'MachineOperationConfig::save/$1'); // Save user
    $routes->get('delete/(:any)', 'MachineOperationConfig::delete/$1'); // Get user

    $routes->get('getItemRecipeSteps', 'MachineOperationConfig::getItemRecipeSteps'); // Get Machine Type List




});
