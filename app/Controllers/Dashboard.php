<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function student()
    {
        // Check if user is logged in and has student role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'student') {
            return redirect()->to('/auth/login');
        }
        
        $data['user'] = [
            'name' => session()->get('name'),
            'email' => session()->get('email'),
            'role' => session()->get('role')
        ];
        
        return view('dashboard/student', $data);
    }

    public function instructor()
    {
        // Check if user is logged in and has instructor role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'instructor') {
            return redirect()->to('/auth/login');
        }
        
        $data['user'] = [
            'name' => session()->get('name'),
            'email' => session()->get('email'),
            'role' => session()->get('role')
        ];
        
        return view('dashboard/instructor', $data);
    }

    public function admin()
    {
        // Check if user is logged in and has admin role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/auth/login');
        }
        
        $data['user'] = [
            'name' => session()->get('name'),
            'email' => session()->get('email'),
            'role' => session()->get('role')
        ];
        
        return view('dashboard/admin', $data);
    }
}
