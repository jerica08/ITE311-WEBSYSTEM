<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id',
        'user_name',
        'action',
        'details',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $useTimestamps = false;
    
    /**
     * Log an activity
     */
    public function logActivity($userId, $userName, $action, $details = null, $ipAddress = null, $userAgent = null)
    {
        $data = [
            'user_id'    => $userId,
            'user_name'  => $userName,
            'action'     => $action,
            'details'    => $details,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        
        return $this->insert($data);
    }
    
    /**
     * Get recent activities
     */
    public function getRecentActivities($limit = 10)
    {
        return $this->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->find();
    }
    
    /**
     * Get activities by user
     */
    public function getActivitiesByUser($userId, $limit = 10)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->find();
    }
}
