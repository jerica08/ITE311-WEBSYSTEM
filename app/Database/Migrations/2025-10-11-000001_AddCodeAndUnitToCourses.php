<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCodeAndUnitToCourses extends Migration
{
    public function up()
    {
        // Add 'code' and 'unit' columns if they don't exist
        $fields = [];
        if (!$this->db->fieldExists('code', 'courses')) {
            $fields['code'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'title',
            ];
        }
        if (!$this->db->fieldExists('unit', 'courses')) {
            $fields['unit'] = [
                'type'       => 'INT',
                'constraint' => 3,
                'null'       => true,
                'after'      => 'code',
            ];
        }
        if (!empty($fields)) {
            $this->forge->addColumn('courses', $fields);
        }
    }

    public function down()
    {
        // Drop added columns (if exist)
        if ($this->db->fieldExists('unit', 'courses')) {
            $this->forge->dropColumn('courses', 'unit');
        }
        if ($this->db->fieldExists('code', 'courses')) {
            $this->forge->dropColumn('courses', 'code');
        }
    }
}
