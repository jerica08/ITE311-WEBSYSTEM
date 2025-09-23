<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class StudentController extends BaseController
{
    public function dashboard()
    {
        // Authorization: must be logged in and student
        if (!session()->get('logged_in') || session()->get('user_role') !== 'student') {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();

        // Example data: enrolled courses for this student (if a junction table exists)
        $courses = [];
        if ($db->tableExists('courses')) {
            // Placeholder: if you later add a enrollments table, join here
            $courses = $db->table('courses')->get()->getResultArray();
        }

        $data = [
            'user' => [
                'id'    => session()->get('user_id'),
                'name'  => session()->get('user_name'),
                'email' => session()->get('user_email'),
                'role'  => session()->get('user_role'),
            ],
            'courses' => $courses,
        ];

        // Reuse existing dashboard view
        return view('dashboard', $data);
    }
}
