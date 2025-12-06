<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('home', 'Home::index');
$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

// Authentication Routes (primary URLs WITHOUT /auth prefix)
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Auth::dashboard');

// (No /auth/... routes; primary URLs are non-prefixed)

// Course actions
$routes->post('course/enroll', 'Course::enroll');

// Admin dashboard & management
$routes->get('admin', 'AdminController::dashboard');
$routes->get('admin/dashboard', 'AdminController::dashboard');
$routes->get('admin/users', 'AdminController::users');
$routes->get('admin/courses', 'AdminController::courses');
$routes->post('admin/courses/create', 'AdminController::createCourse');

// Admin course CRUD
$routes->get('admin/courses/(:num)', 'AdminController::showCourse/$1');
$routes->get('admin/courses/(:num)/edit', 'AdminController::editCourse/$1');
$routes->post('admin/courses/(:num)/update', 'AdminController::updateCourse/$1');
$routes->post('admin/courses/(:num)/delete', 'AdminController::deleteCourse/$1');

// Admin view students enrolled in a course
$routes->get('admin/courses/(:num)/students', 'AdminController::courseStudents/$1');

// Admin user management actions
$routes->get('admin/users/edit/(:num)', 'AdminController::editUser/$1');
$routes->post('admin/users/update/(:num)', 'AdminController::updateUser/$1');
$routes->post('admin/users/delete/(:num)', 'AdminController::deleteUser/$1');

// Admin create user
$routes->get('admin/users/create', 'AdminController::createUserForm');
$routes->post('admin/users/store', 'AdminController::storeUser');

// Teacher course management
$routes->get('teacher/courses', 'TeacherController::myCourses');
$routes->post('teacher/courses/create', 'TeacherController::createCourse');
$routes->get('teacher/courses/(:num)', 'TeacherController::showCourse/$1');
$routes->get('teacher/courses/(:num)/edit', 'TeacherController::editCourse/$1');
$routes->post('teacher/courses/(:num)/delete', 'TeacherController::deleteCourse/$1');
$routes->get('teacher/courses/(:num)/students', 'TeacherController::courseStudents/$1');

// Materials management
$routes->get('materials/upload/(:num)', 'Materials::upload/$1');
$routes->post('materials/upload/(:num)', 'Materials::upload/$1');
$routes->post('materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('materials/download/(:num)', 'Materials::download/$1');

// Admin routes for material uploads
$routes->get('admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('admin/course/(:num)/upload', 'Materials::upload/$1');

// Optional GET route for delete (use cautiously; POST is preferred)
$routes->get('materials/delete/(:num)', 'Materials::delete/$1');

// Notifications API
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');

//Search Route
$routes->get('/course', 'Course::search');
$routes->match(['get', 'post'], '/courses/search', 'Course::search');
