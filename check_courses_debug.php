<?php
// Simple database check using CodeIgniter's database config
$databasePath = __DIR__ . '/app/Config/Database.php';
if (file_exists($databasePath)) {
    require_once $databasePath;
    $config = new Config\Database();
    $db = \Config\Database::connect();
} else {
    die("Database config not found\n");
}

echo "=== Checking courses table ===\n";
if ($db->tableExists('courses')) {
    $courses = $db->table('courses')->get()->getResultArray();
    echo 'Total courses: ' . count($courses) . "\n";
    
    if (!empty($courses)) {
        foreach ($courses as $course) {
            echo 'ID: ' . $course['id'] . ', Title: ' . $course['title'] . ', Instructor ID: ' . $course['instructor_id'] . "\n";
        }
    } else {
        echo "No courses found in database.\n";
    }
} else {
    echo 'Courses table does not exist\n';
}

echo "\n=== Checking users table for teachers ===\n";
if ($db->tableExists('users')) {
    $teachers = $db->table('users')->where('role', 'teacher')->get()->getResultArray();
    echo 'Total teachers: ' . count($teachers) . "\n";
    
    if (!empty($teachers)) {
        foreach ($teachers as $teacher) {
            echo 'ID: ' . $teacher['id'] . ', Name: ' . $teacher['name'] . ', Email: ' . $teacher['email'] . "\n";
        }
    } else {
        echo "No teachers found in database.\n";
    }
} else {
    echo 'Users table does not exist\n';
}
?>
