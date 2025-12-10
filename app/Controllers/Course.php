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
        log_message('debug', 'Course::enroll method called');
        log_message('debug', 'Request method: ' . $this->request->getMethod());
        log_message('debug', 'Request data: ' . json_encode($this->request->getPost()));
        
        $session = session();

        if (!$session->get('isLoggedIn')) {
            log_message('debug', 'User not logged in');
            return $this->response->setStatusCode(401)
                ->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        if (!$this->request->is('post')) {
            log_message('debug', 'Not a POST request');
            return $this->response->setStatusCode(405)
                ->setJSON(['status' => 'error', 'message' => 'Method Not Allowed']);
        }

        $userId = (int) ($session->get('user_id') ?? 0);
        $courseId = (int) ($this->request->getPost('course_id') ?? 0);

        // Debug logging
        log_message('debug', 'Enrollment attempt - User ID: ' . $userId . ', Course ID: ' . $courseId);
        log_message('debug', 'POST data: ' . json_encode($this->request->getPost()));

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
            log_message('debug', 'Looking up course with ID: ' . $courseId);
            $course = $db->table('courses')
                ->select('id, course_start_date, course_end_date')
                ->where('id', $courseId)
                ->get()->getRowArray();
            log_message('debug', 'Course lookup result: ' . json_encode($course));
        } catch (\Throwable $e) {
            log_message('error', 'Course lookup failed: ' . $e->getMessage());
            $course = null;
        }

        if (!$course) {
            log_message('error', 'Course not found for ID: ' . $courseId);
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Course not found']);
        }

        // Check availability window - allow enrollment if course hasn't ended
        $today = date('Y-m-d');
        $startDate = isset($course['course_start_date']) && $course['course_start_date'] !== null && $course['course_start_date'] !== ''
            ? substr((string) $course['course_start_date'], 0, 10)
            : null;
        $endDate = isset($course['course_end_date']) && $course['course_end_date'] !== null && $course['course_end_date'] !== ''
            ? substr((string) $course['course_end_date'], 0, 10)
            : null;

        // Only check if course has ended, not if it hasn't started yet
        if ($endDate !== null && $today > $endDate) {
            log_message('debug', 'Course ended - end date: ' . $endDate . ', today: ' . $today);
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Enrollment for this course has ended.']);
        }

        log_message('debug', 'Date validation passed - start: ' . $startDate . ', end: ' . $endDate . ', today: ' . $today);

        $enrollmentModel = new EnrollmentModel();

        log_message('debug', 'Checking if user already enrolled...');
        if ($enrollmentModel->isAlreadyEnrolled($userId, $courseId)) {
            log_message('debug', 'User already enrolled - returning conflict');
            return $this->response->setStatusCode(409)
                ->setJSON(['status' => 'exists', 'message' => 'User already enrolled']);
        }

        log_message('debug', 'Creating new enrollment...');
        $insertId = $enrollmentModel->enrollUser([
            'user_id'         => $userId,
            'course_id'       => $courseId,
            'enrollment_status' => 'pending', // New enrollments start as pending
            'enrollment_date' => date('Y-m-d H:i:s'),
        ]);

        log_message('debug', 'Enrollment insert result: ' . $insertId);

        if ($insertId === false) {
            log_message('error', 'Failed to insert enrollment record');
            return $this->response->setStatusCode(500)
                ->setJSON(['status' => 'error', 'message' => 'Failed to enroll user']);
        }

        log_message('debug', 'Enrollment successful, creating notification...');
        // Create notification for instructor
        try {
            // Get course details to find instructor
            log_message('debug', 'Getting course details for notification...');
            $courseDetails = $db->table('courses')
                ->select('instructor_id, title')
                ->where('id', $courseId)
                ->get()->getRowArray();
            
            log_message('debug', 'Course details: ' . json_encode($courseDetails));
            
            if ($courseDetails && $courseDetails['instructor_id']) {
                log_message('debug', 'Instructor found: ' . $courseDetails['instructor_id']);
                $notificationModel = new NotificationModel();
                $notificationData = [
                    'user_id' => $courseDetails['instructor_id'],
                    'title' => 'New Enrollment Request',
                    'message' => 'A student has requested enrollment in ' . $courseDetails['title'],
                    'type' => 'enrollment_request',
                    'related_id' => $insertId,
                    'created_at' => date('Y-m-d H:i:s'),
                ];
                log_message('debug', 'Notification data: ' . json_encode($notificationData));
                
                $notificationResult = $notificationModel->insert($notificationData);
                log_message('debug', 'Notification insert result: ' . $notificationResult);
                
                if ($notificationResult) {
                    log_message('debug', 'Notification sent to instructor');
                } else {
                    log_message('error', 'Failed to insert notification');
                }
            } else {
                log_message('warning', 'No instructor assigned to course ID: ' . $courseId);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Failed to create notification: ' . $e->getMessage());
        }

        log_message('debug', 'Returning success response');
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Enrollment request submitted. Waiting for instructor approval.'
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
