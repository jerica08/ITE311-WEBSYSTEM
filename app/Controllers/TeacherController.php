<?php

namespace App\Controllers;

use App\Models\UserModel;

class TeacherController extends BaseController
{
    public function dashboard()
    {
        $session = session();
        // Authorization: teacher/instructor only
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['teacher', 'instructor'], true)) {
            return redirect()->to('/auth/login');
        }

        $userModel = new UserModel();
        $data = [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            // Example: list of users the teacher might manage (customize later)
            'students' => $userModel->whereIn('role', ['student', 'user'])->findAll(),
        ];

        return view('dashboard/teacher', $data);
    }
}
