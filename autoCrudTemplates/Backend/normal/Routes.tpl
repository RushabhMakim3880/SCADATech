<?php
$routes->group('api/{{MODULE_NAME}}', ['filter' => 'jwt', 'namespace' => '\Modules\Backend\{{MODULE_NAME}}\Controllers'], function ($routes) {
    // getDataTableColumns ,getDataTableData
    $routes->get('getDataTableColumns/(:any)', '{{MODULE_NAME}}::getDataTableColumns/$1'); // Get data table
    $routes->post('getDataTableData', '{{MODULE_NAME}}::getDataTableData'); // Get data table
    // get ,save
    $routes->get('get/(:any)', '{{MODULE_NAME}}::get/$1'); // Get user
    $routes->post('save/(:any)', '{{MODULE_NAME}}::save/$1'); // Save user
    $routes->get('delete/(:any)', '{{MODULE_NAME}}::delete/$1'); // Get user

    {{CUSTOM_BACKEND_ROUTES}}
});
