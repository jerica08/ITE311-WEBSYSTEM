<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveDateFieldsFromCoursesTable extends Migration
{
    public function up()
    {
        // Remove start_date and end_date columns
        $this->forge->dropColumn('courses', 'start_date');
        $this->forge->dropColumn('courses', 'end_date');
    }

    public function down()
    {
        // Add back the columns if we need to rollback
        $this->forge->addColumn('courses', [
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
        ]);
    }
}
