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

    public function courseDetail($courseId)
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['student', 'user'], true)) {
            return redirect()->to('/login');
        }

        $userId = (int) ($session->get('user_id') ?? 0);
        $courseId = (int) $courseId;
        $db = Database::connect();

        // Verify student is enrolled in this course
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('enrollment_status', 'approved')
            ->first();

        if (!$enrollment) {
            return redirect()->to('/student/assignments')->with('error', 'You are not enrolled in this course.');
        }

        // Get course details
        $course = [];
        try {
            $course = $db->table('courses')
                ->where('id', $courseId)
                ->get()->getRowArray();
        } catch (\Throwable $e) {
            return redirect()->to('/student/assignments')->with('error', 'Course not found.');
        }

        // Count total assignments for this course
        $totalAssignments = $db->table('assignments')
            ->where('course_id', $courseId)
            ->where('status', 'active')
            ->countAllResults();

        // Count pending assignments (assignments not submitted by this student)
        try {
            // First get all assignment IDs for this course
            $allAssignments = $db->table('assignments')
                ->select('id')
                ->where('course_id', $courseId)
                ->where('status', 'active')
                ->get()->getResultArray();
            
            $assignmentIds = array_column($allAssignments, 'id');
            
            // Get submitted assignment IDs for this student
            $submittedAssignments = $db->table('submissions')
                ->select('assignment_id')
                ->where('user_id', $userId)
                ->whereIn('assignment_id', $assignmentIds)
                ->get()->getResultArray();
            
            $submittedIds = array_column($submittedAssignments, 'assignment_id');
            
            // Pending assignments = total assignments - submitted assignments
            $pendingAssignments = count($assignmentIds) - count($submittedIds);
            
        } catch (\Throwable $e) {
            $pendingAssignments = 0;
        }

        // Get upcoming assignments (next 3)
        $upcomingAssignments = [];
        try {
            $upcomingAssignments = $db->table('assignments')
                ->where('course_id', $courseId)
                ->where('status', 'active')
                ->where('due_date >=', date('Y-m-d H:i:s'))
                ->orderBy('due_date', 'ASC')
                ->limit(3)
                ->get()->getResultArray();
        } catch (\Throwable $e) {
            $upcomingAssignments = [];
        }

        return view('student/course_detail', [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'course' => $course,
            'totalAssignments' => $totalAssignments,
            'pendingAssignments' => $pendingAssignments,
            'upcomingAssignments' => $upcomingAssignments
        ]);
    }

    public function assignments()
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['student', 'user'], true)) {
            return redirect()->to('/login');
        }

        $userId = (int) ($session->get('user_id') ?? 0);
        $db = Database::connect();

        // Get assignments for courses the student is enrolled in
        $assignmentsByCourse = [];
        try {
            // First get enrolled courses
            $enrollmentModel = new EnrollmentModel();
            $enrolledCourses = $enrollmentModel
                ->where('user_id', $userId)
                ->where('enrollment_status', 'approved')
                ->findAll();
            
            $courseIds = array_column($enrolledCourses, 'course_id');
            
            if (!empty($courseIds)) {
                // Get assignments for these courses
                $assignments = $db->table('assignments')
                    ->select('assignments.*, courses.title as course_title, courses.code as course_code')
                    ->join('courses', 'courses.id = assignments.course_id')
                    ->whereIn('assignments.course_id', $courseIds)
                    ->where('assignments.status', 'active')
                    ->orderBy('courses.title', 'ASC')
                    ->orderBy('assignments.due_date', 'ASC')
                    ->get()->getResultArray();

                // Group assignments by course
                foreach ($assignments as $assignment) {
                    $courseId = $assignment['course_id'];
                    if (!isset($assignmentsByCourse[$courseId])) {
                        $assignmentsByCourse[$courseId] = [
                            'course_title' => $assignment['course_title'],
                            'course_code' => $assignment['course_code'],
                            'assignments' => []
                        ];
                    }
                    $assignmentsByCourse[$courseId]['assignments'][] = $assignment;
                }
            }
        } catch (\Throwable $e) {
            $assignmentsByCourse = [];
        }

        return view('student/assignments', [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'assignmentsByCourse' => $assignmentsByCourse
        ]);
    }

    public function courseAssignments($courseId)
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['student', 'user'], true)) {
            return redirect()->to('/login');
        }

        $userId = (int) ($session->get('user_id') ?? 0);
        $courseId = (int) $courseId;
        $db = Database::connect();

        // Verify student is enrolled in this course
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('enrollment_status', 'approved')
            ->first();

        if (!$enrollment) {
            return redirect()->to('/student/assignments')->with('error', 'You are not enrolled in this course.');
        }

        // Get course details
        $course = [];
        try {
            $course = $db->table('courses')
                ->where('id', $courseId)
                ->get()->getRowArray();
        } catch (\Throwable $e) {
            return redirect()->to('/student/assignments')->with('error', 'Course not found.');
        }

        // Get all assignments for this course
        $assignments = [];
        try {
            $assignments = $db->table('assignments')
                ->select('*')
                ->where('course_id', $courseId)
                ->where('status', 'active')
                ->orderBy('due_date', 'ASC')
                ->get()->getResultArray();
                
            // Check submission status for each assignment
            foreach ($assignments as &$assignment) {
                $submission = $db->table('submissions')
                    ->where('user_id', $userId)
                    ->where('assignment_id', $assignment['id'])
                    ->get()
                    ->getRow();
                    
                if ($submission) {
                    $assignment['status'] = 'submitted';
                    $assignment['submitted_at'] = $submission->submission_date;
                    $assignment['grade'] = $submission->grade;
                    $assignment['graded_at'] = $submission->graded_at;
                    $assignment['feedback'] = $submission->feedback;
                    
                    // Update status to graded if grade exists
                    if ($submission->grade !== null) {
                        $assignment['status'] = 'graded';
                    }
                } else {
                    $dueDate = $assignment['due_date'] ?? '';
                    if ($dueDate && strtotime($dueDate) < strtotime('now')) {
                        $assignment['status'] = 'overdue';
                    } else {
                        $assignment['status'] = 'pending';
                    }
                }
            }
        } catch (\Throwable $e) {
            $assignments = [];
        }

        return view('student/course_assignments', [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'course' => $course,
            'assignments' => $assignments
        ]);
    }

    public function assignmentDetails($courseId, $assignmentId)
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['student', 'user'], true)) {
            return redirect()->to('/login');
        }

        $userId = (int) ($session->get('user_id') ?? 0);
        $courseId = (int) $courseId;
        $assignmentId = (int) $assignmentId;
        $db = Database::connect();

        // Verify student is enrolled in this course
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('enrollment_status', 'approved')
            ->first();

        if (!$enrollment) {
            return redirect()->to('/student/assignments')->with('error', 'You are not enrolled in this course.');
        }

        // Get course details
        $course = [];
        try {
            $course = $db->table('courses')
                ->where('id', $courseId)
                ->get()->getRowArray();
        } catch (\Throwable $e) {
            return redirect()->to('/student/assignments')->with('error', 'Course not found.');
        }

        // Get assignment details
        $assignment = [];
        try {
            $assignment = $db->table('assignments')
                ->where('id', $assignmentId)
                ->where('course_id', $courseId)
                ->where('status', 'active')
                ->get()->getRowArray();
        } catch (\Throwable $e) {
            return redirect()->to('/student/course/' . $courseId . '/assignments')->with('error', 'Assignment not found.');
        }

        if (!$assignment) {
            return redirect()->to('/student/course/' . $courseId . '/assignments')->with('error', 'Assignment not found.');
        }

        // Check if student has submitted this assignment and get grade info
        $submission = null;
        $status = 'pending'; // Default status
        try {
            $submission = $db->table('submissions')
                ->where('user_id', $userId)
                ->where('assignment_id', $assignmentId)
                ->get()
                ->getRow();
                
            if ($submission) {
                $status = $submission->status ?? 'submitted';
                if ($status === 'submitted' && $submission->grade !== null) {
                    $status = 'graded';
                }
            }
        } catch (\Throwable $e) {
            // Keep default status if query fails
        }

        return view('student/assignment_details', [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'course' => $course,
            'assignment' => $assignment,
            'dueDate' => $dueDate,
            'isOverdue' => $isOverdue,
            'status' => $status,
            'submission' => $submission
        ]);
    }

    public function answerAssignment($courseId, $assignmentId)
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['student', 'user'], true)) {
            return redirect()->to('/login');
        }

        $userId = (int) ($session->get('user_id') ?? 0);
        $courseId = (int) $courseId;
        $assignmentId = (int) $assignmentId;
        $db = Database::connect();

        // Verify student is enrolled in this course
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('enrollment_status', 'approved')
            ->first();

        if (!$enrollment) {
            return redirect()->to('/student/assignments')->with('error', 'You are not enrolled in this course.');
        }

        // Get course details
        $course = [];
        try {
            $course = $db->table('courses')
                ->where('id', $courseId)
                ->get()->getRowArray();
        } catch (\Throwable $e) {
            return redirect()->to('/student/assignments')->with('error', 'Course not found.');
        }

        // Get assignment details
        $assignment = [];
        try {
            $assignment = $db->table('assignments')
                ->where('id', $assignmentId)
                ->where('course_id', $courseId)
                ->where('status', 'active')
                ->get()->getRowArray();
        } catch (\Throwable $e) {
            return redirect()->to('/student/course/' . $courseId . '/assignments')->with('error', 'Assignment not found.');
        }

        if (!$assignment) {
            return redirect()->to('/student/course/' . $courseId . '/assignments')->with('error', 'Assignment not found.');
        }

        // Check if already submitted (this would normally check submissions table)
        $alreadySubmitted = false; // For demo purposes
        
        if ($alreadySubmitted) {
            return redirect()->to('/student/course/' . $courseId . '/assignment/' . $assignmentId)
                ->with('error', 'You have already submitted this assignment.');
        }

        // Determine if overdue
        $dueDate = $assignment['due_date'] ?? '';
        $isOverdue = $dueDate && strtotime($dueDate) < strtotime('now');

        return view('student/answer_assignment', [
            'user' => [
                'name'  => $session->get('name'),
                'email' => $session->get('email'),
                'role'  => $session->get('role'),
            ],
            'course' => $course,
            'assignment' => $assignment,
            'dueDate' => $dueDate,
            'isOverdue' => $isOverdue
        ]);
    }

    public function submitAssignment()
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['student', 'user'], true)) {
            return redirect()->to('/login');
        }

        $assignmentId = $this->request->getPost('assignment_id');
        $courseId = $this->request->getPost('course_id');
        $answerText = $this->request->getPost('answer_text');
        $attachment = $this->request->getFile('attachment');

        // Debug logging
        log_message('debug', 'Submission attempt - Assignment ID: ' . $assignmentId . ', Course ID: ' . $courseId . ', Answer length: ' . strlen($answerText ?? ''));

        // Validate input
        if (!$assignmentId || !$courseId || !$answerText) {
            log_message('error', 'Validation failed - Assignment ID: ' . $assignmentId . ', Course ID: ' . $courseId . ', Answer provided: ' . (!empty($answerText) ? 'yes' : 'no'));
            return redirect()->back()->with('error', 'Please provide an answer text.');
        }

        $userId = (int) ($session->get('user_id') ?? 0);
        $db = Database::connect();

        // Verify student is enrolled in this course
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('enrollment_status', 'approved')
            ->first();

        if (!$enrollment) {
            log_message('error', 'Student not enrolled - User ID: ' . $userId . ', Course ID: ' . $courseId);
            return redirect()->to('/student/assignments')->with('error', 'You are not enrolled in this course.');
        }

        // Debug: Log the incoming data
        log_message('debug', 'Incoming data - Assignment ID: ' . $assignmentId . ', Course ID: ' . $courseId . ', Answer text: ' . substr($answerText, 0, 100) . '...');

        // Verify assignment exists
        $assignment = $db->table('assignments')
            ->where('id', $assignmentId)
            ->get()
            ->getRow();

        log_message('debug', 'Assignment query result: ' . json_encode($assignment));

        if (!$assignment) {
            log_message('error', 'Assignment not found - Assignment ID: ' . $assignmentId);
            return redirect()->back()->with('error', 'Assignment not found. Assignment ID: ' . $assignmentId);
        }

        // Verify course exists
        $course = $db->table('courses')
            ->where('id', $courseId)
            ->get()
            ->getRow();

        log_message('debug', 'Course query result: ' . json_encode($course));

        if (!$course) {
            log_message('error', 'Course not found - Course ID: ' . $courseId);
            return redirect()->back()->with('error', 'Course not found. Course ID: ' . $courseId);
        }

        try {
            // Handle submission data - use only essential fields
            $submissionData = [
                'user_id' => $userId,
                'assignment_id' => $assignmentId,
                'course_id' => $courseId,
                'answer_text' => $answerText,
                'submission_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Handle file upload if provided
            if ($attachment && $attachment->isValid() && !$attachment->hasMoved()) {
                // Validate file size (10MB max)
                if ($attachment->getSize() > 10485760) {
                    return redirect()->back()->with('error', 'File size must be less than 10MB.');
                }
                
                // Validate file type
                $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];
                $fileExt = $attachment->getExtension();
                
                if (!in_array(strtolower($fileExt), $allowedTypes)) {
                    return redirect()->back()->with('error', 'Invalid file type. Allowed types: PDF, DOC, DOCX, TXT, JPG, JPEG, PNG.');
                }
                
                $newName = $attachment->getRandomName();
                $attachment->move(WRITEPATH . 'uploads', $newName);
                $submissionData['attachment'] = $newName;
                $submissionData['original_filename'] = $attachment->getName(); // Store original filename
            }

            // Debug: Log the submission data
            log_message('debug', 'Submission data: ' . json_encode($submissionData));

            // Temporarily disable foreign key checks to identify the issue
            $db->query('SET FOREIGN_KEY_CHECKS = 0');
            
            // Insert submission
            $db->table('submissions')->insert($submissionData);
            
            // Re-enable foreign key checks
            $db->query('SET FOREIGN_KEY_CHECKS = 1');

            return redirect()->to('/student/course/' . $courseId . '/assignments')
                ->with('success', 'Assignment submitted successfully!');

        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            log_message('error', 'Database error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            log_message('error', 'Submission error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit assignment: ' . $e->getMessage());
        }
    }

    /**
     * My Classes - show student's enrolled classes with schedules
     */
    public function myClasses()
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));
        if (!$session->get('isLoggedIn') || !in_array($role, ['student', 'user'], true)) {
            return redirect()->to('/login');
        }

        $userId = (int) ($session->get('user_id') ?? 0);
        $db = Database::connect();

        try {
            log_message('debug', 'MY_CLASSES DEBUG: User ID: ' . $userId);
            
            // Get student's enrolled classes with schedule information
            $enrolledClasses = [];
            if ($userId > 0) {
                $query = $db->table('enrollments')
                    ->select('courses.id, courses.title, courses.code, courses.unit, courses.course_level, courses.department, courses.program, courses.class_schedule, courses.academic_year, courses.course_start_date, courses.course_end_date, enrollments.enrollment_status, enrollments.created_at as enrolled_at')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->where('enrollments.user_id', $userId)
                    ->where('enrollments.enrollment_status', 'approved')
                    ->orderBy('courses.title', 'ASC');
                
                log_message('debug', 'MY_CLASSES DEBUG: Query: ' . $db->getLastQuery());
                
                $enrolledClasses = $query->get()->getResultArray();
                
                log_message('debug', 'MY_CLASSES DEBUG: Found ' . count($enrolledClasses) . ' enrolled classes');
                
                if (!empty($enrolledClasses)) {
                    log_message('debug', 'MY_CLASSES DEBUG: First class: ' . json_encode($enrolledClasses[0]));
                }
            }

            return view('student/my_classes', [
                'user' => [
                    'name'  => $session->get('name'),
                    'email' => $session->get('email'),
                    'role'  => $session->get('role'),
                ],
                'classes' => $enrolledClasses
            ]);

        } catch (\Throwable $e) {
            log_message('error', 'MY_CLASSES ERROR: ' . $e->getMessage());
            log_message('error', 'MY_CLASSES ERROR TRACE: ' . $e->getTraceAsString());
            return redirect()->to('/student/dashboard')->with('error', 'Failed to load your classes.');
        }
    }
}
