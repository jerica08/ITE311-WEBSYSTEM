<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Student Dashboard</h2>
        <a href="<?= site_url('logout') ?>" class="btn btn-outline-danger">Logout</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Welcome, <?= esc($user['name'] ?? '') ?></h5>
            <p class="card-text mb-0">Role: <span class="badge text-bg-dark"><?= esc($user['role'] ?? '') ?></span></p>
            <p class="card-text">Email: <?= esc($user['email'] ?? '') ?></p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Available Teachers</div>
        <div class="card-body">
            <?php if (!empty($teachers)): ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($teachers as $t): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><?= esc($t['name']) ?> (<?= esc($t['email']) ?>)</span>
                            <span class="badge text-bg-secondary"><?= esc($t['role']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted mb-0">No teachers found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
