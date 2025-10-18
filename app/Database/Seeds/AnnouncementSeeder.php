<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'title'      => 'Welcome to the LMS',
                'content'    => 'This is a sample announcement to get you started.',
                'created_at' => $now,
            ],
            [
                'title'      => 'System Maintenance',
                'content'    => 'We will have scheduled maintenance this weekend. Expect brief downtime.',
                'created_at' => $now,
            ],
        ];

        $this->db->table('announcements')->insertBatch($data);
    }
}
