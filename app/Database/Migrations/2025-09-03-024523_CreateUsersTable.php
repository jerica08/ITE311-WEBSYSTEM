<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'unique' => true,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'role' => [
                'type' => 'ENUM',
<<<<<<< HEAD:app/Database/Migrations/2025-09-03-024523_CreateUsersTable.php
                'constraint' => ['admin', 'user',],
                'default' => 'user',
=======
                'constraint' => ['admin', 'instructor', 'student'],
                'default' => 'student',
>>>>>>> 878f8212430edcd4a230eacc45a9e4b068e6ea80:app/Database/Migrations/2025-08-20-000323_CreateUsersTable.php
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
<<<<<<< HEAD:app/Database/Migrations/2025-09-03-024523_CreateUsersTable.php
        $this->forge->addKey('id');
=======
        $this->forge->addPrimaryKey('id');
>>>>>>> 878f8212430edcd4a230eacc45a9e4b068e6ea80:app/Database/Migrations/2025-08-20-000323_CreateUsersTable.php
        $this->forge->createTable('users');
    }

    public function down()
    {
         $this->forge->dropTable("users");
    }
}
