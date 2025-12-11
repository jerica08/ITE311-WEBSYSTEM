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

        // Courses taught by this teacher (both draft and published courses)
        $courses = [];
        try {
            if ($db->tableExists('courses')) {
                $builder = $db->table('courses')
                    ->select('id, title, code, unit, course_level, department, program, class_schedule, created_at, instructor_id, status');
                if ($userId > 0) {
                    $builder->where('instructor_id', $userId);
                }
                // Show both draft and published courses (not archived)
                $builder->where('status !=', 'archived');
                $coursesRows = $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
                
                // Debug: Log the query and results
                log_message('info', 'TEACHER ID: ' . $userId);
                log_message('info', 'COURSES QUERY: ' . $db->getLastQuery());
                log_message('info', 'COURSES FOUND: ' . count($coursesRows));
                
                foreach ($coursesRows as $r) {
                    $courses[] = [
                        'id'         => $r['id'] ?? null,
                        'title'      => $r['title'] ?? '-',
                        'code'       => $r['code'] ?? '-',
                        'unit'       => $r['unit'] ?? '-',
                        'course_level' => $r['course_level'] ?? '-',
                        'department'   => $r['department'] ?? '-',
                        'program'     => $r['program'] ?? '-',
                        'class_schedule' => $r['class_schedule'] ?? '-',
                        'created_at' => $r['created_at'] ?? '-',
                        'status'     => $r['status'] ?? 'draft',
                    ];
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'COURSES ERROR: ' . $e->getMessage());
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
                
                // Debug: Log pending enrollments
                log_message('info', 'TEACHER ID: ' . $userId);
                log_message('info', 'PENDING ENROLLMENTS COUNT: ' . count($pendingEnrollments));
                foreach ($pendingEnrollments as $pe) {
                    log_message('info', 'PENDING: ' . json_encode($pe));
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'PENDING ENROLLMENTS ERROR: ' . $e->getMessage());
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

        // Fetch departments and programs for dropdowns
        $departments = [];
        $programs = [];
        try {
            if ($db->tableExists('departments')) {
                $departments = $db->table('departments')
                    ->orderBy('department_name', 'ASC')
                    ->get()
                    ->getResultArray();
            }
            if ($db->tableExists('programs')) {
                $programs = $db->table('programs')
                    ->select('program_name, department')
                    ->orderBy('program_name', 'ASC')
                    ->get()
                    ->getResultArray();
            }
        } catch (\Throwable $e) {
            $departments = [];
            $programs = [];
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
            'departments' => $departments,
            'programs'    => $programs,
        ];

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
            return redirect()->to('/teacher/dashboard');
        }

        $title = trim((string) $this->request->getPost('title'));
        $code  = trim((string) ($this->request->getPost('code') ?? ''));
        $unit  = (int) ($this->request->getPost('unit') ?? 0);
        $courseLevel = trim((string) ($this->request->getPost('course_level') ?? ''));
        $department = trim((string) ($this->request->getPost('department') ?? ''));
        $program = trim((string) ($this->request->getPost('program') ?? ''));
        $semester = trim((string) ($this->request->getPost('semester') ?? ''));
        $courseStartDate = trim((string) ($this->request->getPost('course_start_date') ?? ''));
        $courseEndDate = trim((string) ($this->request->getPost('course_end_date') ?? ''));
        $enrollmentStartDate = trim((string) ($this->request->getPost('enrollment_start_date') ?? ''));
        $enrollmentEndDate = trim((string) ($this->request->getPost('enrollment_end_date') ?? ''));
        
        // Process class schedule from days and time inputs
        $classDays = $this->request->getPost('class_days') ?? [];
        $startTime = trim((string) ($this->request->getPost('start_time') ?? ''));
        $endTime = trim((string) ($this->request->getPost('end_time') ?? ''));
        $classSchedule = '';
        if (!empty($classDays) && is_array($classDays)) {
            $days = implode(', ', $classDays);
            if ($startTime && $endTime) {
                $classSchedule = $days . ', ' . $startTime . ' - ' . $endTime;
            } else {
                $classSchedule = $days;
            }
        }
        
        $academicYear = trim((string) ($this->request->getPost('academic_year') ?? ''));
        
        // Use current teacher as instructor
        $instructorId = (int) ($session->get('user_id') ?? 0);

        if ($title === '') {
            return redirect()->to('/teacher/dashboard')->with('error', 'Course title is required.');
        }
        if ($instructorId <= 0) {
            return redirect()->to('/teacher/dashboard')->with('error', 'Invalid teacher session.');
        }

        $db = Database::connect();
        try {
            $data = [
                'title' => $title,
                'code'  => $code !== '' ? $code : null,
                'unit'  => $unit > 0 ? $unit : null,
                'course_level' => $courseLevel !== '' ? $courseLevel : null,
                'department' => $department !== '' ? $department : null,
                'program' => $program !== '' ? $program : null,
                'semester' => $semester !== '' ? $semester : null,
                'course_start_date' => $courseStartDate !== '' ? $courseStartDate : null,
                'course_end_date' => $courseEndDate !== '' ? $courseEndDate : null,
                'enrollment_start_date' => $enrollmentStartDate !== '' ? $enrollmentStartDate : null,
                'enrollment_end_date' => $enrollmentEndDate !== '' ? $enrollmentEndDate : null,
                'class_schedule' => $classSchedule !== '' ? $classSchedule : null,
                'academic_year' => $academicYear !== '' ? $academicYear : null,
                'instructor_id' => $instructorId,
                'status' => 'published',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            $db->table('courses')->insert($data);
            
            return redirect()->to('/teacher/dashboard')->with('success', 'Course created and published successfully! Students can now enroll.');
        } catch (\Throwable $e) {
            // Log the actual error for debugging
            log_message('error', 'Course creation failed: ' . $e->getMessage());
            log_message('error', 'Data being inserted: ' . json_encode($data));
            return redirect()->to('/teacher/dashboard')->with('error', 'Failed to create course: ' . $e->getMessage());
        }
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
        
        // Create notification for student
        try {
            // Get student and course details for notification
            $studentDetails = $db->table('users')
                ->select('name')
                ->where('id', $enrollment['user_id'])
                ->get()->getRowArray();
            
            $courseDetails = $db->table('courses')
                ->select('title')
                ->where('id', $enrollment['course_id'])
                ->get()->getRowArray();
            
            if ($studentDetails && $courseDetails) {
                $notificationModel = new \App\Models\NotificationModel();
                $notificationData = [
                    'user_id'    => (int) $enrollment['user_id'], // Student ID
                    'title'       => 'Enrollment Approved',
                    'message'     => 'Your enrollment in ' . $courseDetails['title'] . ' has been approved!',
                    'type'        => 'enrollment_approved',
                    'related_id'  => (int) $id,
                    'is_read'     => 0,
                    'created_at'  => date('Y-m-d H:i:s'),
                ];
                
                $notificationModel->insert($notificationData);
                log_message('info', 'APPROVAL NOTIFICATION: Sent to student ' . $enrollment['user_id'] . ' for course ' . $enrollment['course_id']);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Failed to create approval notification: ' . $e->getMessage());
        }
        
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
        
        // Create notification for student
        try {
            // Get student and course details for notification
            $studentDetails = $db->table('users')
                ->select('name')
                ->where('id', $enrollment['user_id'])
                ->get()->getRowArray();
            
            $courseDetails = $db->table('courses')
                ->select('title')
                ->where('id', $enrollment['course_id'])
                ->get()->getRowArray();
            
            if ($studentDetails && $courseDetails) {
                $notificationModel = new \App\Models\NotificationModel();
                $notificationData = [
                    'user_id'    => (int) $enrollment['user_id'], // Student ID
                    'title'       => 'Enrollment Rejected',
                    'message'     => 'Your enrollment in ' . $courseDetails['title'] . ' has been rejected.',
                    'type'        => 'enrollment_rejected',
                    'related_id'  => (int) $id,
                    'is_read'     => 0,
                    'created_at'  => date('Y-m-d H:i:s'),
                ];
                
                $notificationModel->insert($notificationData);
                log_message('info', 'REJECTION NOTIFICATION: Sent to student ' . $enrollment['user_id'] . ' for course ' . $enrollment['course_id']);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Failed to create rejection notification: ' . $e->getMessage());
        }
        
        return redirect()->to('/teacher/dashboard')->with('success', 'Enrollment rejected successfully.');
    }

    public function getNotifications()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $userId = (int) ($session->get('user_id') ?? 0);
        $notificationModel = new \App\Models\NotificationModel();
        
        log_message('info', 'Fetching notifications for teacher ID: ' . $userId);
        
        try {
            $notifications = $notificationModel
                ->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->limit(50)
                ->findAll();

            $unreadCount = $notificationModel
                ->where('user_id', $userId)
                ->where('is_read', 0)
                ->countAllResults();

            log_message('info', 'Found ' . count($notifications) . ' notifications, ' . $unreadCount . ' unread');

            return $this->response->setJSON([
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Notification fetch error: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Failed to fetch notifications']);
        }
    }

    public function markNotificationRead()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $notificationId = (int) ($this->request->getPost('notification_id') ?? 0);
        $userId = (int) ($session->get('user_id') ?? 0);

        if ($notificationId <= 0) {
            return $this->response->setJSON(['error' => 'Invalid notification ID']);
        }

        $notificationModel = new \App\Models\NotificationModel();
        
        try {
            $notification = $notificationModel->find($notificationId);
            if (!$notification || $notification['user_id'] != $userId) {
                return $this->response->setJSON(['error' => 'Notification not found']);
            }

            $notificationModel->update($notificationId, ['is_read' => 1]);
            return $this->response->setJSON(['success' => true]);
        } catch (\Throwable $e) {
            log_message('error', 'Mark notification read error: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Failed to mark notification as read']);
        }
    }

    /**
     * AJAX endpoint to fetch programs by department
     */
    public function getProgramsByDepartment()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || !in_array(strtolower((string) $session->get('role')), ['teacher', 'instructor'], true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $department = trim((string) $this->request->getGet('department'));
        
        if ($department === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Department is required']);
        }

        $db = Database::connect();
        $programs = [];
        
        try {
            if ($db->tableExists('programs')) {
                $programs = $db->table('programs')
                    ->select('program_name, program_code, department')
                    ->where('department', $department)
                    ->orderBy('program_name', 'ASC')
                    ->get()
                    ->getResultArray();
            }
            
            return $this->response->setJSON([
                'success' => true,
                'programs' => $programs
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Error fetching programs by department: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to fetch programs']);
        }
    }

    public function markAllNotificationsRead()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $userId = (int) ($session->get('user_id') ?? 0);
        $notificationModel = new \App\Models\NotificationModel();
        
        try {
            $notificationModel
                ->where('user_id', $userId)
                ->where('is_read', 0)
                ->set(['is_read' => 1])
                ->update();

            return $this->response->setJSON(['success' => true]);
        } catch (\Throwable $e) {
            log_message('error', 'Mark all notifications read error: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Failed to mark all notifications as read']);
        }
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

        $searchTerm = trim((string) $this->request->getGet('q'));
        $levelFilter = trim((string) $this->request->getGet('level'));

        $courses = [];
        $availableLevels = [];

        try {
            if ($db->tableExists('courses')) {
                // Build base query for teacher's courses
                $builder = $db->table('courses')
                    ->select('id, title, code, unit, course_level, department, program, class_schedule, created_at, instructor_id, status');

                if ($userId > 0) {
                    $builder->where('instructor_id', $userId);
                }
                
                // Show both draft and published courses (not archived)
                $builder->where('status !=', 'archived');

                if ($searchTerm !== '') {
                    $builder->groupStart()
                        ->like('title', $searchTerm)
                        ->orLike('code', $searchTerm)
                    ->groupEnd();
                }

                if ($levelFilter !== '' && $levelFilter !== 'all') {
                    $builder->where('course_level', $levelFilter);
                }

                $coursesRows = $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
                
                // Debug: Log the query and results
                log_message('info', 'MY COURSES - TEACHER ID: ' . $userId);
                log_message('info', 'MY COURSES - QUERY: ' . $db->getLastQuery());
                log_message('info', 'MY COURSES - COURSES FOUND: ' . count($coursesRows));

                foreach ($coursesRows as $r) {
                    $courses[] = [
                        'id'            => $r['id'] ?? null,
                        'title'         => $r['title'] ?? '-',
                        'code'          => $r['code'] ?? '-',
                        'unit'          => $r['unit'] ?? '-',
                        'course_level'  => $r['course_level'] ?? '-',
                        'department'    => $r['department'] ?? '-',
                        'program'       => $r['program'] ?? '-',
                        'class_schedule'=> $r['class_schedule'] ?? '-',
                        'created_at'    => $r['created_at'] ?? '-',
                        'status'        => $r['status'] ?? 'draft',
                    ];
                }
                
                // Debug: Log after building courses array
                log_message('info', 'MY COURSES - AFTER BUILDING ARRAY: ' . count($courses));

                // Fetch available course levels for filter dropdown (isolated try-catch)
                $availableLevels = [];
                try {
                    $levelsQuery = $db->table('courses')
                        ->select('DISTINCT course_level')
                        ->where('instructor_id', $userId)
                        ->where('course_level IS NOT NULL')
                        ->where('course_level !=', '')
                        ->orderBy('course_level', 'ASC')
                        ->get()
                        ->getResultArray();

                    foreach ($levelsQuery as $lvl) {
                        $val = trim((string) ($lvl['course_level'] ?? ''));
                        if ($val !== '') {
                            $availableLevels[] = $val;
                        }
                    }
                } catch (\Throwable $levelsException) {
                    log_message('error', 'LEVELS QUERY EXCEPTION: ' . $levelsException->getMessage());
                    // Don't reset courses array - just keep levels empty
                }
            }
        } catch (\Throwable $e) {
            $courses = [];
            $availableLevels = [];
        }

        // Debug: Log what's being passed to view
        log_message('info', 'VIEW DATA - Courses count: ' . count($courses));
        log_message('info', 'VIEW DATA - First course: ' . json_encode($courses[0] ?? 'No courses'));
        log_message('info', 'VIEW DATA - Available levels: ' . json_encode($availableLevels));
        
        // Force debug - write to a file
        file_put_contents(WRITEPATH . 'debug_courses.txt', "Courses count: " . count($courses) . "\n");
        file_put_contents(WRITEPATH . 'debug_courses.txt', "First course: " . json_encode($courses[0] ?? 'No courses') . "\n", FILE_APPEND);
        
        return view('teacher/my_courses', [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'courses'        => $courses,
            'filters'        => [
                'q'     => $searchTerm,
                'level' => $levelFilter,
            ],
            'courseLevels'   => $availableLevels,
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

    /**
     * Show assignments page
     */
    public function assignments()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || !in_array(strtolower((string) $session->get('role')), ['teacher', 'instructor'], true)) {
            return redirect()->to('/login');
        }

        $userId = (int) ($session->get('user_id') ?? 0);
        $db = Database::connect();

        // Get courses taught by this teacher
        $courses = [];
        try {
            if ($db->tableExists('courses')) {
                $courses = $db->table('courses')
                    ->select('id, title, code')
                    ->where('instructor_id', $userId)
                    ->where('status !=', 'archived')
                    ->orderBy('title', 'ASC')
                    ->get()->getResultArray();
            }
        } catch (\Throwable $e) {
            $courses = [];
        }

        // Get existing assignments
        $assignments = [];
        try {
            if ($db->tableExists('assignments')) {
                $assignments = $db->table('assignments')
                    ->select('assignments.*, courses.title as course_title, courses.code as course_code')
                    ->join('courses', 'courses.id = assignments.course_id')
                    ->where('assignments.instructor_id', $userId)
                    ->orderBy('assignments.created_at', 'DESC')
                    ->get()->getResultArray();
            }
        } catch (\Throwable $e) {
            $assignments = [];
        }

        return view('teacher/assignments', [
            'user' => $session->get(),
            'courses' => $courses,
            'assignments' => $assignments
        ]);
    }

    /**
     * Create new assignment
     */
    public function createAssignment()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || !in_array(strtolower((string) $session->get('role')), ['teacher', 'instructor'], true)) {
            return redirect()->to('/login');
        }

        if ($this->request->getMethod() === 'POST') {
            $courseId = $this->request->getPost('course_id');
            $title = $this->request->getPost('title');
            $description = $this->request->getPost('description');
            $dueDate = $this->request->getPost('due_date');
            $maxScore = $this->request->getPost('max_score');

            // Validation
            if (empty($courseId) || empty($title) || empty($description) || empty($dueDate)) {
                return redirect()->back()->with('error', 'Please fill in all required fields.');
            }

            $db = Database::connect();
            try {
                // Handle file upload
                $attachmentFile = null;
                $attachmentPath = null;
                
                if ($file = $this->request->getFile('attachment')) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        // Validate file size (10MB max)
                        if ($file->getSize() > 10485760) {
                            return redirect()->to('/teacher/assignments')->with('error', 'File size must be less than 10MB.');
                        }
                        
                        // Validate file type
                        $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];
                        $fileExt = $file->getExtension();
                        
                        if (!in_array(strtolower($fileExt), $allowedTypes)) {
                            return redirect()->to('/teacher/assignments')->with('error', 'Invalid file type. Allowed types: PDF, DOC, DOCX, TXT, JPG, JPEG, PNG.');
                        }
                        
                        // Generate unique filename
                        $newName = $file->getRandomName();
                        
                        // Create uploads directory if it doesn't exist
                        $uploadPath = WRITEPATH . 'uploads/assignments/';
                        if (!is_dir($uploadPath)) {
                            mkdir($uploadPath, 0755, true);
                        }
                        
                        // Move the file
                        if ($file->move($uploadPath, $newName)) {
                            $attachmentFile = $file->getName();
                            $attachmentPath = 'uploads/assignments/' . $newName;
                        }
                    }
                }
                
                $data = [
                    'course_id' => (int) $courseId,
                    'instructor_id' => (int) $session->get('user_id'),
                    'title' => $title,
                    'description' => $description,
                    'due_date' => $dueDate,
                    'max_score' => $maxScore ?? 100,
                    'status' => 'active',
                    'attachment_file' => $attachmentFile,
                    'attachment_path' => $attachmentPath,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                
                $db->table('assignments')->insert($data);
                $assignmentId = $db->insertID();
                
                // Get course details to find enrolled students
                log_message('debug', 'ASSIGNMENT DEBUG: Getting course details for course_id: ' . $courseId);
                $courseDetails = $db->table('courses')
                    ->select('title')
                    ->where('id', $courseId)
                    ->get()->getRowArray();
                
                log_message('debug', 'ASSIGNMENT DEBUG: Course details: ' . json_encode($courseDetails));
                
                // Get all students enrolled in this course
                log_message('debug', 'ASSIGNMENT DEBUG: Getting enrolled students for course_id: ' . $courseId);
                $enrolledStudents = $db->table('enrollments')
                    ->select('user_id')
                    ->where('course_id', $courseId)
                    ->where('enrollment_status', 'approved')
                    ->orWhere('enrollment_status', 'pending') // Temporarily include pending for testing
                    ->get()->getResultArray();
                
                log_message('debug', 'ASSIGNMENT DEBUG: Enrolled students found: ' . json_encode($enrolledStudents));
                
                // Create notifications for all enrolled students
                if ($courseDetails && !empty($enrolledStudents)) {
                    log_message('debug', 'ASSIGNMENT DEBUG: Creating notifications...');
                    $notificationModel = new \App\Models\NotificationModel();
                    $courseTitle = $courseDetails['title'];
                    
                    foreach ($enrolledStudents as $student) {
                        $notificationData = [
                            'user_id'    => (int) $student['user_id'],
                            'title'       => 'New Assignment Posted',
                            'message'     => 'A new assignment has been posted for your course: ' . $courseTitle . ' - ' . $title,
                            'type'        => 'new_assignment',
                            'related_id'  => $assignmentId,
                            'is_read'     => 0,
                            'created_at'  => date('Y-m-d H:i:s'),
                        ];
                        
                        log_message('debug', 'ASSIGNMENT DEBUG: Inserting notification for user_id: ' . $student['user_id'] . ' - Data: ' . json_encode($notificationData));
                        $notificationModel->insert($notificationData);
                    }
                    
                    log_message('info', 'ASSIGNMENT NOTIFICATIONS: Sent to ' . count($enrolledStudents) . ' students for course ' . $courseId);
                } else {
                    log_message('warning', 'ASSIGNMENT DEBUG: No notifications created - Course details: ' . ($courseDetails ? 'found' : 'not found') . ', Enrolled students: ' . count($enrolledStudents));
                }
                
                return redirect()->to('/teacher/assignments')->with('success', 'Assignment created successfully and students notified.');
            } catch (\Throwable $e) {
                return redirect()->to('/teacher/assignments')->with('error', 'Failed to create assignment.');
            }
        }

        return redirect()->to('/teacher/assignments');
    }

    /**
     * View submissions for an assignment
     */
    public function viewSubmissions($assignmentId)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || !in_array(strtolower((string) $session->get('role')), ['teacher', 'instructor'], true)) {
            return redirect()->to('/login');
        }

        $assignmentId = (int) $assignmentId;
        $userId = (int) ($session->get('user_id') ?? 0);
        $db = Database::connect();

        try {
            // Get assignment details and verify ownership
            $assignment = $db->table('assignments')
                ->select('assignments.*, courses.title as course_title, courses.code as course_code')
                ->join('courses', 'courses.id = assignments.course_id')
                ->where('assignments.id', $assignmentId)
                ->where('assignments.instructor_id', $userId)
                ->get()
                ->getRow();

            if (!$assignment) {
                return redirect()->to('/teacher/assignments')->with('error', 'Assignment not found or you do not have permission to view it.');
            }

            // Get submissions for this assignment
            $submissions = [];
            try {
                $submissions = $db->table('submissions')
                    ->select('submissions.*, users.name as student_name, users.email as student_email')
                    ->join('users', 'users.id = submissions.user_id')
                    ->where('submissions.assignment_id', $assignmentId)
                    ->orderBy('submissions.created_at', 'DESC')
                    ->get()
                    ->getResultArray();
            } catch (\Throwable $e) {
                $submissions = [];
            }

            return view('teacher/view_submissions', [
                'user' => [
                    'name'  => $session->get('name'),
                    'email' => $session->get('email'),
                    'role'  => $session->get('role'),
                ],
                'assignment' => $assignment,
                'submissions' => $submissions
            ]);

        } catch (\Throwable $e) {
            return redirect()->to('/teacher/assignments')->with('error', 'Failed to load assignment submissions.');
        }
    }

    /**
     * Download student submission attachment
     */
    public function downloadSubmissionAttachment($submissionId)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || !in_array(strtolower((string) $session->get('role')), ['teacher', 'instructor'], true)) {
            return redirect()->to('/login');
        }

        $submissionId = (int) $submissionId;
        $userId = (int) $session->get('user_id');
        $db = Database::connect();

        try {
            log_message('debug', 'DOWNLOAD DEBUG: Looking for submission ID: ' . $submissionId);
            
            $submission = $db->table('submissions')
                ->select('submissions.attachment, submissions.original_filename, submissions.user_id as student_id, assignments.course_id, assignments.title as assignment_title, courses.title as course_title, users.name as student_name')
                ->join('assignments', 'assignments.id = submissions.assignment_id')
                ->join('courses', 'courses.id = assignments.course_id')
                ->join('users', 'users.id = submissions.user_id')
                ->where('submissions.id', $submissionId)
                ->where('assignments.instructor_id', $userId) // Verify teacher owns this assignment
                ->get()
                ->getRow();

            log_message('debug', 'DOWNLOAD DEBUG: Submission data: ' . json_encode($submission));

            if (!$submission || empty($submission->attachment)) {
                log_message('error', 'DOWNLOAD DEBUG: No submission or attachment found');
                return redirect()->back()->with('error', 'File not found.');
            }

            $filePath = WRITEPATH . 'uploads/' . $submission->attachment;
            log_message('debug', 'DOWNLOAD DEBUG: File path: ' . $filePath);
            log_message('debug', 'DOWNLOAD DEBUG: File exists: ' . (file_exists($filePath) ? 'YES' : 'NO'));
            
            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', 'File not found on server.');
            }

            // Use original filename if available, otherwise use the stored filename
            $downloadFilename = !empty($submission->original_filename) ? $submission->original_filename : $submission->attachment;
            log_message('debug', 'DOWNLOAD DEBUG: Download filename: ' . $downloadFilename);

            // Set headers for file download
            return $this->response
                ->setHeader('Content-Type', 'application/octet-stream')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $downloadFilename . '"')
                ->setHeader('Content-Length', filesize($filePath))
                ->setHeader('Cache-Control', 'no-cache, must-revalidate')
                ->setHeader('Pragma', 'no-cache')
                ->download($filePath, null, true);

        } catch (\Throwable $e) {
            log_message('error', 'Download submission error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to download file.');
        }
    }

    /**
     * Download assignment attachment
     */
    public function downloadAssignmentAttachment($assignmentId)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || !in_array(strtolower((string) $session->get('role')), ['teacher', 'instructor', 'student'], true)) {
            return redirect()->to('/login');
        }

        $assignmentId = (int) $assignmentId;
        $db = Database::connect();

        try {
            $assignment = $db->table('assignments')
                ->select('assignments.attachment_file, assignments.attachment_path, assignments.course_id, courses.title as course_title')
                ->join('courses', 'courses.id = assignments.course_id')
                ->where('assignments.id', $assignmentId)
                ->get()
                ->getRow();

            if (!$assignment || empty($assignment['attachment_path'])) {
                return redirect()->back()->with('error', 'File not found.');
            }

            $filePath = WRITEPATH . $assignment['attachment_path'];
            
            if (!file_exists($filePath)) {
                return redirect()->back()->with('error', 'File not found on server.');
            }

            return $this->response
                ->setHeader('Content-Type', 'application/octet-stream')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $assignment['attachment_file'] . '"')
                ->setHeader('Content-Length', filesize($filePath))
                ->setHeader('Cache-Control', 'no-cache, must-revalidate')
                ->setHeader('Pragma', 'no-cache')
                ->download($filePath, null, true);

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to download file.');
        }
    }

    /**
     * Save grade via AJAX
     */
    public function saveGrade()
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['teacher', 'instructor'], true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $submissionId = $this->request->getPost('submission_id');
        $grade = $this->request->getPost('grade');
        $userId = (int) ($session->get('user_id') ?? 0);
        $db = Database::connect();

        // Validate input
        if (!$submissionId || !is_numeric($grade) || $grade < 0 || $grade > 100) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid grade']);
        }

        try {
            // Verify submission belongs to teacher's assignment and get details for notification
            $submission = $db->table('submissions')
                ->select('submissions.id, submissions.user_id as student_id, assignments.instructor_id, assignments.title as assignment_title, courses.title as course_title, users.name as student_name')
                ->join('assignments', 'assignments.id = submissions.assignment_id')
                ->join('courses', 'courses.id = assignments.course_id')
                ->join('users', 'users.id = submissions.user_id')
                ->where('submissions.id', $submissionId)
                ->where('assignments.instructor_id', $userId)
                ->get()
                ->getRow();

            if (!$submission) {
                return $this->response->setJSON(['success' => false, 'message' => 'Submission not found']);
            }

            // Update the grade
            $updateData = [
                'grade' => $grade,
                'graded_by' => $userId,
                'graded_at' => date('Y-m-d H:i:s'),
                'status' => 'graded',
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $db->table('submissions')->where('id', $submissionId)->update($updateData);

            // Create notification for student
            try {
                $notificationModel = new \App\Models\NotificationModel();
                $notificationData = [
                    'user_id'    => (int) $submission->student_id,
                    'title'       => 'Assignment Graded',
                    'message'     => 'Your assignment "' . $submission->assignment_title . '" for ' . $submission->course_title . ' has been graded. Score: ' . $grade . '/100',
                    'type'        => 'assignment_graded',
                    'related_id'  => (int) $submissionId,
                    'is_read'     => 0,
                    'created_at'  => date('Y-m-d H:i:s'),
                ];
                
                $notificationModel->insert($notificationData);
                log_message('info', 'GRADE NOTIFICATION: Sent to student ' . $submission->student_id . ' for submission ' . $submissionId);
            } catch (\Throwable $e) {
                log_message('error', 'Failed to create grade notification: ' . $e->getMessage());
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Grade saved successfully and student notified']);

        } catch (\Throwable $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }

    /**
     * Edit assignment - show edit form
     */
    public function editAssignment($id)
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['teacher', 'instructor'], true)) {
            return redirect()->to('/login');
        }

        $assignmentId = (int) $id;
        $userId = (int) ($session->get('user_id') ?? 0);
        $db = Database::connect();

        try {
            // Get assignment details and verify ownership
            $assignment = $db->table('assignments')
                ->select('assignments.*, courses.title as course_title, courses.code as course_code')
                ->join('courses', 'courses.id = assignments.course_id')
                ->where('assignments.id', $assignmentId)
                ->where('assignments.instructor_id', $userId)
                ->get()
                ->getRow();

            if (!$assignment) {
                return redirect()->to('/teacher/assignments')->with('error', 'Assignment not found or you do not have permission to edit it.');
            }

            // Get courses for dropdown
            $courses = [];
            try {
                $courses = $db->table('courses')
                    ->select('id, title, code')
                    ->where('instructor_id', $userId)
                    ->where('status !=', 'archived')
                    ->orderBy('title', 'ASC')
                    ->get()
                    ->getResultArray();
            } catch (\Throwable $e) {
                $courses = [];
            }

            return view('teacher/edit_assignment', [
                'user' => [
                    'name'  => $session->get('name'),
                    'email' => $session->get('email'),
                    'role'  => $session->get('role'),
                ],
                'assignment' => $assignment,
                'courses' => $courses
            ]);

        } catch (\Throwable $e) {
            return redirect()->to('/teacher/assignments')->with('error', 'Failed to load assignment for editing.');
        }
    }

    /**
     * Update assignment - process form submission
     */
    public function updateAssignment($id)
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['teacher', 'instructor'], true)) {
            return redirect()->to('/login');
        }

        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/teacher/assignments');
        }

        $assignmentId = (int) $id;
        $userId = (int) ($session->get('user_id') ?? 0);
        $db = Database::connect();

        // Get form data
        $courseId = $this->request->getPost('course_id');
        $title = $this->request->getPost('title');
        $description = $this->request->getPost('description');
        $dueDate = $this->request->getPost('due_date');
        $maxScore = $this->request->getPost('max_score');

        // Validation
        if (empty($courseId) || empty($title) || empty($description) || empty($dueDate)) {
            return redirect()->back()->with('error', 'Please fill in all required fields.');
        }

        try {
            // Verify assignment ownership
            $assignment = $db->table('assignments')
                ->where('id', $assignmentId)
                ->where('instructor_id', $userId)
                ->get()
                ->getRow();

            if (!$assignment) {
                return redirect()->to('/teacher/assignments')->with('error', 'Assignment not found or you do not have permission to edit it.');
            }

            $updateData = [
                'course_id' => (int) $courseId,
                'title' => $title,
                'description' => $description,
                'due_date' => $dueDate,
                'max_score' => $maxScore ?? 100,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Handle file upload if provided
            $file = $this->request->getFile('attachment');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Validate file size (10MB max)
                if ($file->getSize() > 10485760) {
                    return redirect()->back()->with('error', 'File size must be less than 10MB.');
                }

                // Validate file type
                $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];
                $fileExt = $file->getExtension();
                
                if (!in_array(strtolower($fileExt), $allowedTypes)) {
                    return redirect()->back()->with('error', 'Invalid file type. Allowed types: PDF, DOC, DOCX, TXT, JPG, JPEG, PNG.');
                }

                // Generate unique filename
                $newName = $file->getRandomName();
                
                // Create uploads directory if it doesn't exist
                $uploadPath = WRITEPATH . 'uploads/assignments/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Move the file
                if ($file->move($uploadPath, $newName)) {
                    $updateData['attachment_file'] = $file->getName();
                    $updateData['attachment_path'] = 'uploads/assignments/' . $newName;
                }
            }

            $db->table('assignments')->where('id', $assignmentId)->update($updateData);

            return redirect()->to('/teacher/assignments')->with('success', 'Assignment updated successfully.');

        } catch (\Throwable $e) {
            return redirect()->to('/teacher/assignments')->with('error', 'Failed to update assignment.');
        }
    }

    /**
     * Delete assignment
     */
    public function deleteAssignment($id)
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['teacher', 'instructor'], true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $assignmentId = (int) $id;
        $userId = (int) ($session->get('user_id') ?? 0);
        $db = Database::connect();

        try {
            // Verify assignment ownership
            $assignment = $db->table('assignments')
                ->where('id', $assignmentId)
                ->where('instructor_id', $userId)
                ->get()
                ->getRow();

            if (!$assignment) {
                return $this->response->setJSON(['success' => false, 'message' => 'Assignment not found or you do not have permission to delete it.']);
            }

            // Check if there are any submissions for this assignment
            $submissionsCount = $db->table('submissions')
                ->where('assignment_id', $assignmentId)
                ->countAllResults();

            if ($submissionsCount > 0) {
                return $this->response->setJSON(['success' => false, 'message' => 'Cannot delete assignment with submissions. Please delete submissions first.']);
            }

            // Delete the assignment
            $db->table('assignments')->where('id', $assignmentId)->delete();

            return $this->response->setJSON(['success' => true, 'message' => 'Assignment deleted successfully.']);

        } catch (\Throwable $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete assignment.']);
        }
    }
}
