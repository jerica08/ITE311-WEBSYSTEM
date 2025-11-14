<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CreateUsersTableCommand extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'App';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'db:create-users';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Create users table manually';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'db:create-users';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        // Check if users table exists
        if ($db->tableExists('users')) {
            CLI::write("Users table already exists.", 'yellow');
            return;
        }
        
        CLI::write("Creating users table...", 'green');
        
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
            CLI::write("Users table created successfully!", 'green');
            
            // Mark migration as completed in migrations table
            $migrationSql = "INSERT INTO migrations (version, class, group, namespace, time, batch) VALUES 
                ('2025-09-04-073215', 'App\\\\Database\\\\Migrations\\\\CreateUsersTable', 'default', 'App', " . time() . ", 1)";
            $db->query($migrationSql);
            CLI::write("Migration marked as completed.", 'green');
        } else {
            CLI::write("Error creating users table.", 'red');
        }
    }
}
