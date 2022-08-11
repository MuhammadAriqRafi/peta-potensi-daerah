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
$routes->setDefaultController('PageController');
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
$routes->post('/', 'GuestbookController::store', ['as' => 'guestbook.store']);

foreach (glob(ROOTPATH . 'Modules/*', GLOB_ONLYDIR) as $module) {
    if (file_exists($module . '\Config\Routes.php')) {
        require_once($module . '\Config\Routes.php');
    }
}

// Backend Routes
$routes->group('backend', ['filter' => 'auth'], function ($routes) {

    // Dashboard Routes
    $routes->get('/', 'PageController::backend_dashboard', ['as' => 'backend.dashboard.index']);

    // Administrators Routes
    $routes->group('administrators', function ($routes) {
        $routes->get('/', 'AdministratorController::index', ['as' => 'backend.administrators.index']);
        $routes->get('(:any)', 'AdministratorController::$1');
        $routes->post('(:any)', 'AdministratorController::$1');
        $routes->patch('(:any)', 'AdministratorController::$1');
        $routes->delete('(:any)', 'AdministratorController::$1');
    });

    // Guestbooks Routes
    $routes->group('guestbooks', function ($routes) {
        $routes->get('/', 'GuestbookController::index', ['as' => 'backend.guestbooks.index']);
        $routes->get('(:any)', 'GuestbookController::$1');
        $routes->delete('(:any)', 'GuestbookController::$1');
        // $routes->get('(:any)', 'GuestbookController::show/$1', ['as' => 'backend.guestbooks.show']);
        // $routes->delete('(:num)', 'GuestbookController::destroy/$1', ['as' => 'backend.guestbooks.destroy']);
    });

    // Popups Routes
    $routes->group('popups', function ($routes) {
        $routes->get('/', 'PopupController::index', ['as' => 'backend.popups.index']);
        $routes->get('(:any)', 'PopupController::$1');
        $routes->post('(:any)', 'PopupController::$1');
        $routes->patch('(:any)', 'PopupController::$1');
        $routes->delete('(:any)', 'PopupController::$1');
    });

    // Settings Routes
    $routes->group('settings', function ($routes) {
        $routes->get('/', 'SettingController::index', ['as' => 'backend.settings.index']);
        $routes->patch('(:any)', 'SettingController::$1');
    });

    // Menu Manager Routes
    $routes->group('menus', function ($routes) {
        $routes->get('/', 'MenuController::index', ['as' => 'backend.menus.index']);
        $routes->get('(:any)', 'MenuController::$1');
        $routes->post('(:any)', 'MenuController::$1');
        $routes->patch('(:any)', 'MenuController::$1');
        $routes->delete('(:any)', 'MenuController::$1');
    });

    // Posts Routes
    $routes->delete('posts/(:any)/(:any)', 'PostController::destroy/$1/$2', ['as' => 'backend.posts.delete']);

    // Map Settings Routes
    $routes->group('maps', function ($routes) {
        // Categories Routes
        $routes->group('categories', function ($routes) {
            $routes->get('/', 'CategoryController::index', ['as' => 'backend.maps.categories.index']);
            $routes->get('(:any)', 'CategoryController::$1');
            $routes->post('(:any)', 'CategoryController::$1');
            $routes->patch('(:any)', 'CategoryController::$1');
            $routes->delete('(:any)', 'CategoryController::$1');
        });

        // Galleries Routes
        $routes->group('galleries', function ($routes) {
            $routes->get('(:any)', 'GalleryController::index/$1', ['as' => 'backend.maps.galleries.index']);
            $routes->post('(:any)', 'GalleryController::store/$1', ['as' => 'backend.maps.galleries.store']);
        });

        $routes->get('/', 'MapController::index', ['as' => 'backend.maps.index']);
        $routes->get('(:any)', 'MapController::$1');
        $routes->post('(:any)', 'MapController::$1');
        $routes->patch('(:any)', 'MapController::$1');
        $routes->delete('(:any)', 'MapController::$1');
    });

    // Profiles Routes
    $routes->group('profiles', function ($routes) {
        $routes->get('/', 'ProfileController::index', ['as' => 'backend.profiles.index']);
        $routes->get('(:any)', 'ProfileController::$1');
        $routes->post('(:any)', 'ProfileController::$1');
        $routes->patch('(:any)', 'ProfileController::$1');
        $routes->delete('(:any)', 'ProfileController::$1');
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
