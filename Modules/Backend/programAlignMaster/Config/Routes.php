<?php
$routes->group('api/programAlignMaster', ['filter' => 'jwt', 'namespace' => '\Modules\Backend\programAlignMaster\Controllers'], function ($routes) {
    // getDataTableColumns ,getDataTableData
    $routes->get('getDataTableColumns/(:any)', 'programAlignMaster::getDataTableColumns/$1'); // Get data table
    $routes->post('getDataTableData', 'programAlignMaster::getDataTableData'); // Get data table
    $routes->get('getKpiData', 'programAlignMaster::getKpiData'); // Get KPI data
    $routes->post('getKpiData', 'programAlignMaster::getKpiData'); // Get filtered KPI data
});
