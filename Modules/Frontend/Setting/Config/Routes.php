<?php
$routes->group("setting", ["namespace" => "\Modules\Frontend\Setting\Controllers"], function ($routes) {
    $routes->get("settings", "Setting::settings");
    $routes->get("companySetting", "Setting::companySetting");
});
