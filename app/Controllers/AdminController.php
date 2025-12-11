<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\CourseModel;
use App\Models\DepartmentModel;
use App\Models\ProgramModel;
use App\Models\ActivityLogModel;
use Config\Database;

class AdminController extends BaseController
{
    public function dashboard()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $userModel    = new UserModel();
        $studentModel = new StudentModel();
        $activityLogModel = new ActivityLogModel();
        $db = Database::connect();

        // Totals
        $totalUsers = $userModel->countAllResults();
        $totalCourses = 0;
        try {
            if ($db->tableExists('courses')) {
                $totalCourses = (int) $db->table('courses')->countAll();
            }
        } catch (\Throwable $e) {
            $totalCourses = 0;
        }

        // Recent activities from activity log
        $recentActivities = [];
        try {
            if ($db->tableExists('activity_logs')) {
                $recentActivities = $activityLogModel->getRecentActivities(10);
            }
        } catch (\Throwable $e) {
            // Fallback to recent users if activity log table doesn't exist yet
            $recentActivities = $userModel
                ->orderBy('created_at', 'DESC')
                ->select('name,email,role,created_at')
                ->limit(10)
                ->find();
        }

        // User initials for badge on the right
        $name = (string) $session->get('name');
        $initials = '';
        if ($name !== '') {
            $parts = preg_split('/\s+/', trim($name));
            $first = strtoupper(substr($parts[0] ?? '', 0, 1));
            $last  = strtoupper(substr($parts[count($parts)-1] ?? '', 0, 1));
            $initials = $first . $last;
        }

        $data = [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'adminUsers'    => $userModel->where('role', 'admin')->findAll(),
            'totalUsers'    => $totalUsers,
            'totalCourses'  => $totalCourses,
            'recentActivities' => $recentActivities,
            'userInitials'  => $initials,
        ];

