<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class AdminController extends BaseController
{
    public function dashboard()
    {
        // Authorization: must be logged in and admin
        if (!session()->get('logged_in') || session()->get('user_role') !== 'admin') {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();

        // Prepare admin stats
        $totalUsers = $db->table('users')->countAll();

        // Compute total courses if the 'courses' table exists (fallback to 0)
        $tables = $db->listTables();
        $totalCourses = \is_array($tables) && \in_array('courses', $tables, true)
            ? $db->table('courses')->countAll()
            : 0;

        $stats = [
            'total_users'    => $totalUsers,
            'total_admins'   => $db->table('users')->where('role', 'admin')->countAllResults(),
            'total_teachers' => $db->table('users')->where('role', 'teacher')->countAllResults(),
            'total_students' => $db->table('users')->where('role', 'student')->countAllResults(),
            // Provide generic keys used by the view as fallback
            'users'          => $totalUsers,
            'courses'        => $totalCourses,
        ];

        // Build a simple recent activity feed (fallback): latest users created
        $recentUsers = $db->table('users')
            ->select('name, email, role, created_at')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $recentActivities = [];
        foreach ($recentUsers as $u) {
            $recentActivities[] = [
                'time'   => isset($u['created_at']) ? (string)$u['created_at'] : '',
                'user'   => isset($u['name']) ? (string)$u['name'] : (isset($u['email']) ? (string)$u['email'] : ''),
                'action' => 'User Registered',
                'details'=> isset($u['role']) ? ('Role: ' . (string)$u['role']) : '',
            ];
        }

        $data = [
            'user' => [
                'id'    => session()->get('user_id'),
                'name'  => session()->get('user_name'),
                'email' => session()->get('user_email'),
                'role'  => session()->get('user_role'),
            ],
            'stats'            => $stats,
            'totalUsers'       => $totalUsers,
            'totalCourses'     => $totalCourses,
            'recentActivities' => $recentActivities,
        ];

        // Render admin-specific dashboard view
        return view('admin/dashboard', $data);
    }
}
