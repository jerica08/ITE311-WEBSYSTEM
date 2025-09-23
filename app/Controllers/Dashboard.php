<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function student()
    {
        // Check if user is logged in and has student role
        if (!session()->get('logged_in') || session()->get('user_role') !== 'student') {
            return redirect()->to('/login');
        }
        
        $data['user'] = [
            'name' => session()->get('user_name'),
            'email' => session()->get('user_email'),
            'role' => session()->get('user_role')
        ];
        
        return view('dashboard/student', $data);
    }

    public function teacher()
    {
        // Check if user is logged in and has teacher role
        if (!session()->get('logged_in') || session()->get('user_role') !== 'teacher') {
            return redirect()->to('/login');
        }
        
        $data['user'] = [
            'name' => session()->get('user_name'),
            'email' => session()->get('user_email'),
            'role' => session()->get('user_role')
        ];
        
        return view('dashboard/teacher', $data);
    }

    public function admin()
    {
        // Check if user is logged in and has admin role
        if (!session()->get('logged_in') || session()->get('user_role') !== 'admin') {
            return redirect()->to('/login');
        }
        
        $data['user'] = [
            'name' => session()->get('user_name'),
            'email' => session()->get('user_email'),
            'role' => session()->get('user_role')
        ];
        
        return view('dashboard/admin', $data);
    }
}
