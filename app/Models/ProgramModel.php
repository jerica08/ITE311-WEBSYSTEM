<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgramModel extends Model
{
    protected $table = 'programs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'program_name',
        'program_code',
        'department',
        'description',
        'duration',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField = 'updated_at';

    public function getProgramsByDepartment($department)
    {
        return $this->where('department', $department)->findAll();
    }
}
