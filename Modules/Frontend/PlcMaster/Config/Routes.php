<?php

$routes->group("PlcMaster", ['filter' => 'cache', "namespace" => '\Modules\Frontend\PlcMaster\Controllers'], function ($routes) {
    $routes->get("addPlcMaster/(:any)", "PlcMaster::addPlcMaster/$1");
    $routes->get("addPlcMaster", "PlcMaster::addPlcMaster");
    $routes->get("managePlcMaster", "PlcMaster::managePlcMaster");

    $routes->get("editPlcMaster/(:any)", "PlcMaster::editPlcMaster/$1");
});


//
$routes->group("PlcTagMaster", ['filter' => 'cache', "namespace" => '\Modules\Frontend\PlcMaster\Controllers'], function ($routes) {
    $routes->get("editPlctagmaster/(:any)", "PlcTagMaster::addPlctagmaster/$1");
    $routes->get("addPlctagmaster", "PlcTagMaster::addPlctagmaster");
    $routes->get("managePlctagmaster/(:any)", "PlcTagMaster::managePlctagmaster/$1");
});

//

$routes->group("PlcTagGroupMaster", ['filter' => 'cache', "namespace" => '\Modules\Frontend\PlcMaster\Controllers'], function ($routes) {
    $routes->get("addPlcTag/(:any)", "PlcTagGroupMaster::addPlcTag/$1");
    $routes->get("addPlcTag", "PlcTagGroupMaster::addPlcTag");
    $routes->get("managePlcTag", "PlcTagGroupMaster::managePlcTag");
});
