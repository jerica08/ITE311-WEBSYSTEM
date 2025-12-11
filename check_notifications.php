<?php
$db = new mysqli('localhost', 'root', '', 'ite311_lms');
if ($db->connect_error) { 
    die('Connection failed: ' . $db->connect_error); 
}

echo "Checking notifications for student users...\n";

// Check for student users
$result = $db->query('SELECT id, name FROM users WHERE role = "student"');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "Student: ID {$row['id']}, Name: {$row['name']}\n";
        
        // Check notifications for this student
        $notifResult = $db->query("SELECT * FROM notifications WHERE user_id = {$row['id']} ORDER BY created_at DESC LIMIT 3");
        if ($notifResult && $notifResult->num_rows > 0) {
            while ($notif = $notifResult->fetch_assoc()) {
                echo "  - Notification: {$notif['title']} - {$notif['message']} (Read: {$notif['is_read']})\n";
            }
        } else {
            echo "  - No notifications found\n";
        }
    }
}

$db->close();
?>
