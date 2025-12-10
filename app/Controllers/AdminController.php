<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\CourseModel;
use App\Models\DepartmentModel;
use App\Models\ProgramModel;
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

        // Recent activity: latest 3 registered users
        $recentUsers = $userModel
            ->orderBy('created_at', 'DESC')
            ->select('name,email,role,created_at')
            ->limit(3)
            ->find();

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
            'recentUsers'   => $recentUsers,
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
        $users = $userModel->orderBy('created_at', 'DESC')->findAll();

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

    public function storeUser()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        if (!$this->request->is('post')) {
            return redirect()->to('/admin/users');
        }

        $name  = trim((string) $this->request->getPost('name'));
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');
        $passwordConfirm = (string) $this->request->getPost('password_confirm');
        $role  = trim((string) $this->request->getPost('role'));

        if ($name === '' || $email === '' || $password === '' || $passwordConfirm === '' || $role === '') {
            return redirect()->back()->withInput()->with('error', 'All fields are required.');
        }

        if ($password !== $passwordConfirm) {
            return redirect()->back()->withInput()->with('error', 'Passwords do not match.');
        }

        $userModel = new UserModel();

        try {
            $userModel->insert([
                'name'     => $name,
                'email'    => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role'     => $role,
            ]);

            // If this new user is a student, also create a record in students table
            if (strtolower($role) === 'student') {
                $userId = $userModel->getInsertID();

                if ($userId) {
                    try {
                        $studentModel->insert([
                            'user_id' => $userId,
                            'email'   => $email,
                        ]);
                    } catch (\Throwable $e) {
                        // Optionally log, but don't block user creation in admin
                    }
                }
            }
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to create user. The email may already be in use.');
        }

        return redirect()->to('/admin/users')->with('success', 'User created successfully.');
    }

    public function courses()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $db = Database::connect();
        $courses = [];
        try {
            if ($db->tableExists('courses')) {
                $courses = $db->table('courses')
                    ->select('id, title, code, unit, course_level, department, course_start_date, course_end_date, enrollment_start_date, enrollment_end_date, class_schedule, academic_year, start_date, end_date, instructor_id, created_at')
                    ->orderBy('created_at', 'DESC')
                    ->get()->getResultArray();
            }
        } catch (\Throwable $e) {
            $courses = [];
        }

        $userModel = new UserModel();
        $teachers = $userModel->where('role', 'teacher')->orderBy('name', 'ASC')->findAll();

        // Get departments and programs
        $departmentModel = new DepartmentModel();
        $programModel = new ProgramModel();
        
        $departments = [];
        $programs = [];
        
        try {
            $departments = $departmentModel->orderBy('department_name', 'ASC')->findAll();
            $programs = $programModel->orderBy('program_name', 'ASC')->findAll();
        } catch (\Throwable $e) {
            $departments = [];
            $programs = [];
        }

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
        $courseStartDate = trim((string) ($this->request->getPost('course_start_date') ?? ''));
        $courseEndDate = trim((string) ($this->request->getPost('course_end_date') ?? ''));
        $enrollmentStartDate = trim((string) ($this->request->getPost('enrollment_start_date') ?? ''));
        $enrollmentEndDate = trim((string) ($this->request->getPost('enrollment_end_date') ?? ''));
        $classSchedule = trim((string) ($this->request->getPost('class_schedule') ?? ''));
        $academicYear = trim((string) ($this->request->getPost('academic_year') ?? ''));
        $startDate = (string) ($this->request->getPost('start_date') ?? '');
        $endDate = (string) ($this->request->getPost('end_date') ?? '');
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
                'course_start_date' => $courseStartDate !== '' ? $courseStartDate : null,
                'course_end_date' => $courseEndDate !== '' ? $courseEndDate : null,
                'enrollment_start_date' => $enrollmentStartDate !== '' ? $enrollmentStartDate : null,
                'enrollment_end_date' => $enrollmentEndDate !== '' ? $enrollmentEndDate : null,
                'class_schedule' => $classSchedule !== '' ? $classSchedule : null,
                'academic_year' => $academicYear !== '' ? $academicYear : null,
                'start_date' => $startDate !== '' ? $startDate : null,
                'end_date' => $endDate !== '' ? $endDate : null,
                'instructor_id' => $instructorId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $db->table('courses')->insert($data);
            return redirect()->to('/admin/courses')->with('success', 'Course created successfully.');
        } catch (\Throwable $e) {
            return redirect()->to('/admin/courses')->with('error', 'Failed to create course.');
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
        $courseStartDate = trim((string) ($this->request->getPost('course_start_date') ?? ''));
        $courseEndDate = trim((string) ($this->request->getPost('course_end_date') ?? ''));
        $enrollmentStartDate = trim((string) ($this->request->getPost('enrollment_start_date') ?? ''));
        $enrollmentEndDate = trim((string) ($this->request->getPost('enrollment_end_date') ?? ''));
        $classSchedule = trim((string) ($this->request->getPost('class_schedule') ?? ''));
        $academicYear = trim((string) ($this->request->getPost('academic_year') ?? ''));
        $startDate = (string) ($this->request->getPost('start_date') ?? '');
        $endDate = (string) ($this->request->getPost('end_date') ?? '');
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
            'course_start_date' => $courseStartDate !== '' ? $courseStartDate : null,
            'course_end_date' => $courseEndDate !== '' ? $courseEndDate : null,
            'enrollment_start_date' => $enrollmentStartDate !== '' ? $enrollmentStartDate : null,
            'enrollment_end_date' => $enrollmentEndDate !== '' ? $enrollmentEndDate : null,
            'class_schedule' => $classSchedule !== '' ? $classSchedule : null,
            'academic_year' => $academicYear !== '' ? $academicYear : null,
            'start_date' => $startDate !== '' ? $startDate : null,
            'end_date' => $endDate !== '' ? $endDate : null,
            'instructor_id' => $instructorId > 0 ? $instructorId : null,
        ];

        $courseModel->update((int) $id, $data);

        return redirect()->to('/admin/courses')->with('success', 'Course updated successfully.');
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

    public function updateUser($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        if (!$this->request->is('post')) {
            return redirect()->to('/admin/users');
        }

        $name = trim((string) $this->request->getPost('name'));
        $role = trim((string) $this->request->getPost('role'));

        if ($name === '' || $role === '') {
            return redirect()->back()->withInput()->with('error', 'Name and role are required.');
        }

        $dataToUpdate = [
            'name' => $name,
        ];

        if ((int) $id !== (int) $session->get('user_id')) {
            $dataToUpdate['role'] = $role;
        }

        $userModel = new UserModel();
        $userModel->skipValidation(true)->update((int) $id, $dataToUpdate);

        return redirect()->to('/admin/users')->with('success', 'User updated successfully.');
    }

    // Department Management Methods
    public function createDepartment()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        if (!$this->request->is('post')) {
            return redirect()->to('/admin/courses');
        }

        $departmentName = trim((string) $this->request->getPost('department_name'));
        $departmentCode = trim((string) ($this->request->getPost('department_code') ?? ''));
        $description = trim((string) ($this->request->getPost('description') ?? ''));

        if ($departmentName === '') {
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
            return redirect()->to('/admin/courses')->with('success', 'Department created successfully.');
        } catch (\Throwable $e) {
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

    // Program Management Methods
    public function createProgram()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        if (!$this->request->is('post')) {
            return redirect()->to('/admin/courses');
        }

        $programName = trim((string) $this->request->getPost('program_name'));
        $programCode = trim((string) ($this->request->getPost('program_code') ?? ''));
        $department = trim((string) $this->request->getPost('department'));
        $description = trim((string) ($this->request->getPost('description') ?? ''));
        $duration = (int) ($this->request->getPost('duration') ?? 4);

        if ($programName === '' || $department === '') {
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
            return redirect()->to('/admin/courses')->with('success', 'Program created successfully.');
        } catch (\Throwable $e) {
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
        $userModel->delete($id);

        return redirect()->to('/admin/users')->with('success', 'User deleted successfully.');
    }
}
