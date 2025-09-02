<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('home', 'Home::index');
$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');
$routes->setAutoRoute(true);

//Authentication Routes
$routes->get('/auth/login', 'Auth::login');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/auth/register', 'Auth::register');
$routes->post('/auth/register', 'Auth::register');
$routes->get('/auth/logout', 'Auth::logout');
$routes->get('/dashboard', 'Auth::dashboard');

//Dashboard Routes by Role
$routes->get('/student/dashboard', 'Auth::studentDashboard');
$routes->get('/instructor/dashboard', 'Auth::instructorDashboard');
$routes->get('/admin/dashboard', 'Auth::adminDashboard');