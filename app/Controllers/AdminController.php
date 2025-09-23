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
        $stats = [
            'total_users'    => $db->table('users')->countAll(),
            'total_admins'   => $db->table('users')->where('role', 'admin')->countAllResults(),
            'total_teachers' => $db->table('users')->where('role', 'teacher')->countAllResults(),
            'total_students' => $db->table('users')->where('role', 'student')->countAllResults(),
        ];

        $data = [
            'user' => [
                'id'    => session()->get('user_id'),
                'name'  => session()->get('user_name'),
                'email' => session()->get('user_email'),
                'role'  => session()->get('user_role'),
            ],
            'stats' => $stats,
        ];

        // Reuse existing dashboard view
        return view('dashboard', $data);
    }
}
