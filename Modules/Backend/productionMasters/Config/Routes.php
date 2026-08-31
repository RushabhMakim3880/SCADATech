<?php
$routes->group('api/productionMasters', ['filter' => 'jwt', 'namespace' => '\Modules\Backend\productionMasters\Controllers'], function ($routes) {
    // getDataTableColumns ,getDataTableData
    $routes->get('getDataTableColumns/(:any)', 'productionMasters::getDataTableColumns/$1'); // Get data table
    $routes->post('getDataTableData', 'productionMasters::getDataTableData'); // Get data table
});
