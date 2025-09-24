<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUsersTable extends Migration
{
    public function up()
    {
        $fields = [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'teacher', 'student'],
                'default'    => 'student',
            ],
        ];

        $this->forge->modifyColumn('users', $fields);
    }

    public function down()
    {
         $fields = [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'user'],
                'default'    => 'user',
            ],
        ];

        $this->forge->modifyColumn('users', $fields);
    }
}
