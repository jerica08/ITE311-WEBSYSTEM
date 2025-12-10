<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
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

        // Courses taught by this teacher (only published courses)
        $courses = [];
        try {
            if ($db->tableExists('courses')) {
                $builder = $db->table('courses')
                    ->select('id, title, code, unit, course_level, department, created_at, instructor_id');
                if ($userId > 0) {
                    $builder->where('instructor_id', $userId);
                }
                $builder->where('status', 'published'); // Only show published courses
                $coursesRows = $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
                foreach ($coursesRows as $r) {
                    $courses[] = [
                        'id'         => $r['id'] ?? null,
                        'title'      => $r['title'] ?? '-',
                        'code'       => $r['code'] ?? '-',
                        'unit'       => $r['unit'] ?? '-',
                        'course_level' => $r['course_level'] ?? '-',
                        'department'   => $r['department'] ?? '-',
                        'created_at' => $r['created_at'] ?? '-',
                    ];
                }
            }
        } catch (\Throwable $e) {
            $courses = [];
        }

        // Pending enrollments for this teacher's courses
        $pendingEnrollments = [];
        try {
            $enrollmentModel = new EnrollmentModel();
            if ($userId > 0) {
                $pendingEnrollments = $enrollmentModel
                    ->select('enrollments.*, courses.title as course_title, courses.code as course_code, users.name as student_name, users.email as student_email')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->join('users', 'users.id = enrollments.user_id')
                    ->where('enrollments.enrollment_status', 'pending')
                    ->where('courses.instructor_id', $userId)
                    ->orderBy('enrollments.created_at', 'DESC')
                    ->findAll();
            }
        } catch (\Throwable $e) {
            $pendingEnrollments = [];
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
            'pendingEnrollments' => $pendingEnrollments,
        ];

        return view('teacher', $data);
    }

    public function approveEnrollment($id)
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['teacher', 'instructor'], true)) {
            return redirect()->to('/login');
        }

        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->find((int) $id);
        
        if (!$enrollment) {
            return redirect()->to('/teacher/dashboard')->with('error', 'Enrollment not found.');
        }

        // Verify this enrollment is for a course taught by this teacher
        $db = Database::connect();
        $course = $db->table('courses')
            ->select('instructor_id')
            ->where('id', $enrollment['course_id'])
            ->get()->getRowArray();

        if (!$course || $course['instructor_id'] != $session->get('user_id')) {
            return redirect()->to('/teacher/dashboard')->with('error', 'You can only approve enrollments for your courses.');
        }

        $enrollmentModel->update((int) $id, ['enrollment_status' => 'approved']);
        
        return redirect()->to('/teacher/dashboard')->with('success', 'Enrollment approved successfully.');
    }

    public function rejectEnrollment($id)
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['teacher', 'instructor'], true)) {
            return redirect()->to('/login');
        }

        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->find((int) $id);
        
        if (!$enrollment) {
            return redirect()->to('/teacher/dashboard')->with('error', 'Enrollment not found.');
        }

        // Verify this enrollment is for a course taught by this teacher
        $db = Database::connect();
        $course = $db->table('courses')
            ->select('instructor_id')
            ->where('id', $enrollment['course_id'])
            ->get()->getRowArray();

        if (!$course || $course['instructor_id'] != $session->get('user_id')) {
            return redirect()->to('/teacher/dashboard')->with('error', 'You can only reject enrollments for your courses.');
        }

        $enrollmentModel->update((int) $id, ['enrollment_status' => 'rejected']);
        
        return redirect()->to('/teacher/dashboard')->with('success', 'Enrollment rejected successfully.');
    }

    public function myCourses()
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['teacher', 'instructor'], true)) {
            return redirect()->to('/login');
        }

        $userId = (int) ($session->get('user_id') ?? 0);
        $db = Database::connect();

        // Same courses list as in dashboard
        $courses = [];
        try {
            if ($db->tableExists('courses')) {
                $builder = $db->table('courses')
                    ->select('id, title, code, unit, course_level, department, created_at, instructor_id');
                if ($userId > 0) {
                    $builder->where('instructor_id', $userId);
                }
                $coursesRows = $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
                foreach ($coursesRows as $r) {
                    $courses[] = [
                        'id'         => $r['id'] ?? null,
                        'title'      => $r['title'] ?? '-',
                        'code'       => $r['code'] ?? '-',
                        'unit'       => $r['unit'] ?? '-',
                        'course_level' => $r['course_level'] ?? '-',
                        'department'   => $r['department'] ?? '-',
                        'created_at' => $r['created_at'] ?? '-',
                    ];
                }
            }
        } catch (\Throwable $e) {
            $courses = [];
        }

        return view('teacher/my_courses', [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'courses' => $courses,
        ]);
    }

    public function showCourse($id)
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['teacher', 'instructor'], true)) {
            return redirect()->to('/login');
        }

        $userId = (int) ($session->get('user_id') ?? 0);
        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->where('instructor_id', $userId)->find((int) $id);
        if (!$course) {
            return redirect()->to('/teacher/dashboard')->with('error', 'Course not found or you are not the instructor.');
        }

        return view('admin/course_view', [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'course' => $course,
        ]);
    }

    public function courseStudents($id)
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['teacher', 'instructor'], true)) {
            return redirect()->to('/login');
        }

        $userId = (int) ($session->get('user_id') ?? 0);
        $db = Database::connect();

        // Load course and ensure it belongs to this teacher
        $course = $db->table('courses')
            ->where('id', (int) $id)
            ->where('instructor_id', $userId)
            ->get()->getRowArray();

        if (!$course) {
            return redirect()->to('/teacher/dashboard')->with('error', 'Course not found or you are not the instructor.');
        }

        // Fetch enrolled students for this course
        $students = [];
        try {
            if ($db->tableExists('enrollments')) {
                $builder = $db->table('enrollments e')
                    ->select('u.id as user_id, u.name, u.email, u.created_at as enrolled_at')
                    ->join('users u', 'u.id = e.user_id', 'inner')
                    ->where('e.course_id', (int) $id)
                    ->where('u.role', 'student')
                    ->orderBy('u.name', 'ASC');

                $students = $builder->get()->getResultArray();
            }
        } catch (\Throwable $e) {
            $students = [];
        }

        return view('teacher/course_students', [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'course'   => $course,
            'students' => $students,
        ]);
    }

    public function editCourse($id)
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['teacher', 'instructor'], true)) {
            return redirect()->to('/auth/login');
        }

        $userId = (int) ($session->get('user_id') ?? 0);

        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->where('instructor_id', $userId)->find((int) $id);
        if (!$course) {
            return redirect()->to('/teacher/dashboard')->with('error', 'Course not found or you are not the instructor.');
        }

        $userModel = new UserModel();
        $teachers = $userModel->where('role', 'teacher')->orderBy('name', 'ASC')->findAll();

        return view('admin/course_edit', [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'course'   => $course,
            'teachers' => $teachers,
        ]);
    }

    public function deleteCourse($id)
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['teacher', 'instructor'], true)) {
            return redirect()->to('/auth/login');
        }

        if (!$this->request->is('post')) {
            return redirect()->to('/teacher/dashboard');
        }

        $userId = (int) ($session->get('user_id') ?? 0);
        $courseModel = new \App\Models\CourseModel();

        // Ensure course belongs to this teacher
        $course = $courseModel->where('instructor_id', $userId)->find((int) $id);
        if (!$course) {
            return redirect()->to('/teacher/dashboard')->with('error', 'Course not found or you are not the instructor.');
        }

        $courseModel->delete((int) $id);

        return redirect()->to('/teacher/dashboard')->with('success', 'Course deleted successfully.');
    }

    public function createCourse()
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['teacher', 'instructor'], true)) {
            return redirect()->to('/auth/login');
        }

        if (!$this->request->is('post')) {
            return redirect()->to('/teacher/dashboard');
        }

        $title = trim((string) $this->request->getPost('title'));
        $code  = trim((string) ($this->request->getPost('code') ?? ''));
        $unit  = (int) ($this->request->getPost('unit') ?? 0);
        $courseLevel = trim((string) ($this->request->getPost('course_level') ?? ''));
        $department = trim((string) ($this->request->getPost('department') ?? ''));
        $courseStartDate = trim((string) ($this->request->getPost('course_start_date') ?? ''));
        $courseEndDate = trim((string) ($this->request->getPost('course_end_date') ?? ''));
        $enrollmentStartDate = trim((string) ($this->request->getPost('enrollment_start_date') ?? ''));
        $enrollmentEndDate = trim((string) ($this->request->getPost('enrollment_end_date') ?? ''));
        $classSchedule = trim((string) ($this->request->getPost('class_schedule') ?? ''));
        $academicYear = trim((string) ($this->request->getPost('academic_year') ?? ''));
        $startDate = (string) ($this->request->getPost('start_date') ?? '');
        $endDate = (string) ($this->request->getPost('end_date') ?? '');
        $instructorId = (int) ($session->get('user_id') ?? 0);

        if ($title === '') {
            return redirect()->to('/teacher/dashboard')->with('error', 'Course title is required.');
        }

        $db = Database::connect();
        try {
            $data = [
                'title' => $title,
                'code'  => $code !== '' ? $code : null,
                'unit'  => $unit > 0 ? $unit : null,
                'course_level' => $courseLevel !== '' ? $courseLevel : null,
                'department' => $department !== '' ? $department : null,
                'course_start_date' => $courseStartDate !== '' ? $courseStartDate : null,
                'course_end_date' => $courseEndDate !== '' ? $courseEndDate : null,
                'enrollment_start_date' => $enrollmentStartDate !== '' ? $enrollmentStartDate : null,
                'enrollment_end_date' => $enrollmentEndDate !== '' ? $enrollmentEndDate : null,
                'class_schedule' => $classSchedule !== '' ? $classSchedule : null,
                'academic_year' => $academicYear !== '' ? $academicYear : null,
                'start_date' => $startDate !== '' ? $startDate : null,
                'end_date' => $endDate !== '' ? $endDate : null,
                'instructor_id' => $instructorId > 0 ? $instructorId : null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $db->table('courses')->insert($data);
            return redirect()->to('/teacher/dashboard')->with('success', 'Course created successfully.');
        } catch (\Throwable $e) {
            return redirect()->to('/teacher/dashboard')->with('error', 'Failed to create course.');
        }
    }
}
