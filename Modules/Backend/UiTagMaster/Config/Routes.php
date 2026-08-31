<?php
$routes->group('api/UiTagMaster', ['filter' => 'jwt', 'namespace' => '\Modules\Backend\UiTagMaster\Controllers'], function ($routes) {
    // getDataTableColumns ,getDataTableData
    $routes->get('getDataTableColumns/(:any)', 'UiTagMaster::getDataTableColumns/$1'); // Get data table
    $routes->post('getDataTableData', 'UiTagMaster::getDataTableData'); // Get data table
    // get ,save
    $routes->get('get/(:any)', 'UiTagMaster::get/$1'); // Get user
    $routes->post('save/(:any)', 'UiTagMaster::save/$1'); // Save user
    $routes->get('changeStatus/(:any)', 'UiTagMaster::changeStatus/$1'); // change Status
    $routes->get('getUiTag', 'UiTagMaster::getUiTag'); // getUiTag



});
