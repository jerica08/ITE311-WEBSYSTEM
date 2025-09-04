<?php

namespace App\Controllers;

use App\Models\UserModel;
use Exception;

class Auth extends BaseController
{
    public function register()
    {
        // Debug: Log the request method
        log_message('info', 'Register method called. Method: ' . $this->request->getMethod());
        echo "<!-- DEBUG: Register method called. Method: " . $this->request->getMethod() . " -->";
        
        // If it's a POST request, handle the registration
        if ($this->request->getMethod() === 'post') {
            $userModel = new UserModel();
            
            $data = [
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'password_confirm' => $this->request->getPost('password_confirm'),
                'role' => $this->request->getPost('role')
            ];
            
            // Debug: Log the received data
            log_message('info', 'Registration data received: ' . print_r($data, true));
            echo "<!-- DEBUG: Registration data received: " . print_r($data, true) . " -->";
            
            // Validate password confirmation
            if ($data['password'] !== $data['password_confirm']) {
                log_message('error', 'Password confirmation mismatch');
                session()->setFlashdata('errors', ['password_confirm' => 'Password confirmation does not match']);
                return redirect()->back()->withInput();
            }
            
            // Remove password_confirm from data array
            unset($data['password_confirm']);
            
            // Try to insert the user
            try {
                // Temporarily disable validation to test
                $userModel->setValidationRules([]);
                if ($userModel->insert($data)) {
                    log_message('info', 'Registration successful for: ' . $data['email']);
                    session()->setFlashdata('success', 'Registration successful! Please log in.');
                    return redirect()->to('/login');
                } else {
                    $errors = $userModel->errors();
                    log_message('error', 'Registration failed for: ' . $data['email'] . ' - Errors: ' . print_r($errors, true));
                    session()->setFlashdata('errors', $errors);
                    return redirect()->back()->withInput();
                }
            } catch (Exception $e) {
                log_message('error', 'Registration exception: ' . $e->getMessage());
                session()->setFlashdata('errors', ['general' => 'Registration failed: ' . $e->getMessage()]);
                return redirect()->back()->withInput();
            }
        }
        
        // If it's a GET request, show the registration form
        return view('auth/register');
    }

    public function login()
    {
        // Debug: Log the request method
        log_message('info', 'Login method called. Method: ' . $this->request->getMethod());
        echo "<!-- DEBUG: Login method called. Method: " . $this->request->getMethod() . " -->";
        
        // If it's a POST request, handle the login
        if ($this->request->getMethod() === 'post') {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            
            // Debug: Log the login attempt
            log_message('info', 'Login attempt for email: ' . $email);
            echo "<!-- DEBUG: Login attempt for email: " . $email . " -->";
            
            // Simple validation
            if (empty($email) || empty($password)) {
                log_message('error', 'Login failed: Empty email or password');
                session()->setFlashdata('errors', ['general' => 'Please fill in all fields']);
                return redirect()->back();
            }
            
            $userModel = new UserModel();
            
            try {
                $user = $userModel->authenticate($email, $password);
                
                if ($user) {
                    log_message('info', 'Login successful for: ' . $email);
                    
                    // Set session data
                    session()->set([
                        'isLoggedIn' => true,
                        'user_id' => $user['id'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'name' => $user['name']
                    ]);
                    
                    // Redirect to main dashboard (which will redirect to role-specific dashboard)
                    return redirect()->to('/dashboard');
                } else {
                    log_message('error', 'Login failed: Invalid credentials for ' . $email);
                    session()->setFlashdata('errors', ['general' => 'Invalid email or password']);
                    return redirect()->back();
                }
            } catch (Exception $e) {
                log_message('error', 'Login exception: ' . $e->getMessage());
                session()->setFlashdata('errors', ['general' => 'Login failed: ' . $e->getMessage()]);
                return redirect()->back();
            }
        }
        
        // If it's a GET request, show the login form
        return view('auth/login');
    }

    public function logout()
    {
        // Clear all session data
        session()->destroy();
        
        // Redirect to home page
        return redirect()->to('/');
    }

    public function dashboard()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        
        // Redirect to role-specific dashboard
        $role = session()->get('role');
        switch ($role) {
            case 'student':
                return redirect()->to('/dashboard/student');
            case 'instructor':
                return redirect()->to('/dashboard/instructor');
            case 'admin':
                return redirect()->to('/dashboard/admin');
            default:
                return redirect()->to('/');
        }
    }

    public function dashboardStudent()
    {
        // Check if user is logged in and has student role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'student') {
            return redirect()->to('/login');
        }
        
        $data['user'] = [
            'name' => session()->get('name'),
            'email' => session()->get('email'),
            'role' => session()->get('role')
        ];
        
        return view('dashboard/student', $data);
    }

    public function dashboardInstructor()
    {
        // Check if user is logged in and has instructor role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'instructor') {
            return redirect()->to('/login');
        }
        
        $data['user'] = [
            'name' => session()->get('name'),
            'email' => session()->get('email'),
            'role' => session()->get('role')
        ];
        
        return view('dashboard/instructor', $data);
    }

    public function dashboardAdmin()
    {
        // Check if user is logged in and has admin role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }
        
        $data['user'] = [
            'name' => session()->get('name'),
            'email' => session()->get('email'),
            'role' => session()->get('role')
        ];
        
        return view('dashboard/admin', $data);
    }
}
