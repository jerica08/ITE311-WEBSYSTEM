<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'title',
        'description',
        'code',
        'unit',
        'course_level',
        'department',
        'course_start_date',
        'course_end_date',
        'enrollment_start_date',
        'enrollment_end_date',
        'class_schedule',
        'academic_year',
        'start_date',
        'end_date',
        'instructor_id',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
