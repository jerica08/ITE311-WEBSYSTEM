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
        $data = [];

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required'
            ];

            if ($this->validate($rules)) {
                $userModel = new UserModel();
                $email = $this->request->getPost('email');
                $password = $this->request->getPost('password');

                $user = $userModel->where('email', $email)->first();

                if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
                    $sessionData = [
                        'userId' => $user['id'] ?? null,
                        'name' => $user['name'] ?? '',
                        'email' => $user['email'] ?? '',
                        'role' => $user['role'] ?? 'student',
                        'isLoggedIn' => true
                    ];
                    session()->set($sessionData);
                    session()->setFlashdata('success', 'Welcome back, ' . ($user['name'] ?? 'User') . '!');
                    return redirect()->to('/dashboard');
                }

                $data['error'] = 'Invalid email or password.';
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('auth/login', $data);
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function dashboard()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        return view('auth/dashboard', [
            'name' => session()->get('name'),
            'role' => session()->get('role'),
        ]);
    }
}


