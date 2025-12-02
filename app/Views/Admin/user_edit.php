<?php helper(['url', 'form']); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Role</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body style="background-color: #e6e3dc;font-family: 'Times New Roman', serif;">
    <div class="topbar" style="background:#000;color:#fff;padding:.5rem 1rem;font-family: 'Times New Roman', serif;">
        <div class="container-fluid fw-bold">Kawas National High School</div>
    </div>
    <div class="subbar" style="background:#DAA520;color:#fff;padding:.5rem 1rem;font-family: 'Times New Roman', serif;">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="fw-bold">Learning Management System</div>
            <div class="menu d-flex align-items-center gap-2">
                <a href="<?= site_url('admin/dashboard') ?>" style="color:#fff;text-decoration:none;padding:.4rem .8rem;border-radius:.3rem;">Dashboard</a>
                <a href="<?= site_url('admin/users') ?>" style="color:#fff;text-decoration:none;padding:.4rem .8rem;border-radius:.3rem;background: rgba(0,0,0,.15);">User Management</a>
                <a href="<?= site_url('admin/courses') ?>" style="color:#fff;text-decoration:none;padding:.4rem .8rem;border-radius:.3rem;">Course Management</a>
                <a href="<?= site_url('logout') ?>" class="btn btn-sm" style="background:#E74C3C;color:#fff;border:none;padding:.4rem .8rem;border-radius:.3rem;">Logout</a>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <div class="card">
            <div class="card-header" style="background:#D1A11F;color:#000;font-weight:600;">Edit User Role</div>
            <div class="card-body">
                <form action="<?= site_url('admin/users/update-role') ?>" method="post" class="row g-3">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">

                    <div class="col-12 col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" value="<?= esc($user['name']) ?>" disabled>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control" value="<?= esc($user['email']) ?>" disabled>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="teacher" <?= $user['role'] === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                            <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Student</option>
                        </select>
                    </div>

                    <div class="col-12 d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="<?= site_url('admin/users') ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
