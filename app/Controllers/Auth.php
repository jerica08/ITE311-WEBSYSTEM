<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function register()
    {
        $data = [];

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'name' => 'required|min_length[3]|max_length[255]',
                'email' => 'required|valid_email|max_length[255]',
                'password' => 'required|min_length[6]|max_length[255]',
                'password_confirm' => 'required|matches[password]'
            ];

            if ($this->validate($rules)) {
                $userModel = new UserModel();

                $userData = [
                    'name' => $this->request->getPost('name'),
                    'email' => $this->request->getPost('email'),
                    'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'role' => 'student'
                ];

                try {
                    $userModel->insert($userData);
                    session()->setFlashdata('success', 'Registration successful. Please log in.');
                    return redirect()->to('/login');
                } catch (\Throwable $e) {
                    $data['error'] = 'Failed to register. The email may already be in use.';
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('auth/register', $data);
    }

    public function login()
    {
        if ($this->request->getMethod() === 'post') {
            // Login processing will be implemented in Step 5
        }

        return view('auth/login');
    }

    public function logout(): RedirectResponse
    {
        // Session destruction will be implemented in Step 6
        return redirect()->to('/login');
    }

    public function dashboard()
    {
        // Session check will be implemented in Step 6
        return 'Dashboard (protected)';
    }
}


