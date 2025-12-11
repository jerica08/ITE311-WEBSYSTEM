<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #e6e3dc; font-family: 'Times New Roman', serif; }
        .section-title { background:#D1A11F; color:#000; padding:.5rem .75rem; border-radius:8px 8px 0 0; font-weight:600; }
        .table-wrap { border-radius:10px; overflow:hidden; box-shadow:0 8px 16px rgba(0,0,0,.08); background:#fff; }
    </style>
</head>
<body>
    <?= view('templates/header') ?>

    <div class="container my-4">
        <div class="mb-2 section-title">
            <i class="bi bi-people-fill me-2"></i>
            Students Enrolled in: <?= esc($course['title'] ?? 'Course') ?>
        </div>
        <div class="table-wrap">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th style="width:200px;">Enrolled At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($students ?? [])): ?>
                        <?php foreach ($students as $i => $s): ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td><?= esc($s['name'] ?? '') ?></td>
                                <td><?= esc($s['email'] ?? '') ?></td>
                                <td><?= esc($s['enrolled_at'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-muted text-center">No students enrolled in this course yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <a href="<?= site_url('dashboard') ?>" class="btn btn-secondary btn-sm">&laquo; Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
