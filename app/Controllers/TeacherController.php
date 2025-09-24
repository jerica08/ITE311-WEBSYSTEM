<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class TeacherController extends BaseController
{
    public function dashboard()
    {
        // Authorization: must be logged in and teacher
        if (!session()->get('logged_in') || session()->get('user_role') !== 'teacher') {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();

        // Example data: courses taught by this teacher (if courses table exists)
        $courses = [];
        if ($db->tableExists('courses')) {
            $courses = $db->table('courses')
                ->where('instructor_id', session()->get('user_id'))
                ->get()
                ->getResultArray();
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

        // Render teacher-specific dashboard view
        return view('teacher/dashboard', $data);
    }
}
