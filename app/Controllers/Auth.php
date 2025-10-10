<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = \Config\Services::session();
        helper(['form', 'url']);
    }

    /**
     * Display registration form and process form submission
     */
    public function register()
    {
        // If user is already logged in, redirect to dashboard
        if ($this->session->get('user_id')) {
            return redirect()->to('/auth/dashboard');
        }

        $data = [];

        if ($this->request->getMethod() === 'POST') {
            // Validation rules
            $rules = [
                'name' => 'required|min_length[3]|max_length[100]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'confirm_password' => 'required|matches[password]'
            ];

            if ($this->validate($rules)) {
                // Prepare user data
                $userData = [
                    'name' => $this->request->getPost('name'),
                    'email' => $this->request->getPost('email'),
                    'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'role' => 'user',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // Save user to database
                if ($this->userModel->save($userData)) {
                    $this->session->setFlashdata('success', 'Registration successful! Please login.');
                    return redirect()->to('/auth/login');
                } else {
                    $data['error'] = 'Registration failed. Please try again.';
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('auth/register', $data);
    }

    /**
     * Display login form and process form submission
     */
    public function login()
    {
        // If user is already logged in, redirect to dashboard
        if ($this->session->get('user_id')) {
            return redirect()->to('/auth/dashboard');
        }

        $data = [];

        if ($this->request->getMethod() === 'POST') {
            // Validation rules
            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required'
            ];

            if ($this->validate($rules)) {
                $email = $this->request->getPost('email');
                $password = $this->request->getPost('password');

                // Find user by email
                $user = $this->userModel->where('email', $email)->first();

                if ($user && password_verify($password, $user['password'])) {
                    // Set session data (provide both legacy and unified keys)
                    $sessionData = [
                        'user_id'    => $user['id'],
                        'user_name'  => $user['name'],
                        'user_email' => $user['email'],
                        'user_role'  => $user['role'],
                        'logged_in'  => true,          // existing usage in Auth
                        // Keys expected by Dashboard controller
                        'isLoggedIn' => true,
                        'name'       => $user['name'],
                        'email'      => $user['email'],
                        'role'       => $user['role'],
                    ];
                    $this->session->set($sessionData);

                    $this->session->setFlashdata('success', 'Welcome back, ' . $user['name'] . '!');

                    // Role-based redirection
                    $role = strtolower((string) $user['role']);
                    switch ($role) {
                        case 'admin':
                            return redirect()->to('/admin/dashboard');
                        case 'instructor':
                        case 'teacher':
                            return redirect()->to('/teacher/dashboard');
                        case 'student':
                            return redirect()->to('/student/dashboard');
                        default:
                            // default regular users go to student dashboard
                            return redirect()->to('/student/dashboard');
                    }
                } else {
                    $data['error'] = 'Invalid email or password.';
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('auth/login', $data);
    }

    /**
     * Destroy user session and redirect
     */
    public function logout()
    {
        // Destroy session
        $this->session->destroy();
        
        $this->session->setFlashdata('success', 'You have been logged out successfully.');
        return redirect()->to('/auth/login');
    }

    /**
     * Protected dashboard page for logged-in users only
     */
    public function dashboard()
    {
        // Check if user is logged in
        if (!$this->session->get('logged_in')) {
            $this->session->setFlashdata('error', 'Please login to access the dashboard.');
            return redirect()->to('/auth/login');
        }

        $data = [
            'user' => [
                'id' => $this->session->get('user_id'),
                'name' => $this->session->get('user_name'),
                'email' => $this->session->get('user_email'),
                'role' => $this->session->get('user_role')
            ]
        ];

        return view('auth/dashboard', $data);
    }

    /**
     * Check if user is authenticated (helper method)
     */
    private function isAuthenticated()
    {
        return $this->session->get('logged_in') === true;
    }

    /**
     * Check if user has specific role (helper method)
     */
    private function hasRole($role)
    {
        return $this->session->get('user_role') === $role;
    }
}
