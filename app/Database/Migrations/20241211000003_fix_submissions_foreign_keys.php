<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixSubmissionsForeignKeys extends Migration
{
    public function up()
    {
        // Drop any existing problematic foreign keys first
        $this->db->query('ALTER TABLE submissions DROP FOREIGN KEY IF EXISTS submissions_student_id_foreign');
        $this->db->query('ALTER TABLE submissions DROP FOREIGN KEY IF EXISTS submissions_assignment_id_foreign');
        $this->db->query('ALTER TABLE submissions DROP FOREIGN KEY IF EXISTS submissions_course_id_foreign');
        $this->db->query('ALTER TABLE submissions DROP FOREIGN KEY IF EXISTS submissions_graded_by_foreign');
        
        // Add proper foreign keys with proper constraints
        $this->db->query('ALTER TABLE submissions ADD CONSTRAINT submissions_student_id_foreign FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE submissions ADD CONSTRAINT submissions_assignment_id_foreign FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE submissions ADD CONSTRAINT submissions_course_id_foreign FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE submissions ADD CONSTRAINT submissions_graded_by_foreign FOREIGN KEY (graded_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Drop the foreign keys
        $this->db->query('ALTER TABLE submissions DROP FOREIGN KEY IF EXISTS submissions_student_id_foreign');
        $this->db->query('ALTER TABLE submissions DROP FOREIGN KEY IF EXISTS submissions_assignment_id_foreign');
        $this->db->query('ALTER TABLE submissions DROP FOREIGN KEY IF EXISTS submissions_course_id_foreign');
        $this->db->query('ALTER TABLE submissions DROP FOREIGN KEY IF EXISTS submissions_graded_by_foreign');
    }
}
