<?php

$routes->group('apidocs', ['filter' => 'cache', 'namespace' => '\Modules\Apidocs\Controllers'], function ($routes) {
    $routes->get('spec', 'Docs::spec');   // Returns dynamic OpenAPI JSON
    $routes->get('/', 'Docs::index');  // Serves Swagger UI
});
