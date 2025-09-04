<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function register()
    {
        // If it's a POST request, handle the registration
        if ($this->request->getMethod() === 'post') {
            // Handle registration logic here
            // For now, just redirect back to home
            return redirect()->to('/');
        }
        
        // If it's a GET request, show the registration form
        return view('auth/register');
    }

    public function login()
    {
        // If it's a POST request, handle the login
        if ($this->request->getMethod() === 'post') {
            // Handle login logic here
            // For now, just redirect back to home
            return redirect()->to('/');
        }
        
        // If it's a GET request, show the login form
        return view('auth/login');
    }

    public function logout()
    {
        // Handle logout logic here
        // For now, just redirect to home
        return redirect()->to('/');
    }
}
