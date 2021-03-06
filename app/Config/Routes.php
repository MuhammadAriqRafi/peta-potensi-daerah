<?php

namespace Config;

use App\Controllers\CategoryController;

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
        $routes->post('/', 'AdministratorController::store', ['as' => 'backend.administrators.store']);
        $routes->get('(:any)/edit', 'AdministratorController::edit/$1', ['as' => 'backend.administrators.edit']);
        $routes->patch('(:any)', 'AdministratorController::update/$1', ['as' => 'backend.administrators.update']);
        $routes->delete('(:any)', 'AdministratorController::destroy/$1', ['as' => 'backend.administrators.delete']);
    });

    // Guestbooks Routes
    $routes->group('guestbooks', function ($routes) {
        $routes->get('(:any)', 'GuestbookController::show/$1', ['as' => 'backend.guestbooks.show']);
        $routes->get('/', 'GuestbookController::index', ['as' => 'backend.guestbooks.index']);
        $routes->delete('(:num)', 'GuestbookController::destroy/$1', ['as' => 'backend.guestbooks.destroy']);
    });

    // Popups Routes
    $routes->group('popups', function ($routes) {
        // Popup Statuses Routes
        $routes->group('status', function ($routes) {
            $routes->patch('/', 'PopupController::update_status', ['as' => 'backend.popups.statuses.update']);
        });

        $routes->get('/', 'PopupController::index', ['as' => 'backend.popups.index']);
        $routes->post('/', 'PopupController::store', ['as' => 'backend.popups.store']);
        $routes->get('(:any)/edit', 'PopupController::edit/$1', ['as' => 'backend.popups.edit']);
        $routes->patch('(:any)', 'PopupController::update/$1', ['as' => 'backend.popups.update']);
        $routes->delete('(:num)', 'PopupController::destroy/$1', ['as' => 'backend.popups.delete']);
    });

    // Settings Routes
    $routes->group('settings', function ($routes) {
        $routes->get('/', 'SettingController::index', ['as' => 'backend.settings.index']);
        $routes->get('(:any)/edit', 'SettingController::edit/$1', ['as' => 'backend.settings.edit']);
        $routes->patch('(:num)', 'SettingController::update/$1', ['as' => 'backend.settings.update']);
    });

    // Menu Manager Routes
    $routes->group('menus', function ($routes) {
        $routes->get('/', 'MenuController::index', ['as' => 'backend.menus.index']);
        $routes->get('(:any)/edit', 'MenuController::edit/$1', ['as' => 'backend.menus.edit']);
        $routes->patch('(:any)', 'MenuController::update/$1', ['as' => 'backend.menus.update']);
    });

    // Posts Routes
    $routes->delete('posts/(:num)/(:any)', 'PostController::destroy/$1/$2', ['as' => 'backend.posts.delete']);

    // Map Settings Routes
    $routes->group('maps', function ($routes) {
        // Categories Routes
        $routes->group('categories', function ($routes) {
            $routes->get('/', 'CategoryController::index', ['as' => 'backend.maps.categories.index']);
            $routes->post('/', 'CategoryController::store', ['as' => 'backend.maps.categories.store']);
            $routes->get('(:any)/edit', 'CategoryController::edit/$1', ['as' => 'backend.maps.categories.edit']);
            $routes->patch('(:any)', 'CategoryController::update/$1', ['as' => 'backend.maps.categories.update']);
            $routes->delete('(:any)', 'CategoryController::destroy/$1', ['as' => 'backend.maps.categories.destroy']);
        });

        // Galleries Routes
        $routes->group('galleries', function ($routes) {
            $routes->get('(:any)', 'GalleryController::index/$1', ['as' => 'backend.maps.galleries.index']);
            $routes->post('(:any)', 'GalleryController::store/$1', ['as' => 'backend.maps.galleries.store']);
        });

        $routes->get('/', 'MapController::index', ['as' => 'backend.maps.index']);
        $routes->post('/', 'MapController::store', ['as' => 'backend.maps.store']);
        $routes->get('create', 'MapController::create', ['as' => 'backend.maps.create']);
        $routes->get('(:any)/edit', 'MapController::edit/$1', ['as' => 'backend.maps.edit']);
        $routes->patch('(:any)', 'MapController::update/$1', ['as' => 'backend.maps.update']);
        $routes->delete('(:any)', 'MapController::destroy/$1', ['as' => 'backend.maps.delete']);
    });

    // Profiles Routes
    $routes->group('profile', function ($routes) {
        $routes->get('/', 'ProfileController::index', ['as' => 'backend.profiles.index']);
        $routes->post('/', 'ProfileController::store', ['as' => 'backend.profiles.store']);
        $routes->get('(:any)/edit', 'ProfileController::edit/$1', ['as' => 'backend.profiles.edit']);
        $routes->patch('(:any)', 'ProfileController::update/$1', ['as' => 'backend.profiles.update']);
        $routes->delete('(:any)', 'ProfileController::destroy/$1', ['as' => 'backend.profiles.destroy']);
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
