<?php

use CodeIgniter\Router\RouteCollection;
use App\Libraries\Auth;

/**
 * @var RouteCollection $routes
 */

// defined default home route in .env file, if not defined it will use Home::index
$user = Auth::user();
if ($user and is_null($user->tenantId)) {
    $homeRoute = env('app.saasHomeRoute', 'Home::index');
} else {
    $homeRoute = env('app.homeRoute', 'Home::index');
}

if (str_contains($homeRoute, '::')) {



    [$controller, $method] = explode('::', $homeRoute);

    // Auto-prefix App\Controllers only if not namespaced
    if (!str_starts_with($controller, '\\') && !str_contains($controller, '\\')) {
        $controller = 'App\\Controllers\\' . $controller;
    }

    $routes->get('/', [$controller, $method]);
} else {
    $routes->get('/', 'Home::index');
}

// dashboardDesigner
$routes->get('home/dashboardDesigner', 'Home::dashboardDesigner');
// viewDashboard
$routes->get('home/viewDashboard/(:any)', 'Home::viewDashboard/$1');

$routes->group('assets', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('combined.css', 'Assets::css');
    $routes->get('combined.js', 'Assets::js');
});

$routes->get('manifest.json', 'Assets::manifest');

// auth login route
$routes->group('auth', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('login', 'Auth::login');         // List users
    $routes->get('logout', 'Auth::logout');  // Create user
    // Add more routes like update, delete as needed
});


$routes->group('home', ['namespace' => 'App\Controllers'], function ($routes) {
    // sendTestEmail
    $routes->get('sendTestEmail', 'Home::sendTestEmail');

    $routes->get('sendNotification', 'Home::sendNotification');
    // saveUserSubscription
    $routes->post('saveUserSubscription', 'Home::saveUserSubscription');

    //sseStream
    $routes->get('sseStream', 'Home::sseStream');
});


$routes->group('tools', ['namespace' => 'App\Controllers'], function ($routes) {
    // $routes->get('captcha', 'Tools::captcha');

    // any route that is not defined will be handled by the Tools controller
    $routes->match(['GET', 'POST'], '(:any)', 'Tools::$1');
    $routes->cli('(:any)', 'Tools::$1');
});


$routes->group('projecttools', ['namespace' => 'App\Controllers'], function ($routes) {
    // $routes->get('captcha', 'Tools::captcha');

    // any route that is not defined will be handled by the Tools controller
    $routes->match(['GET', 'POST'], '(:any)', 'ProjectTools::$1');
    $routes->cli('(:any)', 'ProjectTools::$1');
});


/**************************************/
/* TEMP LINK RECEIVER ROUTES
/**************************************/
$routes->get('r/(:segment)', 'Tools::redirect/$1', ['filter' => 'tempLink']);

//sample route for temp link example
// $routes->get('tempLink', 'Tools::generateFeedbackLink');
// $routes->get('tools/tempController/(:any)', 'Tools::tempController/$1');
/**************************************/
