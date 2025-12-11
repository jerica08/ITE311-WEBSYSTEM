<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifySubmissionsTableForAssignments extends Migration
{
    public function up()
    {
        // Add new columns for assignment submissions
        $this->forge->addColumn('submissions', [
            'student_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true, // Allow null for existing quiz submissions
                'after' => 'id'
            ],
            'assignment_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true, // Allow null for existing quiz submissions
                'after' => 'student_id'
            ],
            'course_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'assignment_id'
            ],
            'answer_text' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'course_id'
            ],
            'attachment' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'answer_text'
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'submitted', 'graded', 'overdue'],
                'default' => 'pending',
                'null' => true,
                'after' => 'attachment'
            ],
            'grade' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'after' => 'score'
            ],
            'feedback' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'grade'
            ],
            'graded_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'feedback'
            ],
            'graded_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'graded_by'
            ]
        ]);

        // Add foreign keys for the new columns
        $this->forge->addForeignKey('student_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assignment_id', 'assignments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('graded_by', 'users', 'id', 'SET NULL', 'CASCADE');

        // Add indexes for better performance
        $this->forge->addKey(['student_id', 'assignment_id'], false, false, 'idx_student_assignment');
        $this->forge->addKey('course_id', false, false, 'idx_course');
        $this->forge->addKey('status', false, false, 'idx_status');
    }

    public function down()
    {
        // Drop the foreign keys first
        $this->db->query('ALTER TABLE submissions DROP FOREIGN KEY IF EXISTS submissions_student_id_foreign');
        $this->db->query('ALTER TABLE submissions DROP FOREIGN KEY IF EXISTS submissions_assignment_id_foreign');
        $this->db->query('ALTER TABLE submissions DROP FOREIGN KEY IF EXISTS submissions_course_id_foreign');
        $this->db->query('ALTER TABLE submissions DROP FOREIGN KEY IF EXISTS submissions_graded_by_foreign');

        // Drop the indexes
        $this->db->query('ALTER TABLE submissions DROP INDEX IF EXISTS idx_student_assignment');
        $this->db->query('ALTER TABLE submissions DROP INDEX IF EXISTS idx_course');
        $this->db->query('ALTER TABLE submissions DROP INDEX IF EXISTS idx_status');

        // Drop the columns
        $this->forge->dropColumn('submissions', 'student_id');
        $this->forge->dropColumn('submissions', 'assignment_id');
        $this->forge->dropColumn('submissions', 'course_id');
        $this->forge->dropColumn('submissions', 'answer_text');
        $this->forge->dropColumn('submissions', 'attachment');
        $this->forge->dropColumn('submissions', 'status');
        $this->forge->dropColumn('submissions', 'grade');
        $this->forge->dropColumn('submissions', 'feedback');
        $this->forge->dropColumn('submissions', 'graded_by');
        $this->forge->dropColumn('submissions', 'graded_at');
    }
}
