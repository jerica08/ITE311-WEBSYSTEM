<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EnrollmentModel;
use Config\Database;

class StudentController extends BaseController
{
    public function dashboard()
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        // Authorization: student or generic user
        if (!$session->get('isLoggedIn') || !in_array($role, ['student', 'user'], true)) {
            return redirect()->to('/auth/login');
        }

        $userModel = new UserModel();

        $userId = (int) ($session->get('user_id') ?? 0);

        // Enrolled courses via EnrollmentModel join
        $enrolledCourses = [];
        try {
            $enrollmentModel = new EnrollmentModel();
            if ($userId > 0) {
                $enrolledCourses = $enrollmentModel->getUserEnrollments($userId);
            }
        } catch (\Throwable $e) {
            $enrolledCourses = [];
        }

        // Available courses = courses not yet enrolled by user
        $availableCourses = [];
        try {
            $db = Database::connect();
            if ($db->tableExists('courses')) {
                $builder = $db->table('courses')->select('id, title, code, unit');
                $enrolledIds = array_column($enrolledCourses, 'id');
                if (!empty($enrolledIds)) {
                    $builder->whereNotIn('id', $enrolledIds);
                }
                $availableCourses = $builder->orderBy('id', 'DESC')->get()->getResultArray();
            }
        } catch (\Throwable $e) {
            $availableCourses = [];
        }

        // Placeholder datasets for other sections
        $deadlines   = [];
        $grades      = [];

        $data = [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'enrolledCourses' => $enrolledCourses,
            'availableCourses' => $availableCourses,
            'deadlines'   => $deadlines,
            'grades'      => $grades,
        ];

        return view('student/dashboard', $data);
    }
}
