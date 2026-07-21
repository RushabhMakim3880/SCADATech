<?php

$routes->group("MachineMaster", ['filter' => 'cache', "namespace" => '\Modules\Frontend\MachineMaster\Controllers'], function ($routes) {
    $routes->get("editMachine/(:any)", "MachineMaster::editMachine/$1");
    $routes->get("addMachine", "MachineMaster::addMachine");
    $routes->get("manageMachine", "MachineMaster::manageMachine");

    //machineSetup
    $routes->get("machineSetup", "MachineMaster::machineSetup");
});


$routes->group("MachineOperationConfig", ['filter' => 'cache', "namespace" => '\Modules\Frontend\MachineMaster\Controllers'], function ($routes) {
    $routes->get("addMachineOperation/(:any)", "MachineOperationConfig::addMachineOperation/$1");
    $routes->get("addMachineOperation", "MachineOperationConfig::addMachineOperation");
    $routes->get("manageMachineOperation", "MachineOperationConfig::manageMachineOperation");
});
