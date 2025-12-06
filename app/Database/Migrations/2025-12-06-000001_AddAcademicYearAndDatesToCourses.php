<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAcademicYearAndDatesToCourses extends Migration
{
    public function up()
    {
        $fields = [];

        if (!$this->db->fieldExists('academic_year', 'courses')) {
            $fields['academic_year'] = [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'unit',
            ];
        }

        if (!$this->db->fieldExists('start_date', 'courses')) {
            $fields['start_date'] = [
                'type' => 'DATE',
                'null' => true,
                'after' => 'academic_year',
            ];
        }

        if (!$this->db->fieldExists('end_date', 'courses')) {
            $fields['end_date'] = [
                'type' => 'DATE',
                'null' => true,
                'after' => 'start_date',
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('courses', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('end_date', 'courses')) {
            $this->forge->dropColumn('courses', 'end_date');
        }

        if ($this->db->fieldExists('start_date', 'courses')) {
            $this->forge->dropColumn('courses', 'start_date');
        }

        if ($this->db->fieldExists('academic_year', 'courses')) {
            $this->forge->dropColumn('courses', 'academic_year');
        }
    }
}
