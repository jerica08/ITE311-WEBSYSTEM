<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('home', 'Home::index');
$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

// Authentication Routes
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Auth::dashboard');

// Auth group routes (alternative approach)
$routes->group('auth', function($routes) {
    $routes->get('register', 'Auth::register');
    $routes->post('register', 'Auth::register');
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::login');
    $routes->get('logout', 'Auth::logout');
    $routes->get('dashboard', 'Auth::dashboard');
});

// Role-based dashboards (new controllers)
$routes->get('admin/dashboard', 'AdminController::dashboard');
$routes->get('admin/users', 'AdminController::users');
$routes->get('admin/courses', 'AdminController::courses');
$routes->get('teacher/dashboard', 'TeacherController::dashboard');
$routes->get('student/dashboard', 'StudentController::dashboard');

// Course actions
$routes->post('course/enroll', 'Course::enroll');

// Admin course management
$routes->post('admin/courses/create', 'AdminController::createCourse');

// Teacher course management
$routes->post('teacher/courses/create', 'TeacherController::createCourse');


// Materials management
$routes->get('materials/upload/(:num)', 'Materials::upload/$1');
$routes->post('materials/upload/(:num)', 'Materials::upload/$1');
$routes->post('materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('materials/download/(:num)', 'Materials::download/$1');
