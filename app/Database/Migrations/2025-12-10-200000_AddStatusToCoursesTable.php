<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToCoursesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('courses', [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'published', 'archived'],
                'default' => 'draft',
                'after' => 'instructor_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', 'status');
    }
}
