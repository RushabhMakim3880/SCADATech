<?php

$routes->group("UiTagMaster", ['filter' => 'cache', "namespace" => '\Modules\Frontend\UiTagMaster\Controllers'], function ($routes) {
    $routes->get("addUiTag/(:any)", "UiTagMaster::addUiTag/$1");
    $routes->get("addUiTag", "UiTagMaster::addUiTag");
    $routes->get("manageUiTag", "UiTagMaster::manageUiTag");
});
