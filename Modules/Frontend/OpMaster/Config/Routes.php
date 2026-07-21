<?php

$routes->group("OpMaster", ["namespace" => '\Modules\Frontend\OpMaster\Controllers'], function ($routes) {

    // home
    $routes->get("start", "OpMaster::start");

    $routes->get("home", "OpMaster::home");
    // masterSettings
    $routes->get("machineParameters", "OpMaster::machineParameters");
    // homing
    $routes->get("homing", "OpMaster::homing");
    // manualControl
    $routes->get("manualControl", "OpMaster::manualControl");
    // autoControl
    $routes->get("autoControl", "OpMaster::autoControl");
    // log
    $routes->get("log", "OpMaster::log");

    // programPrepare
    $routes->get("programPrepare", "OpMaster::programPrepare");
});
