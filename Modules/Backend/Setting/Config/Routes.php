<?php

$routes->group('api/setting', ['namespace' => '\Modules\Backend\Setting\Controllers'], function ($routes) {

    $routes->get('getSetting/(:any)', 'Setting::getSetting/$1');
    $routes->post('saveSetting/(:any)', 'Setting::saveSetting/$1');
});
