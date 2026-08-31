<?php

$routes->group("AalarmModules", ['filter' => 'cache', "namespace" => '\Modules\Frontend\AalarmModules\Controllers'], function ($routes) {
    $routes->get("editAlarmconfig/(:any)", "AalarmModules::addAlarmconfig/$1");
    $routes->get("addAlarmconfig", "AalarmModules::addAlarmconfig");
    $routes->get("manageAlarmconfig", "AalarmModules::manageAlarmconfig");
});


$routes->group("AlarmLog", ['filter' => 'cache', "namespace" => '\Modules\Frontend\AalarmModules\Controllers'], function ($routes) {
    $routes->get("manageAlarmlog", "AlarmLog::manageAlarmlog");
});

