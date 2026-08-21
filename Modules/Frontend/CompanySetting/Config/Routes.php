<?php
$routes->group("CompanySetting", ["namespace" => "\Modules\Frontend\CompanySetting\Controllers"], function ($routes) {
    $routes->get("manageCompanyMasterSettings", "CompanySetting::manageCompanyMasterSettings");
    $routes->get("manageCompanySetting", "CompanySetting::manageCompanySetting");
});
