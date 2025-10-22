<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table            = 'materials';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'course_id',
        'file_name',
        'file_path',
        'created_at',
    ];

    protected $useTimestamps = false; // we only have created_at and we will manage it manually

    /**
     * Insert a new material record.
     *
     * @param array $data
     * @return int|false Insert ID on success, false on failure
     */
    public function insertMaterial(array $data)
    {
        // Ensure created_at if not provided
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        return $this->insert($data, true); // returns insert ID when $returnID=true
    }

    /**
     * Get all materials for a specific course.
     *
     * @param int $courseId
     * @return array
     */
    public function getMaterialsByCourse(int $courseId): array
    {
        return $this->where('course_id', $courseId)
            ->orderBy('id', 'DESC')
            ->findAll();
    }
}
