<?php

namespace App\Controllers;

use App\Models\UserModel;
use Config\Database;

class TeacherController extends BaseController
{
    public function dashboard()
    {
        $session = session();
        // Authorization: teacher/instructor only
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['teacher', 'instructor'], true)) {
            return redirect()->to('/login');
        }

        $userId = (int) ($session->get('user_id') ?? 0);

        $db = Database::connect();

        // Courses in the system (shown on teacher dashboard)
        $courses = [];
        try {
            if ($db->tableExists('courses')) {
                $coursesRows = $db->table('courses')
                    ->select('id, title, code, unit, instructor_id')
                    ->orderBy('id', 'DESC')
                    ->get()->getResultArray();

                foreach ($coursesRows as $r) {
                    $courses[] = [
                        'id'    => $r['id'] ?? null,
                        'term'  => '-',
                        'title' => $r['title'] ?? '-',
                        'code'  => $r['code'] ?? '-',
                        'unit'  => $r['unit'] ?? '-',
                    ];
                }
            }
        } catch (\Throwable $e) {
            $courses = [];
        }

        // Recent assignment submissions (notifications)
        $submissions = [];
        try {
            if ($db->tableExists('submissions')) {
                $subsRows = $db->table('submissions')
                    ->select('*')
                    ->orderBy('created_at', 'DESC')
                    ->limit(5)
                    ->get()->getResultArray();

                foreach ($subsRows as $s) {
                    $submissions[] = [
                        'submitted_at'    => $s['created_at'] ?? $s['submitted_at'] ?? '-',
                        'student_name'    => $s['student_name'] ?? '-',
                        'course_title'    => $s['course_title'] ?? '-',
                        'assignment_title'=> $s['assignment_title'] ?? '-',
                        'status'          => $s['status'] ?? '-',
                    ];
                }
            }
        } catch (\Throwable $e) {
            $submissions = [];
        }

        $data = [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'courses'     => $courses,
            'submissions' => $submissions,
        ];

        // Use the existing teacher.php view
        return view('teacher', $data);
    }

    public function createCourse()
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['teacher', 'instructor'], true)) {
            return redirect()->to('/login');
        }

        if (!$this->request->is('post')) {
            return redirect()->to('/t-dashboard');
        }

        $title = trim((string) $this->request->getPost('title'));
        $code  = trim((string) ($this->request->getPost('code') ?? ''));
        $unit  = (int) ($this->request->getPost('unit') ?? 0);
        $instructorId = (int) ($session->get('user_id') ?? 0);

        if ($title === '') {
            return redirect()->to('/t-dashboard')->with('error', 'Course title is required.');
        }

        $db = Database::connect();
        try {
            $data = [
                'title' => $title,
                'code'  => $code !== '' ? $code : null,
                'unit'  => $unit > 0 ? $unit : null,
                'instructor_id' => $instructorId > 0 ? $instructorId : null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $db->table('courses')->insert($data);
            return redirect()->to('/t-dashboard')->with('success', 'Course created successfully.');
        } catch (\Throwable $e) {
            return redirect()->to('/t-dashboard')->with('error', 'Failed to create course.');
        }
    }
}
