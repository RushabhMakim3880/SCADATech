<?php

$routes->group("samples", ["namespace" => "\Modules\Frontend\Samples\Controllers"], function ($routes) {
    $routes->get('/', 'Samples::index');
    $routes->post('/', 'Samples::index');

    $routes->get('(:any)', 'Samples::$1');
    $routes->post('(:any)', 'Samples::$1');

    $routes->get("addSampleNew", "Samples::addSampleNew");
    $routes->get("editSampleNew/(:any)", "Samples::editSampleNew/$1");
    $routes->get("manageSampleNew", "Samples::manageSampleNew");
});
