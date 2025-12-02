<?php
helper('url');

if (isset($user['role'])) {
    if ($user['role'] === 'admin') {
        echo view('admin/admin', ['user' => $user]);
        return;
    }

    if ($user['role'] === 'teacher') {
        echo view('teacher', ['user' => $user]);
        return;
    }

    // Default: student dashboard
    echo view('student', ['user' => $user]);
    return;
}
?>
