<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CourseModel;
use Config\Database;

class AdminController extends BaseController
{
    public function dashboard()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $userModel = new UserModel();
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
                    ->select('id, title, code, unit, academic_year, start_date, end_date, instructor_id, created_at')
                    ->orderBy('created_at', 'DESC')
                    ->get()->getResultArray();
            }
        } catch (\Throwable $e) {
            $courses = [];
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
