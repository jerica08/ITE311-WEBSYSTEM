<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUsersRoleEnum extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('users')) {
            // Convert any legacy 'user' roles to 'student' before restricting ENUM
            try {
                $this->db->query("UPDATE users SET role='student' WHERE role='user'");
            } catch (\Throwable $e) {
                // ignore
            }
            $fields = [
                'role' => [
                    'type'       => 'ENUM',
                    'constraint' => ['admin', 'teacher', 'student'],
                    'default'    => 'student',
                    'null'       => false,
                ],
            ];

            $this->forge->modifyColumn('users', $fields);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('users')) {
            $fields = [
                'role' => [
                    'type'       => 'ENUM',
                    'constraint' => ['admin', 'user'],
                    'default'    => 'user',
                    'null'       => false,
                ],
            ];

            $this->forge->modifyColumn('users', $fields);
            // Optionally convert 'student' back to 'user'
            try {
                $this->db->query("UPDATE users SET role='user' WHERE role='student'");
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }
}
