<?php

$routes->group("jobCards", ['filter' => 'cache', "namespace" => '\Modules\Frontend\jobCards\Controllers'], function ($routes) {
    $routes->get("editJobcard/(:any)", "jobCards::addJobcard/$1");
    $routes->get("addJobcard", "jobCards::addJobcard");
    $routes->get("manageJobcard", "jobCards::manageJobcard");
});
