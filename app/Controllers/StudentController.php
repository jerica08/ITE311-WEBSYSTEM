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
        $data = [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            // Example: data a student might see
            'teachers' => $userModel->whereIn('role', ['teacher', 'instructor'])->findAll(),
        ];

        return view('dashboard/student', $data);
    }
}
