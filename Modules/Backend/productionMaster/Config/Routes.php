<?php

$routes->group('api/productionMaster', ['namespace' => '\Modules\Backend\productionMaster\Controllers'], function ($routes) {
    $routes->get('pendingJobcards', 'productionMaster::pendingJobcards');

    // programAlign
    $routes->post('programAlign', 'productionMaster::programAlign');

    // get loadSettings
    $routes->get('loadSettings', 'productionMaster::loadSettings');

    //storeProgramState
    $routes->post('storeProgramState/(:any)', 'productionMaster::storeProgramState/$1');

    // recordItemCompletion
    $routes->post('recordItemCompletion', 'productionMaster::recordItemCompletion');

    // recordPunchCount
    $routes->post('recordPunchCount', 'productionMaster::recordPunchCount');

    // logCompletedStateRecord
    $routes->post('logCompletedStateRecord', 'productionMaster::logCompletedStateRecord');
});
