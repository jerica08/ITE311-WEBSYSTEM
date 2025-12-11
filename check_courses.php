<?php
// Initialize CodeIgniter
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';

define('BASEPATH', __DIR__ . '/system');
define('APPPATH', __DIR__ . '/app/');

// Load CodeIgniter
require_once 'system/codeigniter/CodeIgniter.php';
$CI = new CodeIgniter();

// Connect to database
$db = \Config\Database::connect();

echo 'Checking courses table structure...' . PHP_EOL;
$result = $db->query('DESCRIBE courses');
foreach($result->getResultArray() as $row) {
    echo $row['Field'] . ' - ' . $row['Type'] . PHP_EOL;
}

echo PHP_EOL . 'Checking if instructor_id exists...' . PHP_EOL;
$result = $db->query('SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = "courses" AND COLUMN_NAME = "instructor_id" AND TABLE_SCHEMA = DATABASE()');
if($result->getNumRows() > 0) {
    echo 'instructor_id field exists' . PHP_EOL;
} else {
    echo 'instructor_id field MISSING' . PHP_EOL;
}

echo PHP_EOL . 'Checking sample courses...' . PHP_EOL;
$result = $db->query('SELECT id, title, instructor_id FROM courses LIMIT 5');
foreach($result->getResultArray() as $row) {
    echo 'ID: ' . $row['id'] . ', Title: ' . $row['title'] . ', Instructor: ' . $row['instructor_id'] . PHP_EOL;
}

echo PHP_EOL . 'Checking teachers in users table...' . PHP_EOL;
$result = $db->query('SELECT id, name, email, role FROM users WHERE role IN ("teacher", "instructor") LIMIT 5');
foreach($result->getResultArray() as $row) {
    echo 'ID: ' . $row['id'] . ', Name: ' . $row['name'] . ', Role: ' . $row['role'] . PHP_EOL;
}
?>
