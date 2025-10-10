<?php

namespace App\Controllers;

use App\Models\UserModel;

class AdminController extends BaseController
{
    public function dashboard()
    {
        $session = session();
        // Authorization: only admin
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $userModel = new UserModel();
        $data = [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'adminUsers' => $userModel->where('role', 'admin')->findAll(),
            'totalUsers' => $userModel->countAllResults(),
        ];

        return view('dashboard/admin', $data);
    }
}