        return view('admin/admin', $data);
    }

    public function users()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $userModel = new UserModel();
        
        // Get search parameter
        $userSearch = $this->request->getGet('user_search');
        
        // Build query with search filter
        $query = $userModel->orderBy('created_at', 'DESC');
        
        if ($userSearch) {
            $query->groupStart()
                  ->like('name', $userSearch)
                  ->orLike('email', $userSearch)
                  ->orLike('role', $userSearch)
                  ->groupEnd();
        }
        
        $users = $query->findAll();

        return view('admin/users', [
            'user'  => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'users' => $users,
        ]);
    }

    public function createUserForm()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        return view('admin/user_create', [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
        ]);
    }

    public function courses()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $db = Database::connect();
        $courses = [];
        $departments = [];
        $programs = [];
        
        // Get search parameters
        $courseSearch = $this->request->getGet('course_search');
        $departmentSearch = $this->request->getGet('department_search');
        $programSearch = $this->request->getGet('program_search');
        
        try {
            // Fetch courses with search filter
            if ($db->tableExists('courses')) {
                $query = $db->table('courses')
                    ->select('courses.id, courses.title, courses.code, courses.unit, courses.course_level, courses.department, courses.course_start_date, courses.course_end_date, courses.enrollment_start_date, courses.enrollment_end_date, courses.class_schedule, courses.academic_year, courses.instructor_id, courses.status, courses.created_at, users.name as instructor_name')
                    ->join('users', 'users.id = courses.instructor_id', 'left');
                
                if ($courseSearch) {
                    $query->groupStart()
                          ->like('courses.title', $courseSearch)
                          ->orLike('courses.code', $courseSearch)
                          ->orLike('courses.department', $courseSearch)
                          ->orLike('courses.course_level', $courseSearch)
                          ->orLike('users.name', $courseSearch)
                          ->groupEnd();
                }
                
                $courses = $query->orderBy('courses.created_at', 'DESC')
                                 ->get()
                                 ->getResultArray();
            }
            
            // Fetch departments with search filter
            if ($db->tableExists('departments')) {
                $query = $db->table('departments');
                
                if ($departmentSearch) {
                    $query->groupStart()
                          ->like('department_name', $departmentSearch)
                          ->orLike('department_code', $departmentSearch)
                          ->orLike('description', $departmentSearch)
                          ->groupEnd();
                }
                
                $departments = $query->orderBy('department_name', 'ASC')
                                    ->get()
                                    ->getResultArray();
            }
            
            // Fetch programs with search filter
            if ($db->tableExists('programs')) {
                $query = $db->table('programs');
                
                if ($programSearch) {
                    $query->groupStart()
                          ->like('program_name', $programSearch)
                          ->orLike('program_code', $programSearch)
                          ->orLike('department', $programSearch)
                          ->orLike('description', $programSearch)
                          ->groupEnd();
                }
                
                $programs = $query->orderBy('program_name', 'ASC')
                                 ->get()
                                 ->getResultArray();
            }
        } catch (\Throwable $e) {
            $courses = [];
            $departments = [];
            $programs = [];
        }

        $userModel = new UserModel();
        $teachers = $userModel->where('role', 'teacher')->orderBy('name', 'ASC')->findAll();

        return view('admin/courses', [
            'user'     => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'courses'  => $courses,
            'teachers' => $teachers,
            'departments' => $departments,
            'programs' => $programs,
        ]);
    }

    public function createCourse()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        if (!$this->request->is('post')) {
            return redirect()->to('/admin/courses');
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
        $instructorId = (int) ($this->request->getPost('instructor_id') ?? 0);

        if ($title === '') {
            return redirect()->to('/admin/courses')->with('error', 'Course title is required.');
        }
        if ($instructorId <= 0) {
            return redirect()->to('/admin/courses')->with('error', 'Instructor ID is required and must be a valid user ID.');
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
                'status' => 'published', // Admin courses are auto-published
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            $db->table('courses')->insert($data);
            
            // Log course creation
            $this->logActivity('Course Created', 'Created and published new course: ' . $title . ' (' . $code . ')');
            
            return redirect()->to('/admin/courses')->with('success', 'Course created and published successfully.');
        } catch (\Throwable $e) {
            // Log the actual error for debugging
            log_message('error', 'Course creation failed: ' . $e->getMessage());
            log_message('error', 'Data being inserted: ' . json_encode($data));
            return redirect()->to('/admin/courses')->with('error', 'Failed to create course: ' . $e->getMessage());
        }
    }

    public function showCourse($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find((int) $id);
        if (!$course) {
            return redirect()->to('/admin/courses')->with('error', 'Course not found.');
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
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $db = Database::connect();

        // Load course
        $course = $db->table('courses')->where('id', (int) $id)->get()->getRowArray();
        if (!$course) {
            return redirect()->to('/admin/courses')->with('error', 'Course not found.');
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

        return view('admin/course_students', [
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
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find((int) $id);
        if (!$course) {
            return redirect()->to('/admin/courses')->with('error', 'Course not found.');
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

    public function updateCourse($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        if (!$this->request->is('post')) {
            return redirect()->to('/admin/courses');
        }

        $courseModel = new CourseModel();

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
        $instructorId = (int) ($this->request->getPost('instructor_id') ?? 0);

        if ($title === '') {
            return redirect()->back()->withInput()->with('error', 'Course title is required.');
        }

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
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $courseModel = new CourseModel();
        $courseModel->update((int) $id, $data);
        
        // Log course update
        $this->logActivity('Course Updated', 'Updated course: ' . $title . ' (' . $code . ')');
        
        return redirect()->to('/admin/courses')->with('success', 'Course updated successfully.');
    }

    public function approveCourse($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $db = Database::connect();
        $db->table('courses')->where('id', (int) $id)->update(['status' => 'published']);
        
        return redirect()->to('/admin/courses')->with('success', 'Course approved and published successfully.');
    }

    public function rejectCourse($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $db = Database::connect();
        $db->table('courses')->where('id', (int) $id)->update(['status' => 'archived']);
        
        return redirect()->to('/admin/courses')->with('success', 'Course rejected and archived.');
    }

    public function pendingEnrollments()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $enrollmentModel = new EnrollmentModel();
        $pendingEnrollments = $enrollmentModel
            ->select('enrollments.*, courses.title as course_title, courses.code as course_code, users.name as student_name, users.email as student_email')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->join('users', 'users.id = enrollments.user_id')
            ->where('enrollments.enrollment_status', 'pending')
            ->orderBy('enrollments.created_at', 'DESC')
            ->findAll();

        return view('admin/pending_enrollments', [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'pendingEnrollments' => $pendingEnrollments,
        ]);
    }

    public function approveEnrollment($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $enrollmentModel = new EnrollmentModel();
        $enrollmentModel->update((int) $id, ['enrollment_status' => 'approved']);
        
        return redirect()->to('/admin/pending-enrollments')->with('success', 'Enrollment approved successfully.');
    }

    public function rejectEnrollment($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $enrollmentModel = new EnrollmentModel();
        $enrollmentModel->update((int) $id, ['enrollment_status' => 'rejected']);
        
        return redirect()->to('/admin/pending-enrollments')->with('success', 'Enrollment rejected successfully.');
    }

    public function deleteCourse($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        if (!$this->request->is('post')) {
            return redirect()->to('/admin/courses');
        }

        $courseModel = new CourseModel();
        $courseModel->delete((int) $id);

        return redirect()->to('/admin/courses')->with('success', 'Course deleted successfully.');
    }

    public function editUser($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $userModel = new UserModel();
        $userToEdit = $userModel->find((int) $id);
        if (!$userToEdit) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }

        return view('admin/user_edit', [
            'user'       => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'userToEdit' => $userToEdit,
        ]);
    }

    // Department Management Methods
    public function createDepartment()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }
            return redirect()->to('/auth/login');
        }

        if (!$this->request->is('post')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
            }
            return redirect()->to('/admin/courses');
        }

        $departmentName = trim((string) $this->request->getPost('department_name'));
        $departmentCode = trim((string) ($this->request->getPost('department_code') ?? ''));
        $description = trim((string) ($this->request->getPost('description') ?? ''));

        if ($departmentName === '') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Department name is required.']);
            }
            return redirect()->to('/admin/courses')->with('error', 'Department name is required.');
        }

        $departmentModel = new DepartmentModel();

        try {
            $data = [
                'department_name' => $departmentName,
                'department_code' => $departmentCode !== '' ? $departmentCode : null,
                'description' => $description !== '' ? $description : null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $departmentModel->insert($data);
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => true, 'message' => 'Department created successfully.']);
            }
            return redirect()->to('/admin/courses')->with('success', 'Department created successfully.');
        } catch (\Throwable $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to create department.']);
            }
            return redirect()->to('/admin/courses')->with('error', 'Failed to create department.');
        }
    }

    public function deleteDepartment($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        if (!$this->request->is('post')) {
            return redirect()->to('/admin/courses');
        }

        $departmentModel = new DepartmentModel();

        try {
            $departmentModel->delete((int) $id);
            return redirect()->to('/admin/courses')->with('success', 'Department deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->to('/admin/courses')->with('error', 'Failed to delete department.');
        }
    }

    public function showDepartment($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $departmentModel = new DepartmentModel();
        $department = $departmentModel->find((int) $id);
        if (!$department) {
            return redirect()->to('/admin/courses')->with('error', 'Department not found.');
        }

        return view('admin/department_view', [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'department' => $department,
        ]);
    }

    public function editDepartment($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $departmentModel = new DepartmentModel();
        $department = $departmentModel->find((int) $id);
        if (!$department) {
            return redirect()->to('/admin/courses')->with('error', 'Department not found.');
        }

        return view('admin/department_edit', [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'department' => $department,
        ]);
    }

    public function updateDepartment($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        if (!$this->request->is('post')) {
            return redirect()->to('/admin/departments/' . (int) $id . '/edit');
        }

        $departmentModel = new DepartmentModel();
        $department = $departmentModel->find((int) $id);
        if (!$department) {
            return redirect()->to('/admin/courses')->with('error', 'Department not found.');
        }

        $departmentName = trim((string) $this->request->getPost('department_name'));
        $departmentCode = trim((string) ($this->request->getPost('department_code') ?? ''));
        $description = trim((string) ($this->request->getPost('description') ?? ''));

        if ($departmentName === '') {
            return redirect()->back()->withInput()->with('error', 'Department name is required.');
        }

        try {
            $departmentModel->update((int) $id, [
                'department_name' => $departmentName,
                'department_code' => $departmentCode !== '' ? $departmentCode : null,
                'description'     => $description !== '' ? $description : null,
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/departments/' . (int) $id)->with('success', 'Department updated successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update department.');
        }
    }

    // Program Management Methods
    public function createProgram()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }
            return redirect()->to('/auth/login');
        }

        if (!$this->request->is('post')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
            }
            return redirect()->to('/admin/courses');
        }

        $programName = trim((string) $this->request->getPost('program_name'));
        $programCode = trim((string) ($this->request->getPost('program_code') ?? ''));
        $department = trim((string) $this->request->getPost('department'));
        $description = trim((string) ($this->request->getPost('description') ?? ''));
        $duration = (int) ($this->request->getPost('duration') ?? 4);

        if ($programName === '' || $department === '') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Program name and department are required.']);
            }
            return redirect()->to('/admin/courses')->with('error', 'Program name and department are required.');
        }

        $programModel = new ProgramModel();

        try {
            $data = [
                'program_name' => $programName,
                'program_code' => $programCode !== '' ? $programCode : null,
                'department' => $department,
                'description' => $description !== '' ? $description : null,
                'duration' => $duration > 0 ? $duration : 4,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $programModel->insert($data);
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => true, 'message' => 'Program created successfully.']);
            }
            return redirect()->to('/admin/courses')->with('success', 'Program created successfully.');
        } catch (\Throwable $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to create program.']);
            }
            return redirect()->to('/admin/courses')->with('error', 'Failed to create program.');
        }
    }

    public function deleteProgram($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        if (!$this->request->is('post')) {
            return redirect()->to('/admin/courses');
        }

        $programModel = new ProgramModel();

        try {
            $programModel->delete((int) $id);
            return redirect()->to('/admin/courses')->with('success', 'Program deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->to('/admin/courses')->with('error', 'Failed to delete program.');
        }
    }

    public function showProgram($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $programModel = new ProgramModel();
        $program = $programModel->find((int) $id);
        if (!$program) {
            return redirect()->to('/admin/courses')->with('error', 'Program not found.');
        }

        return view('admin/program_view', [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'program' => $program,
        ]);
    }

    public function editProgram($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $programModel = new ProgramModel();
        $program = $programModel->find((int) $id);
        if (!$program) {
            return redirect()->to('/admin/courses')->with('error', 'Program not found.');
        }

        $departmentModel = new DepartmentModel();
        $departments = $departmentModel->orderBy('department_name', 'ASC')->findAll();

        return view('admin/program_edit', [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'program'     => $program,
            'departments' => $departments,
        ]);
    }

    public function updateProgram($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        if (!$this->request->is('post')) {
            return redirect()->to('/admin/programs/' . (int) $id . '/edit');
        }

        $programModel = new ProgramModel();
        $program = $programModel->find((int) $id);
        if (!$program) {
            return redirect()->to('/admin/courses')->with('error', 'Program not found.');
        }

        $programName = trim((string) $this->request->getPost('program_name'));
        $programCode = trim((string) ($this->request->getPost('program_code') ?? ''));
        $department  = trim((string) $this->request->getPost('department'));
        $description = trim((string) ($this->request->getPost('description') ?? ''));
        $duration    = (int) ($this->request->getPost('duration') ?? 0);

        if ($programName === '' || $department === '') {
            return redirect()->back()->withInput()->with('error', 'Program name and department are required.');
        }

        if ($duration <= 0) {
            $duration = 4;
        }

        try {
            $programModel->update((int) $id, [
                'program_name' => $programName,
                'program_code' => $programCode !== '' ? $programCode : null,
                'department'   => $department,
                'description'  => $description !== '' ? $description : null,
                'duration'     => $duration,
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/programs/' . (int) $id)->with('success', 'Program updated successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update program.');
        }
    }

    // API method to get programs by department (for dynamic loading)
    public function getProgramsByDepartment()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $department = $this->request->getGet('department');
        if (!$department) {
            return $this->response->setJSON([]);
        }

        $programModel = new ProgramModel();
        $programs = $programModel->getProgramsByDepartment($department);

        return $this->response->setJSON($programs);
    }

    // API method to get recent activities for real-time updates
    public function getRecentActivities()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $activityLogModel = new ActivityLogModel();
        $db = Database::connect();
        
        try {
            if ($db->tableExists('activity_logs')) {
                $activities = $activityLogModel->getRecentActivities(10);
                
                // Format activities for display
                $formattedActivities = [];
                foreach ($activities as $activity) {
                    $formattedActivities[] = [
                        'id' => $activity['id'],
                        'date_time' => date('M j, Y H:i', strtotime($activity['created_at'])),
                        'user' => htmlspecialchars($activity['user_name']),
                        'action' => htmlspecialchars($activity['action']),
                        'details' => htmlspecialchars($activity['details'] ?? '')
                    ];
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'activities' => $formattedActivities
                ]);
            } else {
                // Fallback to recent users if activity log table doesn't exist
                $userModel = new UserModel();
                $recentUsers = $userModel
                    ->orderBy('created_at', 'DESC')
                    ->select('name,email,role,created_at')
                    ->limit(10)
                    ->find();
                
                $formattedActivities = [];
                foreach ($recentUsers as $user) {
                    $formattedActivities[] = [
                        'id' => $user['id'],
                        'date_time' => date('M j, Y H:i', strtotime($user['created_at'])),
                        'user' => htmlspecialchars($user['name']),
                        'action' => 'User Registered',
                        'details' => 'New ' . htmlspecialchars($user['role']) . ' account created'
                    ];
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'activities' => $formattedActivities
                ]);
            }
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Failed to fetch activities'
            ]);
        }
    }

    // Helper method to log activities
    private function logActivity($action, $details = null)
    {
        $session = session();
        $activityLogModel = new ActivityLogModel();
        
        $userId = $session->get('user_id');
        $userName = $session->get('name');
        $ipAddress = $this->request->getIPAddress();
        $userAgent = $this->request->getUserAgent();
        
        try {
            $activityLogModel->logActivity($userId, $userName, $action, $details, $ipAddress, $userAgent);
        } catch (\Throwable $e) {
            // Log error but don't break the main functionality
            log_message('error', 'Failed to log activity: ' . $e->getMessage());
        }
    }

    public function deleteUser($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $id = (int) $id;
        if ($id === (int) $session->get('id')) {
            return redirect()->to('/admin/users')->with('error', 'You cannot delete your own account.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }

        try {
            // Log the deletion before actually deleting
            $this->logActivity('User Deleted', 'Deleted user: ' . $user['name'] . ' (' . $user['email'] . ')');
            
            $userModel->delete($id);
            return redirect()->to('/admin/users')->with('success', 'User deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->to('/admin/users')->with('error', 'Failed to delete user.');
        }
    }

    public function storeUser()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        if (!$this->request->is('post')) {
            return redirect()->to('/admin/users/create');
        }

        $name = trim((string) $this->request->getPost('name'));
        $email = trim((string) $this->request->getPost('email'));
        $password = trim((string) $this->request->getPost('password'));
        $role = trim((string) $this->request->getPost('role'));

        if (empty($name) || empty($email) || empty($password) || empty($role)) {
            return redirect()->back()->withInput()->with('error', 'All fields are required.');
        }

        $userModel = new UserModel();

        // Check if email already exists
        if ($userModel->where('email', $email)->first()) {
            return redirect()->back()->withInput()->with('error', 'Email already exists.');
        }

        try {
            $userData = [
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $userModel->save($userData);
            
            // Log user creation
            $this->logActivity('User Created', 'Created new user: ' . $name . ' (' . $email . ') with role: ' . $role);
            
            return redirect()->to('/admin/users')->with('success', 'User created successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create user.');
        }
    }

    public function updateUser($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        if (!$this->request->is('post')) {
            return redirect()->to('/admin/users/edit/' . $id);
        }

        $id = (int) $id;
        $name = trim((string) $this->request->getPost('name'));
        $email = trim((string) $this->request->getPost('email'));
        $role = trim((string) $this->request->getPost('role'));

        if (empty($name) || empty($email) || empty($role)) {
            return redirect()->back()->withInput()->with('error', 'All fields are required.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }

        try {
            $userData = [
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $userModel->update($id, $userData);
            
            // Log user update
            $this->logActivity('User Updated', 'Updated user: ' . $name . ' (' . $email . ') with role: ' . $role);
            
            return redirect()->to('/admin/users')->with('success', 'User updated successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update user.');
        }
    }
}
