<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'message', 'is_read', 'created_at'];

    protected $useTimestamps    = false;

    public function getUnreadCount(int $userId): int
    {
        return $this->where(['user_id' => $userId, 'is_read' => 0])->countAllResults();
    }

    public function getNotificationsForUser(int $userId): array
    {
        return $this->where(['user_id' => $userId, 'is_read' => 0])
            ->orderBy('created_at', 'DESC')
            ->findAll(5);
    }

    public function markAsRead(int $notificationId): bool
    {
        return (bool) $this->update($notificationId, ['is_read' => 1]);
    }
}
