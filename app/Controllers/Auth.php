<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function register()
    {
        $data = [];
        log_message('info', 'Register request method: {method}', ['method' => $this->request->getMethod()]);

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'name' => 'required|min_length[3]|max_length[100]',
                'email' => 'required|valid_email|max_length[100]|is_unique[users.email]',
                'password' => 'required|min_length[6]|max_length[255]',
                'password_confirm' => 'required|matches[password]',
                'role' => 'required|in_list[student,instructor,admin]'
            ];

            log_message('info', 'Register POST data: {data}', ['data' => json_encode($this->request->getPost())]);
            if ($this->validate($rules)) {
                $userModel = new UserModel();

                $userData = [
                    'name' => $this->request->getPost('name'),
                    'email' => $this->request->getPost('email'),
                    'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'role' => $this->request->getPost('role')
                ];

                try {
                    $result = $userModel->insert($userData);
                    if ($result) {
                        log_message('info', 'User registered successfully: ' . $this->request->getPost('email'));
                        session()->setFlashdata('success', 'Registration successful. Please log in.');
                        return redirect()->to('/login');
                    } else {
                        $errors = $userModel->errors();
                        log_message('error', 'Registration failed: ' . json_encode($errors));
                        return redirect()->back()->withInput()->with('errors', $errors ?: ['general' => 'Failed to register. Please try again.']);
                    }
                } catch (\Throwable $e) {
                    log_message('error', 'Registration error: {message}', ['message' => $e->getMessage()]);
                    return redirect()->back()->withInput()->with('errors', ['general' => 'Database error: ' . $e->getMessage()]);
                }
            } else {
                log_message('info', 'Registration validation failed: ' . json_encode($this->validator->getErrors()));
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        }

        return view('auth/register', $data);
    }

    public function login()
    {
        $data = [];
        log_message('info', 'Login request method: {method}', ['method' => $this->request->getMethod()]);

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required'
            ];

            log_message('info', 'Login POST data email: {email}', ['email' => $this->request->getPost('email')]);
            if ($this->validate($rules)) {
                $userModel = new UserModel();
                $email = $this->request->getPost('email');
                $password = $this->request->getPost('password');

                $user = $userModel->where('email', $email)->first();
                log_message('info', 'Login user lookup found: {found}', ['found' => $user ? 'yes' : 'no']);

                if ($user) {
                    // Debug logging
                    log_message('info', 'Login attempt for email: ' . $email);
                    log_message('info', 'User found: ' . json_encode($user));
                    
                    if (password_verify($password, $user['password'])) {
                        $sessionData = [
                            'userId' => $user['id'],
                            'name' => $user['name'],
                            'email' => $user['email'],
                            'role' => $user['role'],
                            'isLoggedIn' => true
                        ];
                        session()->set($sessionData);
                        session()->setFlashdata('success', 'Welcome back, ' . $user['name'] . '!');
                        return redirect()->to('/dashboard');
                    } else {
                        log_message('info', 'Password verification failed for: ' . $email);
                        return redirect()->back()->withInput()->with('errors', ['credentials' => 'Invalid email or password.']);
                    }
                } else {
                    log_message('info', 'No user found with email: ' . $email);
                    return redirect()->back()->withInput()->with('errors', ['credentials' => 'Invalid email or password.']);
                }
            } else {
                log_message('error', 'Login validation failed: {errors}', ['errors' => json_encode($this->validator->getErrors())]);
                $data['errors'] = $this->validator->getErrors();
                return view('auth/login', $data);
            }
        }

        return view('auth/login', $data);
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();
        return redirect()->to(site_url('login'));
    }

    public function dashboard()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        $role = session()->get('role');
        if ($role === 'admin') {
            return redirect()->to(site_url('dashboard/admin'));
        }
        if ($role === 'instructor') {
            return redirect()->to(site_url('dashboard/instructor'));
        }
        return redirect()->to(site_url('dashboard/student'));
    }

    public function dashboardStudent()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }
        if (session()->get('role') !== 'student') {
            return redirect()->to(site_url('dashboard'));
        }
        return view('auth/dashboard_student', [
            'name' => session()->get('name'),
            'role' => session()->get('role'),
        ]);
    }

    public function dashboardInstructor()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }
        if (session()->get('role') !== 'instructor') {
            return redirect()->to(site_url('dashboard'));
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


