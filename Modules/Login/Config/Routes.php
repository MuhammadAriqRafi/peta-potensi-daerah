<?php

$routes->group('backend', ['namespace' => 'Modules\Login\Controller'], function ($routes) {
    // Login Routes
    $routes->group('login', ['filter' => 'guest', 'namespace' => 'Modules\Login\Controller'], function ($routes) {
        $routes->get('/', 'LoginController::index', ['as' => 'login.index']);
        $routes->post('/', 'LoginController::authenticate', ['as' => 'login.authenticate']);
    });

    $routes->get('logout', 'LoginController::logout', [
        'as' => 'logout',
        'filter' => 'auth'
    ]);
});
