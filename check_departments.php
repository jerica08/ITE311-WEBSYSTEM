<?php
require_once 'system/bootstrap.php';

use Config\Database;

$db = Database::connect();

echo "Checking departments and programs tables...\n\n";

// Check departments
try {
    $departments = $db->table('departments')->get()->getResultArray();
    echo "Departments found: " . count($departments) . "\n";
    if (empty($departments)) {
        echo "Adding sample departments...\n";
        $sampleDepts = [
            ['department_name' => 'College of Computer Studies', 'department_code' => 'CCS', 'description' => 'Computer Science and IT Programs'],
            ['department_name' => 'College of Business', 'department_code' => 'COB', 'description' => 'Business Administration Programs'],
            ['department_name' => 'College of Engineering', 'department_code' => 'COE', 'description' => 'Engineering Programs']
        ];
        foreach ($sampleDepts as $dept) {
            $db->table('departments')->insert($dept);
        }
        echo "Sample departments added.\n";
    } else {
        foreach ($departments as $dept) {
            echo "- " . $dept['department_name'] . " (" . $dept['department_code'] . ")\n";
        }
    }
} catch (\Exception $e) {
    echo "Error with departments: " . $e->getMessage() . "\n";
}

echo "\n";

// Check programs
try {
    $programs = $db->table('programs')->get()->getResultArray();
    echo "Programs found: " . count($programs) . "\n";
    if (empty($programs)) {
        echo "Adding sample programs...\n";
        $samplePrograms = [
            ['program_name' => 'Bachelor of Science in Computer Science', 'program_code' => 'BSCS', 'department' => 'College of Computer Studies', 'description' => 'Computer Science Program'],
            ['program_name' => 'Bachelor of Science in Information Technology', 'program_code' => 'BSIT', 'department' => 'College of Computer Studies', 'description' => 'Information Technology Program'],
            ['program_name' => 'Bachelor of Science in Business Administration', 'program_code' => 'BSBA', 'department' => 'College of Business', 'description' => 'Business Administration Program']
        ];
        foreach ($samplePrograms as $prog) {
            $db->table('programs')->insert($prog);
        }
        echo "Sample programs added.\n";
    } else {
        foreach ($programs as $prog) {
            echo "- " . $prog['program_name'] . " (" . $prog['program_code'] . ") - " . $prog['department'] . "\n";
        }
    }
} catch (\Exception $e) {
    echo "Error with programs: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
?>
