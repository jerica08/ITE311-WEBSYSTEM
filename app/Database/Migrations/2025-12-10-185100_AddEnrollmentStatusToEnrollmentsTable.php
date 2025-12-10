<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnrollmentStatusToEnrollmentsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('enrollments', [
            'enrollment_status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default' => 'pending',
                'after' => 'course_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('enrollments', 'enrollment_status');
    }
}
