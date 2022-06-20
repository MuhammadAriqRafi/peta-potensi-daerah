<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
//$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// Static Page Routes
$routes->get('/', 'PageController::index', ['as' => 'home']);
$routes->get('/tentang', 'PageController::tentang');
$routes->get('/pariwisata/(:alpha)', 'PariwisataController::index/$1');

// Backend Routes
$routes->group('backend', function ($routes) {

    // Popup Routes
    $routes->group('popups', function ($routes) {
        $routes->get('/', 'PopupController::index', ['as' => 'backend.popups.index']);
        $routes->post('/', 'PopupController::store', ['as' => 'backend.popups.store']);
        $routes->get('(:any)/edit', 'PopupController::edit/$1', ['as' => 'backend.popups.edit']);
        $routes->patch('(:num)', 'PopupController::update/$1', ['as' => 'backend.popups.update']);
        $routes->delete('(:num)', 'PopupController::destroy/$1', ['as' => 'backend.popups.delete']);
    });

    // Settings Routes
    $routes->group('settings', function ($routes) {
        $routes->get('/', 'SettingController::index', ['as' => 'backend.settings.index']);
        $routes->post('/', 'SettingController::store', ['as' => 'backend.settings.store']);
        $routes->patch('/', 'SettingController::update');
        $routes->delete('/(:any)', 'SettingController::destroy/$1', ['as' => 'backend.settings.destroy']);
    });

    // Menu Manager Routes
    $routes->group('menus', function ($routes) {
        $routes->get('/', 'MenuController::index', ['as' => 'backend.menus.index']);
        $routes->post('/', 'MenuController::store');
        $routes->patch('/', 'MenuController::update');
        $routes->delete('/', 'MenuController::destroy');
    });
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
