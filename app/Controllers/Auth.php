<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    public function register()
    {
        if ($this->request->getMethod() === 'post') {
            // Registration processing will be implemented in Step 4
        }

        return view('auth/register');
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


