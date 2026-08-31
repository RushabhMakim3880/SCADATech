<?php
$routes->group('api/PlcMaster', ['filter' => 'jwt', 'namespace' => '\Modules\Backend\PlcMaster\Controllers'], function ($routes) {
    // getDataTableColumns ,getDataTableData
    $routes->get('getDataTableColumns/(:any)', 'PlcMaster::getDataTableColumns/$1'); // Get data table
    $routes->post('getDataTableData', 'PlcMaster::getDataTableData'); // Get data table
    // get ,save
    $routes->get('get/(:any)', 'PlcMaster::get/$1'); // Get user
    $routes->post('save/(:any)', 'PlcMaster::save/$1'); // Save user
    $routes->get('delete/(:any)', 'PlcMaster::delete/$1'); // Get user
    $routes->get('changeStatus/(:any)', 'PlcMaster::changeStatus/$1'); // change Status
    $routes->get('getPlcList', 'PlcMaster::getPlcList'); // Get Plc Name
    $routes->get('getPlcTagList', 'PlcMaster::getPlcTagList'); // Get Plc Tag List
    $routes->get('tagDetails/(:any)', 'PlcMaster::tagDetails/$1'); //Details



});

$routes->group('api/PlcTagGroupMaster', ['filter' => 'jwt', 'namespace' => '\Modules\Backend\PlcMaster\Controllers'], function ($routes) {
    // getDataTableColumns ,getDataTableData
    $routes->get('getDataTableColumns/(:any)', 'PlcTagGroupMaster::getDataTableColumns/$1'); // Get data table
    $routes->post('getDataTableData', 'PlcTagGroupMaster::getDataTableData'); // Get data table
    // get ,save
    $routes->get('get/(:any)', 'PlcTagGroupMaster::get/$1'); // Get user
    $routes->post('save/(:any)', 'PlcTagGroupMaster::save/$1'); // Save user
    $routes->get('delete/(:any)', 'PlcTagGroupMaster::delete/$1'); // Get user
    $routes->get('getTagGroupList', 'PlcTagGroupMaster::getTagGroupList'); // Get Machine Type List

});

$routes->group(
    'api/PlcTagMaster',
    ['filter' => 'jwt', 'namespace' => '\Modules\Backend\PlcMaster\Controllers'],
    function ($routes) {
        $routes->get('getDataTableColumns/(:any)', 'PlcTagMaster::getDataTableColumns/$1'); // Get data table
        $routes->post('getDataTableData', 'PlcTagMaster::getDataTableData'); // Get data table
        // get ,save
        $routes->get('get/(:any)', 'PlcTagMaster::get/$1'); // Get user
        $routes->post('save/(:any)', 'PlcTagMaster::save/$1'); // Save user
        $routes->get('delete/(:any)', 'PlcTagMaster::delete/$1'); // Get user

        $routes->get('switchDataType/(:any)', 'PlcTagMaster::switchDataType/$1'); // Switch dataType
        $routes->get('switchRegisterType/(:any)', 'PlcTagMaster::switchRegisterType/$1'); // Switch registerType
        $routes->get('switchReadWrite/(:any)', 'PlcTagMaster::switchReadWrite/$1'); // Switch readWrite
        $routes->get('toggleIsActive/(:any)', 'PlcTagMaster::toggleIsActive/$1'); // Toggle isActive
    }
);
