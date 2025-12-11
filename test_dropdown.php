<?php
// Initialize CodeIgniter
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/test_dropdown.php';

require_once 'system/bootstrap.php';

$config = new Config\Database();
$db = $config->connect();

echo "Checking tables...\n";

if ($db->tableExists('departments')) {
    echo "Departments table exists\n";
    $depts = $db->table('departments')->get()->getResultArray();
    echo "Found " . count($depts) . " departments\n";
    foreach ($depts as $dept) {
        echo "- " . $dept['department_name'] . "\n";
    }
} else {
    echo "Departments table does not exist\n";
}

if ($db->tableExists('programs')) {
    echo "Programs table exists\n";
    $progs = $db->table('programs')->get()->getResultArray();
    echo "Found " . count($progs) . " programs\n";
    foreach ($progs as $prog) {
        echo "- " . $prog['program_name'] . " (Dept: " . $prog['department'] . ")\n";
    }
} else {
    echo "Programs table does not exist\n";
}
?>
