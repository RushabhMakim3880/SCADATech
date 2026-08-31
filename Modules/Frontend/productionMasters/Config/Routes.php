<?php

$routes->group("productionMasters", ['filter' => 'cache', "namespace" => '\Modules\Frontend\productionMasters\Controllers'], function ($routes) {
    $routes->get("manageProductionmaster", "productionMasters::manageProductionmaster");
});
