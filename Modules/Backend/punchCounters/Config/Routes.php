<?php
$routes->group('api/punchCounters', ['filter' => 'jwt', 'namespace' => '\Modules\Backend\punchCounters\Controllers'], function ($routes) {
    // getDataTableColumns ,getDataTableData
    $routes->get('getDataTableColumns/(:any)', 'punchCounters::getDataTableColumns/$1'); // Get data table
    $routes->post('getDataTableData', 'punchCounters::getDataTableData'); // Get data table
});
