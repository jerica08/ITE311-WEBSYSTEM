<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
use App\Models\CourseModel;
use Config\Database;

class Course extends BaseController
{
    protected CourseModel $courseModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
    }
    /**
     * Handle AJAX enrollment requests.
     * POST: course_id
     * JSON responses with appropriate HTTP codes.
     */
    public function enroll()
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)
                ->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)
                ->setJSON(['status' => 'error', 'message' => 'Method Not Allowed']);
        }

        $userId = (int) ($session->get('user_id') ?? 0);
        $courseId = (int) ($this->request->getPost('course_id') ?? 0);

        if ($userId <= 0) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Invalid user']);
        }

        if ($courseId <= 0) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Invalid course_id']);
        }

        // Ensure course exists and is within its active date range
        $db = Database::connect();
        $course = null;
        try {
            $course = $db->table('courses')
                ->select('id, start_date, end_date')
                ->where('id', $courseId)
                ->get()->getRowArray();
        } catch (\Throwable $e) {
            $course = null;
        }

        if (!$course) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Course not found']);
        }

        // Check availability window
        $today = date('Y-m-d');
        $startDate = isset($course['start_date']) && $course['start_date'] !== null && $course['start_date'] !== ''
            ? substr((string) $course['start_date'], 0, 10)
            : null;
        $endDate = isset($course['end_date']) && $course['end_date'] !== null && $course['end_date'] !== ''
            ? substr((string) $course['end_date'], 0, 10)
            : null;

        if ($startDate !== null && $today < $startDate) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'This course is not yet open for enrollment.']);
        }

        if ($endDate !== null && $today > $endDate) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Enrollment for this course has ended.']);
        }

        $enrollmentModel = new EnrollmentModel();

        if ($enrollmentModel->isAlreadyEnrolled($userId, $courseId)) {
            return $this->response->setStatusCode(409)
                ->setJSON(['status' => 'exists', 'message' => 'User already enrolled']);
        }

        $insertId = $enrollmentModel->enrollUser([
            'user_id'         => $userId,
            'course_id'       => $courseId,
            // Model will auto-fill enrollment_date if missing, but set it explicitly as current timestamp
            'enrollment_date' => date('Y-m-d H:i:s'),
        ]);

        if ($insertId === false) {
            return $this->response->setStatusCode(500)
                ->setJSON(['status' => 'error', 'message' => 'Failed to enroll user']);
        }

        // Create a notification for the student about the new enrollment (temporary for testing)
        try {
            $courseRow = $db->table('courses')->select('title')->where('id', $courseId)->get()->getRowArray();
            $courseTitle = $courseRow['title'] ?? ('Course #' . $courseId);
            $notificationModel = new NotificationModel();
            $notificationModel->insert([
                'user_id'    => $userId,
                'message'    => 'You enrolled in ' . $courseTitle,
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // Temporary: log any notification insertion errors for debugging
            log_message('error', 'Notification insert failed after enrollment: ' . $e->getMessage());
        }

        return $this->response->setStatusCode(201)
            ->setJSON([
                'status' => 'success',
                'message' => 'Enrollment created',
                'enrollment_id' => $insertId,
            ]);
    }

    public function search()
    {
        $searchTerm = trim((string) ($this->request->getPost('search_term') ?? $this->request->getGet('search_term') ?? ''));
        $courseModel = new CourseModel();

        if ($searchTerm !== '') {
            $courseModel->groupStart()
                ->like('title', $searchTerm)
                ->orLike('description', $searchTerm)
                ->groupEnd();
        }

        $courses = $courseModel->orderBy('created_at', 'DESC')->findAll();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['courses' => $courses, 'searchTerm' => $searchTerm]);
        }

        return view('courses/search_results', [
            'courses' => $courses,
            'searchTerm' => $searchTerm,
        ]);
    }
}
