<?php
// Simple test to check notification system
require_once 'vendor/autoload.php';

// Initialize CodeIgniter
$app = new CodeIgniter\CodeIgniter();
$app->initialize();

// Get session
$session = \Config\Services::session();

// Test notification creation
$notificationModel = new \App\Models\NotificationModel();

// Get current logged-in user
$userId = $session->get('user_id');
echo "Current User ID: " . $userId . "\n";

// Get unread count
$unreadCount = $notificationModel->getUnreadCount($userId);
echo "Unread count: " . $unreadCount . "\n";

// Get notifications
$notifications = $notificationModel->getNotificationsForUser($userId);
echo "Notifications: " . json_encode($notifications) . "\n";

// Create test notification
if ($userId) {
    $testData = [
        'user_id' => $userId,
        'title' => 'Test Notification',
        'message' => 'This is a test notification',
        'type' => 'test',
        'related_id' => 0,
        'is_read' => 0,
        'created_at' => date('Y-m-d H:i:s'),
    ];
    
    $result = $notificationModel->insert($testData);
    echo "Test notification insert result: " . $result . "\n";
}
?>
