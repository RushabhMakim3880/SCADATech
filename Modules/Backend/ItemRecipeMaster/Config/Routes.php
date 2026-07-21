<?php
$routes->group('api/ItemRecipeMaster', ['filter' => 'jwt', 'namespace' => '\Modules\Backend\ItemRecipeMaster\Controllers'], function ($routes) {
    // getDataTableColumns ,getDataTableData
    $routes->get('getDataTableColumns/(:any)', 'ItemRecipeMaster::getDataTableColumns/$1'); // Get data table
    $routes->post('getDataTableData', 'ItemRecipeMaster::getDataTableData'); // Get data table
    // get ,save
    $routes->get('get/(:any)', 'ItemRecipeMaster::get/$1'); // Get user
    $routes->post('save/(:any)', 'ItemRecipeMaster::save/$1'); // Save user

    $routes->get('itemRecipeDetails/(:any)', 'ItemRecipeMaster::itemRecipeDetails/$1'); //Details
    $routes->get('changeStatus/(:any)', 'ItemRecipeMaster::changeStatus/$1'); // change Status


    // getItemRecipeList
    $routes->post('getItemRecipeList', 'ItemRecipeMaster::getItemRecipeList'); // Get item recipe list

    // getProgramDetails
    $routes->get('getProgramDetails/(:any)', 'ItemRecipeMaster::getProgramDetails/$1'); // Get program details

    // copyProgram
    $routes->get('copyProgram/(:any)', 'ItemRecipeMaster::copyProgram/$1'); // Copy program
});


// importFile

$routes->group('api/importFile', ['filter' => 'jwt', 'namespace' => '\Modules\Backend\ItemRecipeMaster\Controllers'], function ($routes) {
    // getDataTableColumns ,getDataTableData
    $routes->post('save', 'importFile::save'); // Save user
});
