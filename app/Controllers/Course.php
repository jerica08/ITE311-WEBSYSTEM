<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use Config\Database;

class Course extends BaseController
{
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

        // Ensure course exists
        $db = Database::connect();
        try {
            $courseExists = $db->table('courses')->where('id', $courseId)->countAllResults() > 0;
        } catch (\Throwable $e) {
            $courseExists = false;
        }
        if (!$courseExists) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Course not found']);
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

        return $this->response->setStatusCode(201)
            ->setJSON([
                'status' => 'success',
                'message' => 'Enrollment created',
                'enrollment_id' => $insertId,
            ]);
    }
}
