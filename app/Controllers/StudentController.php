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

                // Filter to only show approved enrollments (temporarily removing date filtering)
                $enrolledCourses = array_values(array_filter($enrolledCourses, static function (array $c): bool {
                    // Only show approved courses
                    if (!isset($c['enrollment_status']) || $c['enrollment_status'] !== 'approved') {
                        return false;
                    }
                    
                    // Temporarily skip date filtering to ensure approved courses show up
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
                    ->select('c.id, c.title, c.code, c.unit, c.course_level, c.department, c.academic_year, c.course_start_date, c.course_end_date, u.name AS instructor_name')
                    ->join('users u', 'u.id = c.instructor_id', 'left');

                // Temporarily remove date filtering to show all courses
                // $today = date('Y-m-d');
                // $escapedToday = $db->escape($today);
                // $builder->where("(c.course_start_date IS NULL OR c.course_start_date <= $escapedToday)", null, false);
                // $builder->where("(c.course_end_date IS NULL OR c.course_end_date >= $escapedToday)", null, false);

                // Exclude courses already enrolled (including pending and approved ones)
                $enrolledIds = array_column($enrolledCourses, 'id');
                // Also get pending enrollments to exclude them from available courses
                $pendingEnrollments = $enrollmentModel
                    ->where('user_id', $userId)
                    ->where('enrollment_status', 'pending')
                    ->findAll();
                $pendingIds = array_column($pendingEnrollments, 'course_id');
                
                // Also get approved enrollments to exclude them from available courses
                $approvedEnrollments = $enrollmentModel
                    ->where('user_id', $userId)
                    ->where('enrollment_status', 'approved')
                    ->findAll();
                $approvedIds = array_column($approvedEnrollments, 'course_id');
                
                $excludeIds = array_merge($enrolledIds, $pendingIds, $approvedIds);
                if (!empty($excludeIds)) {
                    $builder->whereNotIn('c.id', $excludeIds);
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
