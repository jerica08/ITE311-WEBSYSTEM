<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #e6e3dc; font-family: 'Times New Roman', serif; }
        .section-title { background:#D1A11F; color:#000; padding:.5rem .75rem; border-radius:8px 8px 0 0; font-weight:600; }
        .table-wrap { border-radius:10px; overflow:hidden; box-shadow:0 8px 16px rgba(0,0,0,.08); background:#fff; }
        .table thead th { background:#f8f9fa; }
    </style>
</head>
<body>
    <?= view('templates/header', ['title' => 'My Courses']) ?>

    <div class="container my-4">
        <div class="mb-2 section-title"><i class="bi bi-journal-text me-2"></i>My Courses</div>
        <div class="table-wrap mb-4">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Title</th>
                        <th style="width:160px;">Subject Code</th>
                        <th style="width:100px;">Unit</th>
                        <th style="width:180px;">Created</th>
                        <th style="width:140px;">View Students</th>
                        <th style="width:160px;">Upload Materials</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($courses ?? [])): ?>
                        <?php foreach ($courses as $i => $c): ?>
                            <?php $cid = (int)($c['id'] ?? 0); ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td><?= esc($c['title'] ?? '-') ?></td>
                                <td><?= esc($c['code'] ?? '-') ?></td>
                                <td><?= esc($c['unit'] ?? '-') ?></td>
                                <td><?= esc($c['created_at'] ?? '-') ?></td>
                                <td>
                                    <?php if ($cid > 0): ?>
                                        <a class="btn btn-sm btn-outline-success w-100" href="<?= site_url('teacher/courses/' . $cid . '/students') ?>">
                                            View Students
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($cid > 0): ?>
                                        <a class="btn btn-sm btn-outline-warning w-100" href="<?= site_url('materials/upload/' . $cid) ?>">
                                            Upload Materials
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-muted">You are not assigned to any courses yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
