<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingFieldsToCoursesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('courses', [
            'program' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'department',
            ],
            'semester' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'program',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', 'program');
        $this->forge->dropColumn('courses', 'semester');
    }
}
