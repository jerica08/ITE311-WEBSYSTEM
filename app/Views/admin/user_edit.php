<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #e6e3dc;font-family: 'Times New Roman', serif; }
        .topbar { background:#000; color:#fff; padding:.5rem 1rem; font-family: 'Times New Roman', serif; }
        .subbar { background:#DAA520; color:#fff; padding:.5rem 1rem; font-family: 'Times New Roman', serif; }
        .menu a { color:#fff; text-decoration:none; padding:.4rem .8rem; border-radius:.3rem; }
        .menu a.active, .menu a:hover { background: rgba(0,0,0,.15); }
        .logout-btn { background:#E74C3C; color:#fff; border:none; padding:.4rem .8rem; border-radius:.3rem; }
        .section-title { background:#D1A11F; color:#000; padding:.5rem .75rem; border-radius:8px 8px 0 0; font-weight:600; }
        .card-wrap { border-radius:10px; overflow:hidden; box-shadow:0 8px 16px rgba(0,0,0,.08); background:#fff; }
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
                <a href="<?= site_url('admin') ?>">Dashboard</a>
                <a href="<?= site_url('admin/users') ?>" class="active">User Management</a>
                <a href="<?= site_url('admin/courses') ?>">Course Management</a>
                <a href="<?= site_url('logout') ?>" class="btn btn-sm logout-btn">Logout</a>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <div class="mb-2 section-title"><i class="bi bi-pencil-square me-2"></i>Edit User</div>
        <div class="card-wrap p-4">
            <form action="<?= site_url('admin/users/update/' . $userToEdit['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?= esc($userToEdit['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="<?= esc($userToEdit['email']) ?>" disabled>
                    <div class="form-text">Email cannot be changed here.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <?php $isSelfEdit = isset($user['email'], $userToEdit['email']) && $user['email'] === $userToEdit['email']; ?>
                    <?php if ($isSelfEdit): ?>
                        <input type="hidden" name="role" value="<?= esc($userToEdit['role']) ?>">
                        <input type="text" class="form-control" value="<?= ucfirst(esc($userToEdit['role'])) ?>" disabled>
                    <?php else: ?>
                        <select name="role" class="form-select" required>
                            <option value="admin" <?= $userToEdit['role']==='admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="teacher" <?= $userToEdit['role']==='teacher' ? 'selected' : '' ?>>Teacher</option>
                            <option value="student" <?= $userToEdit['role']==='student' ? 'selected' : '' ?>>Student</option>
                        </select>
                    <?php endif; ?>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="<?= site_url('admin/users') ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
