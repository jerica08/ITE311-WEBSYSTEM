<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCourseLevelAndDepartmentToCourses extends Migration
{
    public function up()
    {
        $this->forge->addColumn('courses', [
            'course_level' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'after' => 'unit',
            ],
            'department' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'course_level',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', 'course_level');
        $this->forge->dropColumn('courses', 'department');
    }
}
