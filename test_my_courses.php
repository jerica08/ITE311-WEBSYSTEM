<?php
// Test script to check myCourses functionality
session_start();

// Simulate teacher session
$_SESSION['isLoggedIn'] = true;
$_SESSION['user_id'] = 2; // Teacher ID from logs
$_SESSION['name'] = 'Karl Instructor';
$_SESSION['email'] = 'teacher@example.com';
$_SESSION['role'] = 'teacher';

// Load CodeIgniter
require_once 'system/bootstrap.php';

// Create controller instance and test
$controller = new \App\Controllers\TeacherController();

// Mock the request
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';

// Call the method
$result = $controller->myCourses();

echo "Test completed. Check logs for debug output.\n";
?>
