<?php
$routes->group('api/jobCards', ['filter' => 'jwt', 'namespace' => '\Modules\Backend\jobCards\Controllers'], function ($routes) {
    // getDataTableColumns ,getDataTableData
    $routes->get('getDataTableColumns/(:any)', 'jobCards::getDataTableColumns/$1'); // Get data table
    $routes->post('getDataTableData', 'jobCards::getDataTableData'); // Get data table
    // get ,save
    $routes->get('get/(:any)', 'jobCards::get/$1'); // Get user
    $routes->post('save/(:any)', 'jobCards::save/$1'); // Save user
    $routes->get('delete/(:any)', 'jobCards::delete/$1'); // Get user
    $routes->get('cancellStatus/(:any)', 'jobCards::cancellStatus/$1'); // Cancell Status
});
