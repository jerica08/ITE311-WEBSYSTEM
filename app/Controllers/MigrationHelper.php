<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class MigrationHelper extends Controller
{
    public function createUsersTable()
    {
        $db = \Config\Database::connect();
        
        // Check if users table exists
        if ($db->tableExists('users')) {
            echo "Users table already exists.\n";
            return;
        }
        
        // Create users table
        $sql = "CREATE TABLE users (
            id INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'user') DEFAULT 'user',
            created_at DATETIME NULL,
            updated_at DATETIME NULL,
            PRIMARY KEY (id)
        )";
        
        if ($db->query($sql)) {
            echo "Users table created successfully!\n";
            
            // Mark migration as completed in migrations table
            $migrationSql = "INSERT INTO migrations (version, class, group, namespace, time, batch) VALUES 
                ('2025-09-04-073215', 'App\\\\Database\\\\Migrations\\\\CreateUsersTable', 'default', 'App', " . time() . ", 1)";
            $db->query($migrationSql);
            echo "Migration marked as completed.\n";
        } else {
            echo "Error creating users table.\n";
        }
    }
}
