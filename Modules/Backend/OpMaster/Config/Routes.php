<?php

$routes->group('api/OpMasterBack', ['namespace' => '\Modules\Backend\OpMaster\Controllers'], function ($routes) {
    $routes->post('submitData', 'OpMasterBack::submitData');
    $routes->get('getConfig', 'OpMasterBack::getConfig');
    // submitAlarmData
    $routes->post('submitAlarmData', 'OpMasterBack::submitAlarmData');
});


$routes->group('api/OpMasterFront', ['namespace' => '\Modules\Backend\OpMaster\Controllers'], function ($routes) {
    $routes->get('initPlc/(:any)', 'OpMasterFront::initPlc/$1');
    // syncTags
    $routes->get('syncTags/(:any)', 'OpMasterFront::syncTags/$1');

    // writeTags
    $routes->post('writeTags', 'OpMasterFront::writeTags');

    // manageNodeApp
    $routes->post('manageNodeApp', 'OpMasterFront::manageNodeApp');

    // pushTagToCi4
    $routes->post('pushTagToCi4', 'OpMasterFront::pushTagToCi4');

    // get activeAlarms
    $routes->get('activeAlarms', 'OpMasterFront::activeAlarms');

    // get allTagDetails
    $routes->get('allTagDetails', 'OpMasterFront::allTagDetails');

    // get systemInfo
    $routes->get('systemInfo', 'OpMasterFront::systemInfo');
});
