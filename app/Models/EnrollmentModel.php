<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table            = 'enrollments';
    protected $primaryKey       = 'id';

    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'user_id',
        'course_id',
        'enrollment_date',
        'created_at',
        'updated_at',
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Insert a new enrollment record if not already enrolled.
     *
     * @param array $data Expected keys: user_id, course_id, optional enrollment_date
     * @return int|false Insert ID on success, false if duplicate or failure
     */
    public function enrollUser(array $data)
    {
        if (!isset($data['user_id'], $data['course_id'])) {
            return false;
        }

        $userId = (int) $data['user_id'];
        $courseId = (int) $data['course_id'];

        if ($this->isAlreadyEnrolled($userId, $courseId)) {
            return false;
        }

        if (empty($data['enrollment_date'])) {
            $data['enrollment_date'] = date('Y-m-d H:i:s');
        }

        if ($this->insert($data) === false) {
            return false;
        }

        return (int) $this->getInsertID();
    }

    /**
     * Fetch all courses a user is enrolled in.
     * Includes course fields and the enrollment_date.
     *
     * @param int $user_id
     * @return array
     */
    public function getUserEnrollments(int $user_id): array
    {
        return $this->select('courses.*, enrollments.enrollment_date')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->where('enrollments.user_id', $user_id)
            ->orderBy('enrollments.enrollment_date', 'DESC')
            ->findAll();
    }

    /**
     * Check if a user is already enrolled in a specific course.
     *
     * @param int $user_id
     * @param int $course_id
     * @return bool
     */
    public function isAlreadyEnrolled(int $user_id, int $course_id): bool
    {
        return $this->where([
                'user_id' => $user_id,
                'course_id' => $course_id,
            ])->countAllResults() > 0;
    }
}
