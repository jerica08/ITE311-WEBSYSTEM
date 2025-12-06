<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EnrollmentModel;
use App\Models\MaterialModel;
use Config\Database;

class StudentController extends BaseController
{
    public function dashboard()
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        // Authorization: student or generic user
        if (!$session->get('isLoggedIn') || !in_array($role, ['student', 'user'], true)) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();

        $userId = (int) ($session->get('user_id') ?? 0);

        // Enrolled courses via EnrollmentModel join (filtered by course date window)
        $enrolledCourses = [];
        try {
            $enrollmentModel = new EnrollmentModel();
            if ($userId > 0) {
                $enrolledCourses = $enrollmentModel->getUserEnrollments($userId);

                // Filter out courses that are outside their active date range
                $today = date('Y-m-d');
                $enrolledCourses = array_values(array_filter($enrolledCourses, static function (array $c) use ($today): bool {
                    $start = isset($c['start_date']) && $c['start_date'] !== null && $c['start_date'] !== ''
                        ? substr((string) $c['start_date'], 0, 10)
                        : null;
                    $end = isset($c['end_date']) && $c['end_date'] !== null && $c['end_date'] !== ''
                        ? substr((string) $c['end_date'], 0, 10)
                        : null;

                    if ($start !== null && $today < $start) {
                        return false; // not started yet
                    }
                    if ($end !== null && $today > $end) {
                        return false; // already ended
                    }
                    return true;
                }));
            }
        } catch (\Throwable $e) {
            $enrolledCourses = [];
        }

        // Available courses = courses not yet enrolled by user
        $availableCourses = [];
        try {
            $db = Database::connect();
            if ($db->tableExists('courses')) {
                $builder = $db->table('courses c')
                    ->select('c.id, c.title, c.code, c.unit, c.academic_year, c.start_date, c.end_date, u.name AS instructor_name')
                    ->join('users u', 'u.id = c.instructor_id', 'left');

                // Only currently active courses (by date window)
                $today = date('Y-m-d');
                $escapedToday = $db->escape($today);
                $builder->where("(c.start_date IS NULL OR c.start_date <= $escapedToday)", null, false);
                $builder->where("(c.end_date IS NULL OR c.end_date >= $escapedToday)", null, false);

                // Exclude courses already enrolled
                $enrolledIds = array_column($enrolledCourses, 'id');
                if (!empty($enrolledIds)) {
                    $builder->whereNotIn('c.id', $enrolledIds);
                }

                $availableCourses = $builder->orderBy('c.id', 'DESC')->get()->getResultArray();
            }
        } catch (\Throwable $e) {
            $availableCourses = [];
        }

        // Materials for enrolled courses
        $materialsByCourse = [];
        try {
            $courseIds = array_column($enrolledCourses, 'id');
            if (!empty($courseIds)) {
                $materialModel = new MaterialModel();
                $materials = $materialModel->whereIn('course_id', $courseIds)
                    ->orderBy('id', 'DESC')
                    ->findAll();
                foreach ($materials as $m) {
                    $cid = (int) ($m['course_id'] ?? 0);
                    if (!isset($materialsByCourse[$cid])) {
                        $materialsByCourse[$cid] = [];
                    }
                    $materialsByCourse[$cid][] = $m;
                }
            }
        } catch (\Throwable $e) {
            $materialsByCourse = [];
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
            'materialsByCourse' => $materialsByCourse,
            'deadlines'   => $deadlines,
            'grades'      => $grades,
        ];

        return view('student', $data);
    }
}
