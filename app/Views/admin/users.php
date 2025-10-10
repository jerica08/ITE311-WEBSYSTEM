<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #e6e3dc; }
        .topbar { background:#000; color:#fff; padding:.5rem 1rem; font-family: 'Times New Roman', serif; }
        .subbar { background:#DAA520; color:#fff; padding:.5rem 1rem; font-family: 'Times New Roman', serif; }
        .menu a { color:#fff; text-decoration:none; padding:.4rem .8rem; border-radius:.3rem; }
        .menu a.active, .menu a:hover { background: rgba(0,0,0,.15); }
        .logout-btn { background:#E74C3C; color:#fff; border:none; padding:.4rem .8rem; border-radius:.3rem; }
        .section-title { background:#D1A11F; color:#000; padding:.5rem .75rem; border-radius:8px 8px 0 0; font-weight:600; }
        .table-wrap { border-radius:10px; overflow:hidden; box-shadow:0 8px 16px rgba(0,0,0,.08); background:#fff; }
        .table thead th { background:#f8f9fa; }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="container-fluid fw-bold">Kawas National High School</div>
    </div>
    <div class="subbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="fw-bold">Learning Management System</div>
            <div class="menu d-flex align-items-center gap-2">
                <a href="<?= site_url('admin/dashboard') ?>">Dashboard</a>
                <a href="<?= site_url('admin/users') ?>" class="active">User Management</a>
                <a href="<?= site_url('admin/courses') ?>">Course Management</a>
                <a href="<?= site_url('logout') ?>" class="btn btn-sm logout-btn">Logout</a>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <div class="mb-2 section-title"><i class="bi bi-people-fill me-2"></i>Users</div>
        <div class="table-wrap">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th style="width:140px;">Role</th>
                        <th style="width:180px;">Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users ?? [])): ?>
                        <?php foreach ($users as $i => $u): ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td><?= esc($u['name']) ?></td>
                                <td><?= esc($u['email']) ?></td>
                                <td><?= esc($u['role']) ?></td>
                                <td><?= esc($u['created_at'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-muted">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
