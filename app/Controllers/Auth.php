<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $db;
    protected $session;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->session = \Config\Services::session();
    }

   
     /* Display registration form and process form submission
     */
    public function register()
    {
        // If user is already logged in, redirect to dashboard
        if ($this->session->get('user_id')) {
            return redirect()->to('/dashboard');
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
                // Determine role: accept only admin, teacher, student; default to student
                $allowedRoles = ['admin', 'teacher', 'student'];
                $requestedRole = $this->request->getPost('role');
                $role = in_array($requestedRole, $allowedRoles, true) ? $requestedRole : 'student';

                // Prepare user data
                $userData = [
                    'name' => $this->request->getPost('name'),
                    'email' => $this->request->getPost('email'),
                    'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'role' => $role,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // Save user to database using direct query
                $builder = $this->db->table('users');
                if ($builder->insert($userData)) {
                    $this->session->setFlashdata('success', 'Registration successful! Please login.');
                    return redirect()->to('/login');
                } else {
                    $data['error'] = 'Registration failed. Please try again.';
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('auth/register', $data);
    }

    
     /* Display login form and process form submission*/

    public function login()
    {
        // If user is already logged in, redirect to dashboard
        if ($this->session->get('user_id')) {
            return redirect()->to('/dashboard');
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

                // Find user by email using direct query
                $builder = $this->db->table('users');
                $user = $builder->where('email', $email)->get()->getRowArray();

                if ($user && password_verify($password, $user['password'])) {
                    // Set session data
                    $sessionData = [
                        'user_id' => $user['id'],
                        'user_name' => $user['name'],
                        'user_email' => $user['email'],
                        'user_role' => $user['role'],
                        'logged_in' => true
                    ];
                    $this->session->set($sessionData);

                 
                    
                    // Redirect based on role
                    $role = $user['role'] ?? 'student';
                    if ($role === 'admin') {
                        return redirect()->to('/admin/dashboard');
                    } elseif ($role === 'teacher') {
                        return redirect()->to('/teacher/dashboard');
                    } else {
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
        return redirect()->to('/login');
    }

    /**
     * Dashboard page for all logged-in users
     */
    public function dashboard()
    {
        // Check if user is logged in
        if (!$this->session->get('logged_in')) {
            $this->session->setFlashdata('error', 'Please login to access the dashboard.');
            return redirect()->to('/login');
        }

        $data = [
            'user' => [
                'id' => $this->session->get('user_id'),
                'name' => $this->session->get('user_name'),
                'email' => $this->session->get('user_email'),
                'role' => $this->session->get('user_role')
            ]
        ];

        // Add admin statistics if user is admin
        if ($this->session->get('user_role') === 'admin') {
            // Use fresh builders for each count to avoid condition carry-over
            $data['stats'] = [
                'total_users' => $this->db->table('users')->countAll(),
                'total_admins' => $this->db->table('users')->where('role', 'admin')->countAllResults(),
                'total_teachers' => $this->db->table('users')->where('role', 'teacher')->countAllResults(),
                'total_students' => $this->db->table('users')->where('role', 'student')->countAllResults(),
            ];
        }

        return view('dashboard', $data);
    }

}
