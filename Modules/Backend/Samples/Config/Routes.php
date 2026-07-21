<?php
$routes->group('api/samples', ['namespace' => '\Modules\Backend\Samples\Controllers'], function ($routes) {
    // Dynamic route for all methods and segments

    $routes->get("(:any)", "Samples::$1");
    $routes->post("(:any)", "Samples::$1");
});
$routes->group('api/newSample', ['namespace' => '\Modules\Backend\Samples\Controllers'], function ($routes) {
    // $routes->match(['GET', 'POST'], '(:any)', 'newSample::$1');
    $routes->get('get/(:any)', 'newSample::get/$1'); // Get user
    // save
    $routes->post('save/(:any)', 'newSample::save/$1'); // Save user
    $routes->get('getDataTableColumns/(:any)', 'newSample::getDataTableColumns/$1'); // Get user
    $routes->post('getDataTableData', 'newSample::getDataTableData/$1'); // Get user

    $routes->get('changeStatus/(:any)', 'newSample::changeStatus/$1');
    // loadSimpleDropdown
    $routes->get('loadSimpleDropdown', 'newSample::loadSimpleDropdown');

    // loadSimpleDropdownMultiple
    $routes->get('loadSimpleDropdownMultiple', 'newSample::loadSimpleDropdownMultiple');

    // generatePdf
    $routes->get('generatePdf/(:any)', 'newSample::generatePdf/$1');

    // changeStatus
    $routes->get('changeStatus/(:any)', 'newSample::changeStatus/$1');

    // switchPriority
    $routes->get('switchPriority/(:any)', 'newSample::switchPriority/$1');

    // infoPopupExample
    $routes->get('infoPopupExample/(:any)', 'newSample::infoPopupExample/$1');

    // infoFormExample
    $routes->get('infoFormExample/(:any)', 'newSample::infoFormExample/$1');
    $routes->post('infoFormExample/(:any)', 'newSample::infoFormExample/$1');


    $routes->get('getAjaxItem', 'newSample::getAjaxItem');
    $routes->post('getAjaxItem', 'newSample::getAjaxItem');
});
