<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCourseScheduleFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('courses', [
            'course_start_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'department',
            ],
            'course_end_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'course_start_date',
            ],
            'enrollment_start_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'course_end_date',
            ],
            'enrollment_end_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'enrollment_start_date',
            ],
            'class_schedule' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'enrollment_end_date',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', 'course_start_date');
        $this->forge->dropColumn('courses', 'course_end_date');
        $this->forge->dropColumn('courses', 'enrollment_start_date');
        $this->forge->dropColumn('courses', 'enrollment_end_date');
        $this->forge->dropColumn('courses', 'class_schedule');
    }
}
