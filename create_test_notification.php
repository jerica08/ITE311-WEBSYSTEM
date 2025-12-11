<?php
$db = new mysqli('localhost', 'root', '', 'lms_marquez');
if ($db->connect_error) {
    die('Connection failed: ' . $db->connect_error);
}

echo "Creating test notification for student (ID: 3)...\n";

// Insert a test notification
$sql = "INSERT INTO notifications (user_id, message, type, created_at, is_read) VALUES (?, ?, ?, NOW(), 0)";
$stmt = $db->prepare($sql);
$message = "This is a test notification to verify the dropdown works properly.";
$type = "general";

if ($stmt->execute([3, $message, $type])) {
    echo "Test notification created successfully!\n";
    
    // Check total notifications for this user
    $result = $db->query("SELECT COUNT(*) as count FROM notifications WHERE user_id = 3 AND is_read = 0");
    $row = $result->fetch_assoc();
    echo "Total unread notifications for user 3: " . $row['count'] . "\n";
} else {
    echo "Error creating notification: " . $db->error . "\n";
}

$db->close();
?>
