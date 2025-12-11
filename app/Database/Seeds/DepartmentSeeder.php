<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'department_name' => 'College of Computer Studies',
                'department_code' => 'CCS',
                'description' => 'Computer Science and IT Programs'
            ],
            [
                'department_name' => 'College of Business',
                'department_code' => 'COB',
                'description' => 'Business Administration Programs'
            ],
            [
                'department_name' => 'College of Engineering',
                'department_code' => 'COE',
                'description' => 'Engineering Programs'
            ],
            [
                'department_name' => 'College of Arts and Sciences',
                'department_code' => 'CAS',
                'description' => 'Liberal Arts and Sciences Programs'
            ]
        ];

        $this->db->table('departments')->insertBatch($data);
    }
}
