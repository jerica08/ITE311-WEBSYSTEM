<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use CodeIgniter\Controller;

class Notifications extends Controller
{
    protected $session;
    protected $notificationModel;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->notificationModel = new NotificationModel();
    }

    public function get()
    {
        $userId = (int) $this->session->get('user_id');
        $userRole = strtolower((string) $this->session->get('role'));
        
        log_message('debug', 'Notifications::get - User ID: ' . $userId . ', Role: ' . $userRole);
        
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        // For teachers/instructors, use the teacher notification system
        if ($userRole === 'teacher' || $userRole === 'instructor') {
            try {
                // Use the NotificationModel's methods for consistency
                $unreadCount = $this->notificationModel->getUnreadCount($userId);
                $notifications = $this->notificationModel->getNotificationsForUser($userId);
                
                log_message('debug', 'Teacher notifications - Unread count: ' . $unreadCount . ', Notifications: ' . json_encode($notifications));

                return $this->response->setJSON([
                    'success' => true,
                    'unread_count' => $unreadCount,
                    'notifications' => $notifications,
                    'csrf_token' => csrf_token(),
                    'csrf_hash' => csrf_hash(),
                ]);
            } catch (\Throwable $e) {
                log_message('error', 'Teacher notification error: ' . $e->getMessage());
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to fetch notifications']);
            }
        }

        // For other users, use the general notification system
        $unreadCount = $this->notificationModel->getUnreadCount($userId);
        $notifications = $this->notificationModel->getNotificationsForUser($userId);
        
        log_message('debug', 'General notifications - Unread count: ' . $unreadCount . ', Notifications: ' . json_encode($notifications));

        return $this->response->setJSON([
            'success' => true,
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
            'csrf_token' => csrf_token(),
            'csrf_hash' => csrf_hash(),
        ]);
    }

    public function mark_as_read($id)
    {
        // Accept POST; also allow AJAX requests to prevent false 405 due to method detection quirks
        $method = strtolower($this->request->getMethod());
        if ($method !== 'post' && !$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }
        log_message('debug', 'Notifications::mark_as_read method=' . $method);

        $userId = (int) $this->session->get('user_id');
        $userRole = strtolower((string) $this->session->get('role'));
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $notificationId = (int) $id;
        if ($notificationId <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid notification ID']);
        }

        // For teachers/instructors, use the teacher notification system
        if ($userRole === 'teacher' || $userRole === 'instructor') {
            try {
                $notification = $this->notificationModel
                    ->where(['id' => $notificationId, 'user_id' => $userId])
                    ->first();
                
                if (!$notification) {
                    return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Notification not found']);
                }

                $updated = $this->notificationModel->markAsRead($notificationId);

                return $this->response->setJSON([
                    'success' => (bool) $updated,
                    'csrf_token' => csrf_token(),
                    'csrf_hash' => csrf_hash(),
                ]);
            } catch (\Throwable $e) {
                log_message('error', 'Teacher mark notification read error: ' . $e->getMessage());
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to mark notification as read']);
            }
        }

        // For other users, use the general notification system
        $notification = $this->notificationModel
            ->where(['id' => $notificationId, 'user_id' => $userId])
            ->first();

        if (!$notification) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Notification not found']);
        }

        $updated = $this->notificationModel->markAsRead($notificationId);

        return $this->response->setJSON([
            'success' => (bool) $updated,
            'csrf_token' => csrf_token(),
            'csrf_hash' => csrf_hash(),
        ]);
    }
}
