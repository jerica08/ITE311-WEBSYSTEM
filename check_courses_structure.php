<?php
$db = new mysqli('localhost', 'root', '', 'lms_marquez');
if ($db->connect_error) {
    die('Connection failed: ' . $db->connect_error);
}

echo "Checking courses table structure...\n";

$result = $db->query('DESCRIBE courses');
if ($result) {
    echo "Courses table columns:\n";
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['Field']} ({$row['Type']})\n";
    }
} else {
    echo "Error: " . $db->error . "\n";
}

$db->close();
?>
