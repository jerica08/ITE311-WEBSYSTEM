<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
         $users = [
           
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
