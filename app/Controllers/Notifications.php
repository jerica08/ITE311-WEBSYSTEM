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
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $unreadCount = $this->notificationModel->getUnreadCount($userId);
        $notifications = $this->notificationModel->getNotificationsForUser($userId);

        return $this->response->setJSON([
            'success' => true,
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    public function mark_as_read($id)
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method Not Allowed']);
        }

        $userId = (int) $this->session->get('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $notificationId = (int) $id;
        if ($notificationId <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid notification ID']);
        }

        $notification = $this->notificationModel
            ->where(['id' => $notificationId, 'user_id' => $userId])
            ->first();

        if (!$notification) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Notification not found']);
        }

        $updated = $this->notificationModel->markAsRead($notificationId);

        return $this->response->setJSON([
            'success' => (bool) $updated,
        ]);
    }
}
