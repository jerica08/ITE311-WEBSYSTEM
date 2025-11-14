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
            return redirect()->to('/auth/login');
        }

        $userId = (int) ($session->get('user_id') ?? 0);

        $db = Database::connect();

        // Courses taught by this teacher
        $courses = [];
        try {
            if ($db->tableExists('courses')) {
                $builder = $db->table('courses')->select('id, title, instructor_id');
                if ($userId > 0) {
                    $builder->where('instructor_id', $userId);
                }
                $coursesRows = $builder->orderBy('id', 'DESC')->get()->getResultArray();
                foreach ($coursesRows as $r) {
                    $courses[] = [
                        'term' => $r['term'] ?? '-',
                        'title' => $r['title'] ?? '-',
                        'code' => $r['code'] ?? '-',
                        'unit' => $r['unit'] ?? '-',
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

        return view('teacher/dashboard', $data);
    }
}
