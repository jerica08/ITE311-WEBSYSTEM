<?php
$db = new mysqli('localhost', 'root', '', 'lms_marquez');
if ($db->connect_error) {
    die('Connection failed: ' . $db->connect_error);
}

echo "Checking student enrollments...\n";

// Check for student users
$students = $db->query('SELECT id, name FROM users WHERE role = "student" LIMIT 5');
if ($students) {
    while ($student = $students->fetch_assoc()) {
        echo "\nStudent: ID {$student['id']}, Name: {$student['name']}\n";
        
        // Check enrollments for this student
        $enrollments = $db->query("SELECT e.*, c.title as course_title, c.code as course_code, c.class_schedule 
                                   FROM enrollments e 
                                   JOIN courses c ON c.id = e.course_id 
                                   WHERE e.user_id = {$student['id']} 
                                   ORDER BY e.created_at DESC");
        
        if ($enrollments && $enrollments->num_rows > 0) {
            while ($enrollment = $enrollments->fetch_assoc()) {
                echo "  - Course: {$enrollment['course_title']} ({$enrollment['course_code']})\n";
                echo "    Status: {$enrollment['enrollment_status']}\n";
                echo "    Schedule: " . ($enrollment['class_schedule'] ?? 'None') . "\n";
                echo "    Enrolled: {$enrollment['created_at']}\n\n";
            }
        } else {
            echo "  - No enrollments found\n";
        }
    }
} else {
    echo "No students found\n";
}

$db->close();
?>
