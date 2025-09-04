<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function register()
    {
        // If it's a POST request, handle the registration
        if ($this->request->getMethod() === 'post') {
            $userModel = new UserModel();
            
            $data = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'password_confirm' => $this->request->getPost('password_confirm'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'role' => $this->request->getPost('role')
            ];
            
            // Validate password confirmation
            if ($data['password'] !== $data['password_confirm']) {
                session()->setFlashdata('errors', ['Password confirmation does not match']);
                return redirect()->back()->withInput();
            }
            
            // Remove password_confirm from data array
            unset($data['password_confirm']);
            
            if ($userModel->insert($data)) {
                session()->setFlashdata('success', 'Registration successful! Please log in.');
                return redirect()->to('/auth/login');
            } else {
                $errors = $userModel->errors();
                log_message('error', 'Registration failed: ' . print_r($errors, true));
                session()->setFlashdata('errors', $errors);
                return redirect()->back()->withInput();
            }
        }
        
        // If it's a GET request, show the registration form
        return view('auth/register');
    }

    public function login()
    {
        // If it's a POST request, handle the login
        if ($this->request->getMethod() === 'post') {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            
            // Simple validation
            if (empty($email) || empty($password)) {
                session()->setFlashdata('errors', ['Please fill in all fields']);
                return redirect()->back();
            }
            
            $userModel = new UserModel();
            $user = $userModel->authenticate($email, $password);
            
            // Debug: Log the authentication attempt
            log_message('info', 'Login attempt for email: ' . $email);
            
            if ($user) {
                // Set session data
                session()->set([
                    'isLoggedIn' => true,
                    'user_id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'name' => $user['first_name'] . ' ' . $user['last_name'],
                    'username' => $user['username']
                ]);
                
                // Redirect based on role
                switch ($user['role']) {
                    case 'student':
                        return redirect()->to('/dashboard/student');
                    case 'instructor':
                        return redirect()->to('/dashboard/instructor');
                    case 'admin':
                        return redirect()->to('/dashboard/admin');
                    default:
                        return redirect()->to('/');
                }
            } else {
                session()->setFlashdata('errors', ['Invalid email or password']);
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
}
