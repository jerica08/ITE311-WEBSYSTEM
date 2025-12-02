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
            return redirect()->to('/login');
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

        return view('admin/admin', $data);
    }

    public function users()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/login');
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

    public function updateUserRole()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/login');
        }

        $userId = (int) ($this->request->getPost('id') ?? 0);
        $role   = (string) $this->request->getPost('role');

        if ($userId <= 0 || !in_array($role, ['admin', 'teacher', 'student'], true)) {
            return redirect()->to('/admin/users')->with('error', 'Invalid user or role.');
        }

        $userModel = new UserModel();

        if (!$userModel->find($userId)) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }

        try {
            $userModel->update($userId, ['role' => $role]);
            return redirect()->to('/admin/users')->with('success', 'User role updated successfully.');
        } catch (\Throwable $e) {
            return redirect()->to('/admin/users')->with('error', 'Failed to update user role.');
        }
    }

    public function editUser($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $user = $userModel->find((int) $id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }

        return view('admin/user_edit', [
            'user' => $user,
        ]);
    }

    public function deleteUser()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/login');
        }

        $userId = (int) ($this->request->getPost('id') ?? 0);
        if ($userId <= 0) {
            return redirect()->to('/admin/users')->with('error', 'Invalid user.');
        }

        $userModel = new UserModel();

        if (!$userModel->find($userId)) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }

        try {
            $userModel->delete($userId);
            return redirect()->to('/admin/users')->with('success', 'User deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->to('/admin/users')->with('error', 'Failed to delete user.');
        }
    }

    public function courses()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/login');
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
}
