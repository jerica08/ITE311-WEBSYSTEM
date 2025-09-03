<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('home', 'Home::index');
$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

//Athentication Routes
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
$routes->get('auth/register', 'Auth::register');
$routes->post('auth/register', 'Auth::register');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');

//Dashboard Routes
$routes->get('/dashboard', 'Auth::dashboard');
$routes->get('/dashboard/student', 'Auth::dashboardStudent');
$routes->get('/dashboard/instructor', 'Auth::dashboardInstructor');
$routes->get('/dashboard/admin', 'Auth::dashboardAdmin');
$routes->setAutoRoute(true);



