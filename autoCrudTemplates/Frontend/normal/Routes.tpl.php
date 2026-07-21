<?php

$routes->group("{{MODULE_NAME}}", ['filter' => 'cache', "namespace" => '\Modules\Frontend\{{MODULE_NAME}}\Controllers'], function ($routes) {
    $routes->get("add{{ITEM_NAME}}", "{{MODULE_NAME}}::add{{ITEM_NAME}}");
    $routes->get("edit{{ITEM_NAME}}/(:any)", "{{MODULE_NAME}}::edit{{ITEM_NAME}}/$1");
    $routes->get("manage{{ITEM_NAME}}", "{{MODULE_NAME}}::manage{{ITEM_NAME}}");
});
