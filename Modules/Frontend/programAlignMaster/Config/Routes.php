<?php

$routes->group("programAlignMaster", ['filter' => 'cache', "namespace" => '\Modules\Frontend\programAlignMaster\Controllers'], function ($routes) {
    $routes->get("manageProgramalignmaster", "programAlignMaster::manageProgramalignmaster");
});