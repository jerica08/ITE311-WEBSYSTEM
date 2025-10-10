<?php

namespace App\Controllers;

use App\Models\UserModel;
use Config\Database;

class AdminController extends BaseController
{
    public function dashboard()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $userModel = new UserModel();
        $db = Database::connect();

        // Totals
        $totalUsers = $userModel->countAllResults();
        $totalCourses = 0;
        try {
            if ($db->tableExists('courses')) {
                $totalCourses = (int) $db->table('courses')->countAll();
            }
        } catch (\Throwable $e) {
            $totalCourses = 0;
        }

        // Recent activity: latest 3 registered users
        $recentUsers = $userModel
            ->orderBy('created_at', 'DESC')
            ->select('name,email,role,created_at')
            ->limit(3)
            ->find();

        // User initials for badge on the right
        $name = (string) $session->get('name');
        $initials = '';
        if ($name !== '') {
            $parts = preg_split('/\s+/', trim($name));
            $first = strtoupper(substr($parts[0] ?? '', 0, 1));
            $last  = strtoupper(substr($parts[count($parts)-1] ?? '', 0, 1));
            $initials = $first . $last;
        }

        $data = [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'adminUsers'    => $userModel->where('role', 'admin')->findAll(),
            'totalUsers'    => $totalUsers,
            'totalCourses'  => $totalCourses,
            'recentUsers'   => $recentUsers,
            'userInitials'  => $initials,
        ];

        return view('admin/dashboard', $data);
    }

    public function users()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $userModel = new UserModel();
        $users = $userModel->orderBy('created_at', 'DESC')->findAll();

        return view('admin/users', [
            'user'  => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'users' => $users,
        ]);
    }

    public function courses()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $db = Database::connect();
        $courses = [];
        try {
            if ($db->tableExists('courses')) {
                $courses = $db->table('courses')
                    ->select('id, title, instructor_id, created_at')
                    ->orderBy('created_at', 'DESC')
                    ->get()->getResultArray();
            }
        } catch (\Throwable $e) {
            $courses = [];
        }

        return view('admin/courses', [
            'user'    => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'courses' => $courses,
        ]);
    }

    public function createCourse()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        if (!$this->request->is('post')) {
            return redirect()->to('/admin/courses');
        }

        $title = trim((string) $this->request->getPost('title'));
        $code  = trim((string) ($this->request->getPost('code') ?? ''));
        $unit  = (int) ($this->request->getPost('unit') ?? 0);
        $instructorId = (int) ($this->request->getPost('instructor_id') ?? 0);

        if ($title === '') {
            return redirect()->to('/admin/courses')->with('error', 'Course title is required.');
        }
        if ($instructorId <= 0) {
            return redirect()->to('/admin/courses')->with('error', 'Instructor ID is required and must be a valid user ID.');
        }

        $db = Database::connect();
        try {
            $data = [
                'title' => $title,
                'code'  => $code !== '' ? $code : null,
                'unit'  => $unit > 0 ? $unit : null,
                'instructor_id' => $instructorId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $db->table('courses')->insert($data);
            return redirect()->to('/admin/courses')->with('success', 'Course created successfully.');
        } catch (\Throwable $e) {
            return redirect()->to('/admin/courses')->with('error', 'Failed to create course.');
        }
    }
}
