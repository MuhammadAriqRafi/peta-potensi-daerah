<?php

$routes->group('backend', ['namespace' => 'Modules\Login\Controller', 'filter' => 'guest'], function ($routes) {
    // Login Routes
    $routes->group('login', function ($routes) {
        $routes->get('/', 'LoginController::index', ['as' => 'login.index']);
        $routes->post('/', 'LoginController::authenticate', ['as' => 'login.authenticate']);
    });

    $routes->get('logout', 'LoginController::logout', [
        'as' => 'logout',
        'filter' => 'auth'
    ]);
});
