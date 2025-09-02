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
                'email' => 'required|valid_email|max_length[255]|is_unique[users.email]',
                'password' => 'required|min_length[6]|max_length[255]',
                'password_confirm' => 'required|matches[password]',
                'role' => 'required|in_list[student,instructor,admin]'
            ];

            if ($this->validate($rules)) {
                $userModel = new UserModel();

                $userData = [
                    'name' => $this->request->getPost('name'),
                    'email' => $this->request->getPost('email'),
                    'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'role' => $this->request->getPost('role')
                ];

                try {
                    $userModel->insert($userData);
                    session()->setFlashdata('success', 'Registration successful. Please log in.');
                    return redirect()->to('/login');
                } catch (\Throwable $e) {
                    log_message('error', 'Registration error: {message}', ['message' => $e->getMessage()]);
                    return redirect()->back()->withInput()->with('errors', ['general' => 'Failed to register. Please try again.']);
                }
            } else {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
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

                return redirect()->back()->withInput()->with('errors', ['credentials' => 'Invalid email or password.']);
            } else {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
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

        $role = session()->get('role');
        if ($role === 'admin') {
            return redirect()->to('/dashboard/admin');
        }
        if ($role === 'instructor') {
            return redirect()->to('/dashboard/instructor');
        }
        return redirect()->to('/dashboard/student');
    }

    public function dashboardStudent()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        if (session()->get('role') !== 'student') {
            return redirect()->to('/dashboard');
        }
        return view('auth/dashboard_student', [
            'name' => session()->get('name'),
            'role' => session()->get('role'),
        ]);
    }

    public function dashboardInstructor()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        if (session()->get('role') !== 'instructor') {
            return redirect()->to('/dashboard');
        }
        return view('auth/dashboard_instructor', [
            'name' => session()->get('name'),
            'role' => session()->get('role'),
        ]);
    }

    public function dashboardAdmin()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }
        return view('auth/dashboard_admin', [
            'name' => session()->get('name'),
            'role' => session()->get('role'),
        ]);
    }
}


