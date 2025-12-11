<?php
require_once 'system/bootstrap.php';

use Config\Database;

// Start session
session_start();

echo "Current session data:\n";
echo "User ID: " . ($_SESSION['user_id'] ?? 'not set') . "\n";
echo "Name: " . ($_SESSION['name'] ?? 'not set') . "\n";
echo "Email: " . ($_SESSION['email'] ?? 'not set') . "\n";
echo "Role: " . ($_SESSION['role'] ?? 'not set') . "\n";

$db = Database::connect();

echo "\nChecking courses for current user...\n";
$courses = $db->table('courses')
    ->select('id, title, code, status, instructor_id')
    ->where('instructor_id', (int)($_SESSION['user_id'] ?? 0))
    ->get()
    ->getResultArray();

echo "Found " . count($courses) . " courses:\n";
foreach ($courses as $course) {
    echo "- ID: {$course['id']}, Title: {$course['title']}, Status: {$course['status']}, Instructor: {$course['instructor_id']}\n";
}

echo "\nChecking all courses in database:\n";
$allCourses = $db->table('courses')
    ->select('id, title, code, status, instructor_id')
    ->get()
    ->getResultArray();

echo "Total courses in database: " . count($allCourses) . "\n";
foreach ($allCourses as $course) {
    echo "- ID: {$course['id']}, Title: {$course['title']}, Status: {$course['status']}, Instructor: {$course['instructor_id']}\n";
}
?>
