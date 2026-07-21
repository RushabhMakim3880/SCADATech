<?php

$routes->group("punchCounters", ['filter' => 'cache', "namespace" => '\Modules\Frontend\punchCounters\Controllers'], function ($routes) {
    $routes->get("managePunchcounters", "punchCounters::managePunchcounters");
});
