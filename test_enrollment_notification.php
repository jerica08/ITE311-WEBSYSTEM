<?php

// Test script to verify enrollment notification functionality
define('BASEPATH', __DIR__);
define('APPPATH', __DIR__ . '/app/');

// Load environment
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
        $_ENV[trim($key)] = trim($value);
        $_SERVER[trim($key)] = trim($value);
    }
}

// Initialize CodeIgniter
require_once 'system/autoload.php';
$app = new CodeIgniter\CodeIgniter(APPPATH, 'production');
$app->initialize();

echo "=== Testing Enrollment Notification System ===\n\n";

// 1. Check if there are any pending enrollments
echo "1. Checking for pending enrollments...\n";
$db = \Config\Database::connect();
$pendingEnrollments = $db->table('enrollments')
    ->where('enrollment_status', 'pending')
    ->get()
    ->getResultArray();

echo "Found " . count($pendingEnrollments) . " pending enrollments\n";

if (empty($pendingEnrollments)) {
    echo "No pending enrollments found. Creating test data...\n";
    
    // Get a test student and course
    $testStudent = $db->table('users')->where('role', 'student')->limit(1)->get()->getRowArray();
    $testCourse = $db->table('courses')->limit(1)->get()->getRowArray();
    
    if ($testStudent && $testCourse) {
        $enrollmentData = [
            'user_id' => $testStudent['id'],
            'course_id' => $testCourse['id'],
            'enrollment_status' => 'pending',
            'enrollment_date' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        
        $db->table('enrollments')->insert($enrollmentData);
        $enrollmentId = $db->insertID();
        echo "Created test enrollment ID: $enrollmentId\n";
        
        $pendingEnrollments = $db->table('enrollments')
            ->where('enrollment_status', 'pending')
            ->get()
            ->getResultArray();
    }
}

// 2. Test the approval process
if (!empty($pendingEnrollments)) {
    $testEnrollment = $pendingEnrollments[0];
    echo "\n2. Testing approval for enrollment ID: " . $testEnrollment['id'] . "\n";
    
    // Simulate the approval process
    $enrollmentModel = new \App\Models\EnrollmentModel();
    $notificationModel = new \App\Models\NotificationModel();
    
    // Update enrollment status
    $enrollmentModel->update($testEnrollment['id'], ['enrollment_status' => 'approved']);
    echo "Updated enrollment status to 'approved'\n";
    
    // Get student and course details
    $studentDetails = $db->table('users')
        ->select('name')
        ->where('id', $testEnrollment['user_id'])
        ->get()
        ->getRowArray();
    
    $courseDetails = $db->table('courses')
        ->select('title')
        ->where('id', $testEnrollment['course_id'])
        ->get()
        ->getRowArray();
    
    echo "Student: " . ($studentDetails['name'] ?? 'Not found') . "\n";
    echo "Course: " . ($courseDetails['title'] ?? 'Not found') . "\n";
    
    if ($studentDetails && $courseDetails) {
        // Create notification
        $notificationData = [
            'user_id'    => (int) $testEnrollment['user_id'],
            'title'       => 'Enrollment Approved',
            'message'     => 'Your enrollment in ' . $courseDetails['title'] . ' has been approved!',
            'type'        => 'enrollment_approved',
            'related_id'  => (int) $testEnrollment['id'],
            'is_read'     => 0,
            'created_at'  => date('Y-m-d H:i:s'),
        ];
        
        $result = $notificationModel->insert($notificationData);
        echo "Notification created with ID: $result\n";
    }
}

// 3. Check notifications for the student
echo "\n3. Checking notifications for student...\n";
if (!empty($pendingEnrollments)) {
    $studentId = $pendingEnrollments[0]['user_id'];
    
    $notifications = $notificationModel->getNotificationsForUser($studentId);
    $unreadCount = $notificationModel->getUnreadCount($studentId);
    
    echo "Student ID: $studentId\n";
    echo "Unread count: $unreadCount\n";
    echo "Recent notifications:\n";
    
    foreach ($notifications as $notif) {
        echo "- {$notif['title']}: {$notif['message']}\n";
        echo "  Created: {$notif['created_at']}\n";
        echo "  Type: {$notif['type']}\n\n";
    }
}

// 4. Check database tables
echo "\n4. Database table checks...\n";
echo "Enrollments table exists: " . ($db->tableExists('enrollments') ? 'Yes' : 'No') . "\n";
echo "Notifications table exists: " . ($db->tableExists('notifications') ? 'Yes' : 'No') . "\n";

// Check notification table structure
if ($db->tableExists('notifications')) {
    $columns = $db->getTableInfo('notifications');
    echo "Notifications table columns:\n";
    foreach ($columns as $column) {
        echo "- {$column['name']} ({$column['type']})\n";
    }
}

echo "\n=== Test Complete ===\n";
