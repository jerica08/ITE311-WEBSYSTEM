<?php

namespace App\Controllers;

use App\Models\UserModel;

class StudentController extends BaseController
{
    public function dashboard()
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        // Authorization: student or generic user
        if (!$session->get('isLoggedIn') || !in_array($role, ['student', 'user'], true)) {
            return redirect()->to('/auth/login');
        }

        $userModel = new UserModel();

        // Placeholder datasets; wire up to real models later
        $enrollments = [];
        $deadlines   = [];
        $grades      = [];

        $data = [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'enrollments' => $enrollments,
            'deadlines'   => $deadlines,
            'grades'      => $grades,
        ];

        return view('student/dashboard', $data);
    }
}
