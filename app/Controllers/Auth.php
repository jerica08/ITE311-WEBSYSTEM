<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends BaseController
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = service('session');
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

        // Process form submission
        if ($this->request->getMethod() === 'POST') {
            // Validation rules
            $rules = [
                'username' => [
                    'rules' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
                    'errors' => [
                        'required' => 'Username is required',
                        'min_length' => 'Username must be at least 3 characters',
                        'max_length' => 'Username cannot exceed 50 characters',
                        'is_unique' => 'Username already exists'
                    ]
                ],
                'email' => [
                    'rules' => 'required|valid_email|is_unique[users.email]',
                    'errors' => [
                        'required' => 'Email is required',
                        'valid_email' => 'Please enter a valid email address',
                        'is_unique' => 'Email already exists'
                    ]
                ],
                'password' => [
                    'rules' => 'required|min_length[6]',
                    'errors' => [
                        'required' => 'Password is required',
                        'min_length' => 'Password must be at least 6 characters'
                    ]
                ],
                'confirm_password' => [
                    'rules' => 'required|matches[password]',
                    'errors' => [
                        'required' => 'Password confirmation is required',
                        'matches' => 'Passwords do not match'
                    ]
                ]
            ];

            // Validate input
            if (!$this->validate($rules)) {
                return view('auth/register', [
                    'validation' => $this->validator,
                    'title' => 'Register'
                ]);
            }

            // Prepare user data
            $userData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Save user to database
            if ($this->userModel->save($userData)) {
                $this->session->setFlashdata('success', 'Registration successful! Please login.');
                return redirect()->to('/auth/login');
            } else {
                $this->session->setFlashdata('error', 'Registration failed. Please try again.');
            }
        }

        // Display registration form
        return view('auth/register', ['title' => 'Register']);
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

        // Process form submission
        if ($this->request->getMethod() === 'POST') {
            // Validation rules
            $rules = [
                'username' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Username is required'
                    ]
                ],
                'password' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Password is required'
                    ]
                ]
            ];

            // Validate input
            if (!$this->validate($rules)) {
                return view('auth/login', [
                    'validation' => $this->validator,
                    'title' => 'Login'
                ]);
            }

            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            // Find user by username
            $user = $this->userModel->where('username', $username)->first();

            if ($user && password_verify($password, $user['password'])) {
                // Set session data
                $sessionData = [
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'logged_in' => true
                ];
                $this->session->set($sessionData);

                $this->session->setFlashdata('success', 'Welcome back, ' . $user['username'] . '!');
                return redirect()->to('/auth/dashboard');
            } else {
                $this->session->setFlashdata('error', 'Invalid username or password.');
            }
        }

        // Display login form
        return view('auth/login', ['title' => 'Login']);
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
            'title' => 'Dashboard',
            'user' => [
                'id' => $this->session->get('user_id'),
                'username' => $this->session->get('username'),
                'email' => $this->session->get('email')
            ]
        ];

        return view('auth/dashboard', $data);
    }

    /**
     * Helper method to check if user is logged in (for other controllers)
     */
    public function isLoggedIn()
    {
        return $this->session->get('logged_in') === true;
    }

    /**
     * Helper method to get current user data (for other controllers)
     */
    public function getCurrentUser()
    {
        if ($this->isLoggedIn()) {
            return [
                'id' => $this->session->get('user_id'),
                'username' => $this->session->get('username'),
                'email' => $this->session->get('email')
            ];
        }
        return null;
    }
}
