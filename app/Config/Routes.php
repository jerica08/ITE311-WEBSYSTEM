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
$routes->get('/profile', 'Profile::index');

// (No /auth/... routes; primary URLs are non-prefixed)

// Course actions
$routes->post('course/enroll', 'Course::enroll');

// Teacher dashboard and enrollment approval
$routes->get('teacher', 'TeacherController::dashboard');
$routes->get('teacher/dashboard', 'TeacherController::dashboard');
$routes->get('teacher/approve-enrollment/(:num)', 'TeacherController::approveEnrollment/$1');
$routes->get('teacher/reject-enrollment/(:num)', 'TeacherController::rejectEnrollment/$1');
$routes->get('teacher/get-programs-by-department', 'TeacherController::getProgramsByDepartment');

// Teacher notifications
$routes->get('teacher/notifications', 'TeacherController::getNotifications');
$routes->post('teacher/notifications/mark-read', 'TeacherController::markNotificationRead');
$routes->post('teacher/notifications/mark-all-read', 'TeacherController::markAllNotificationsRead');

// Teacher assignments
$routes->get('teacher/assignments', 'TeacherController::assignments');
$routes->post('teacher/createAssignment', 'TeacherController::createAssignment');
$routes->get('teacher/assignments/view/(:num)', 'TeacherController::viewSubmissions/$1');
$routes->get('teacher/assignments/grade/(:num)', 'TeacherController::gradeAssignment/$1');
$routes->post('teacher/assignments/saveGrade', 'TeacherController::saveGrade');
$routes->post('teacher/saveGrade', 'TeacherController::saveGrade');
$routes->get('teacher/assignments/edit/(:num)', 'TeacherController::editAssignment/$1');
$routes->post('teacher/assignments/update/(:num)', 'TeacherController::updateAssignment/$1');
$routes->post('teacher/assignments/delete/(:num)', 'TeacherController::deleteAssignment/$1');
$routes->get('teacher/assignments/download/(:num)', 'TeacherController::downloadAssignmentAttachment/$1');
$routes->get('teacher/submissions/download/(:num)', 'TeacherController::downloadSubmissionAttachment/$1');

// Admin dashboard & management
$routes->get('admin', 'AdminController::dashboard');
$routes->get('admin/dashboard', 'AdminController::dashboard');
$routes->get('admin/users', 'AdminController::users');
$routes->get('admin/courses', 'AdminController::courses');
$routes->post('admin/courses/create', 'AdminController::createCourse');

// Admin course CRUD and approval
$routes->get('admin/courses/(:num)', 'AdminController::showCourse/$1');
$routes->get('admin/courses/(:num)/edit', 'AdminController::editCourse/$1');
$routes->post('admin/courses/(:num)/update', 'AdminController::updateCourse/$1');
$routes->post('admin/courses/(:num)/delete', 'AdminController::deleteCourse/$1');
$routes->get('admin/courses/(:num)/approve', 'AdminController::approveCourse/$1');
$routes->get('admin/courses/(:num)/reject', 'AdminController::rejectCourse/$1');

// Admin department management
$routes->post('admin/departments/create', 'AdminController::createDepartment');
$routes->get('admin/departments/(:num)', 'AdminController::showDepartment/$1');
$routes->get('admin/departments/(:num)/edit', 'AdminController::editDepartment/$1');
$routes->post('admin/departments/(:num)/update', 'AdminController::updateDepartment/$1');
$routes->post('admin/departments/(:num)/delete', 'AdminController::deleteDepartment/$1');

// Admin programs management
$routes->post('admin/programs/create', 'AdminController::createProgram');
$routes->get('admin/programs/(:num)', 'AdminController::showProgram/$1');
$routes->get('admin/programs/(:num)/edit', 'AdminController::editProgram/$1');
$routes->post('admin/programs/(:num)/update', 'AdminController::updateProgram/$1');
$routes->get('admin/programs/by-department', 'AdminController::getProgramsByDepartment');

// Admin recent activities API
$routes->get('admin/recent-activities', 'AdminController::getRecentActivities');

// Admin enrollment approval
$routes->get('admin/pending-enrollments', 'AdminController::pendingEnrollments');
$routes->get('admin/approve-enrollment/(:num)', 'AdminController::approveEnrollment/$1');
$routes->get('admin/reject-enrollment/(:num)', 'AdminController::rejectEnrollment/$1');

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

// Student routes
$routes->get('student', 'StudentController::dashboard');
$routes->get('student/dashboard', 'StudentController::dashboard');
$routes->get('student/my-classes', 'StudentController::myClasses');
$routes->get('student/assignments', 'StudentController::assignments');
$routes->get('student/course/(:num)', 'StudentController::course');
$routes->get('student/course/(:num)/assignments', 'StudentController::courseAssignments/$1');
$routes->post('student/course/(:num)/assignments/submit', 'StudentController::submitAssignment');
$routes->get('student/course/(:num)/answer/(:num)', 'StudentController::answerAssignment/$1/$2');
$routes->post('student/submitAssignment', 'StudentController::submitAssignment');
