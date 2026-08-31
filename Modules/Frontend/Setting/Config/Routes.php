<?php
$routes->group("setting", ["namespace" => "\Modules\Frontend\Setting\Controllers"], function ($routes) {
    $routes->get("settings", "Setting::settings");
    $routes->get("companySetting", "Setting::companySetting");
    $routes->get("manageCompanyMasterSettings", "Setting::manageCompanyMasterSettings");
    $routes->get("addCompanyMasterSetting", "Setting::addCompanyMasterSetting");
    $routes->get("addCompanyMasterSetting/(:any)", "Setting::addCompanyMasterSetting/$1");
});
