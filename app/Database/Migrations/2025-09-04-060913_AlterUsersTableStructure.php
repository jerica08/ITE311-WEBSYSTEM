<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUsersTableStructure extends Migration
{
    public function up()
    {
        // First, add the 'name' column
        $this->forge->addColumn('users', [
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'after' => 'id'
            ]
        ]);
        
        // Update the name column with combined first_name and last_name
        $this->db->query("UPDATE users SET name = CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) WHERE first_name IS NOT NULL OR last_name IS NOT NULL");
        $this->db->query("UPDATE users SET name = username WHERE name = '' OR name = ' ' OR name IS NULL");
        
        // Drop the columns we don't need
        $this->forge->dropColumn('users', ['username', 'first_name', 'last_name']);
    }

    public function down()
    {
        // Add back the original columns
        $this->forge->addColumn('users', [
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'after' => 'id'
            ],
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'password'
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'first_name'
            ]
        ]);
        
        // Drop the name column
        $this->forge->dropColumn('users', 'name');
    }
}
