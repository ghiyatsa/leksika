<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ── Public Routes ─────────────────────────────────────────────────────────────
$routes->get('/', 'Home::index');
$routes->get('login', 'AuthController::login');
$routes->get('register', 'AuthController::register');
$routes->get('forgot-password', 'AuthController::forgotPassword');
$routes->post('auth/forgot-password', 'AuthController::forgotPassword');
$routes->post('auth/firebaseLogin', 'AuthController::firebaseLogin');
$routes->post('logout', 'AuthController::logout');

// ── Protected Routes (require login) ──────────────────────────────────────────
$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes): void {

    // Profile Management
    $routes->get('profile', 'ProfileController::index');
    $routes->post('profile/update', 'ProfileController::update');
    $routes->post('profile/change-password', 'ProfileController::changePassword');
    $routes->post('profile/avatar/delete', 'ProfileController::deleteAvatar');
    $routes->post('profile/delete', 'ProfileController::deleteAccount');

    // Similarity Check (Accessible by both Admin and Student roles, uses layout based on role)
    $routes->get('similarity', 'SimilarityController::index');
    $routes->post('similarity/check', 'SimilarityController::check');
    $routes->get('similarity/history', 'SimilarityController::history');
    $routes->get('similarity/(:segment)', 'SimilarityController::result/$1');

    // ── Admin-only Routes ──────────────────────────────────────────────────
    $routes->group('admin', ['filter' => 'admin'], static function (RouteCollection $routes): void {

        // Dashboard
        $routes->get('dashboard', 'DashboardController::index');

        // Thesis Titles CRUD
        $routes->get('thesis', 'Admin\ThesisTitleController::index');
        $routes->get('thesis/create', 'Admin\ThesisTitleController::create');
        $routes->post('thesis/store', 'Admin\ThesisTitleController::store');
        $routes->get('thesis/(:num)/edit', 'Admin\ThesisTitleController::edit/$1');
        $routes->post('thesis/(:num)/update', 'Admin\ThesisTitleController::update/$1');
        $routes->post('thesis/(:num)/delete', 'Admin\ThesisTitleController::delete/$1');
        // Students CRUD
        $routes->get('students', 'Admin\StudentController::index');
        $routes->get('students/create', 'Admin\StudentController::create');
        $routes->post('students/store', 'Admin\StudentController::store');
        $routes->get('students/(:num)/edit', 'Admin\StudentController::edit/$1');
        $routes->post('students/(:num)/update', 'Admin\StudentController::update/$1');
        $routes->post('students/(:num)/delete', 'Admin\StudentController::delete/$1');

        // Categories CRUD
        $routes->get('categories', 'Admin\CategoryController::index');
        $routes->get('categories/create', 'Admin\CategoryController::create');
        $routes->post('categories/store', 'Admin\CategoryController::store');
        $routes->get('categories/(:num)/edit', 'Admin\CategoryController::edit/$1');
        $routes->post('categories/(:num)/update', 'Admin\CategoryController::update/$1');
        $routes->post('categories/(:num)/delete', 'Admin\CategoryController::delete/$1');

        // Threshold Settings
        $routes->get('threshold', 'Admin\ThresholdController::index');
        $routes->post('threshold/update', 'Admin\ThresholdController::update');

        // User Management CRUD
        $routes->get('users', 'Admin\UserManagementController::index');
        $routes->get('users/create', 'Admin\UserManagementController::create');
        $routes->post('users/store', 'Admin\UserManagementController::store');
        $routes->get('users/(:num)/edit', 'Admin\UserManagementController::edit/$1');
        $routes->post('users/(:num)/update', 'Admin\UserManagementController::update/$1');
        $routes->post('users/(:num)/delete', 'Admin\UserManagementController::delete/$1');
    });
});
