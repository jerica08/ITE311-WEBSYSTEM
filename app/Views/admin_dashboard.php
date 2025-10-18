<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard (Simple)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?= view('templates/header', ['title' => 'Admin Dashboard']) ?>

    <div class="container my-4">
        <h1>Welcome, Admin!</h1>
        <p class="text-muted">This is the simple admin dashboard view required by the task.</p>
    </div>
</body>
</html>
