<?php

$routes->group("ItemRecipeMaster", ['filter' => 'cache', "namespace" => '\Modules\Frontend\ItemRecipeMaster\Controllers'], function ($routes) {
    $routes->get("addItemrecipemaster", "ItemRecipeMaster::addItemrecipemaster");
    $routes->get("editItemrecipemaster/(:any)", "ItemRecipeMaster::editItemrecipemaster/$1");
    $routes->get("manageItemrecipemaster", "ItemRecipeMaster::manageItemrecipemaster");

    $routes->get("importFile", "ItemRecipeMaster::importFile");
});
