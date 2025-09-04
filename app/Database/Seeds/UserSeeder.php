<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
         $users = [
            [
                'username' => 'admin',
                'email' => 'admin@kawas.edu',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'first_name' => 'Admin',
                'last_name' => 'User',
                'role' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'instructor',
                'email' => 'instructor@kawas.edu',
                'password' => password_hash('instructor123', PASSWORD_DEFAULT),
                'first_name' => 'Jane',
                'last_name' => 'Instructor',
                'role' => 'instructor',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'student',
                'email' => 'student@kawas.edu',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'first_name' => 'John',
                'last_name' => 'Student',
                'role' => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'jason',
                'email' => 'jason@lms.com',
                'password' => password_hash('jason123', PASSWORD_DEFAULT),
                'first_name' => 'Jason',
                'last_name' => 'Admin',
                'role' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'karl',
                'email' => 'karl@lms.com',
                'password' => password_hash('karl123', PASSWORD_DEFAULT),
                'first_name' => 'Karl',
                'last_name' => 'Instructor',
                'role' => 'instructor',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'kendra',
                'email' => 'kendra@lms.com',
                'password' => password_hash('kendra123', PASSWORD_DEFAULT),
                'first_name' => 'Kendra',
                'last_name' => 'Student',
                'role' => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        $this->db->table('users')->insertBatch($users);
    }
}
