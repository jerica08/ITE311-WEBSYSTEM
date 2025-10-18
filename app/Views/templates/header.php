<?php
helper('url');
$session   = session();
$isLogged  = (bool) ($session->get('isLoggedIn') ?? $session->get('logged_in') ?? false);
$role      = strtolower((string) ($session->get('role') ?? $session->get('user_role') ?? ''));
$name      = (string) ($session->get('name') ?? $session->get('user_name') ?? '');
?>
<style>
    .topbar { background:#000; color:#fff; padding:.5rem 1rem; font-family: 'Times New Roman', serif; }
    .subbar { background:#DAA520; color:#fff; padding:.5rem 1rem; font-family: 'Times New Roman', serif; }
    .menu a { color:#fff; text-decoration:none; padding:.4rem .8rem; border-radius:.3rem; }
    .menu a.active, .menu a:hover { background: rgba(0,0,0,.15); }
    .logout-btn { background:#E74C3C; color:#fff; border:none; padding:.35rem .7rem; border-radius:.3rem; }
</style>

<div class="topbar">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="fw-bold">Kawas National University</div>
        <?php if ($isLogged && $name): ?>
            <div class="small">Signed in as <strong><?= esc($name) ?></strong> (<?= esc(ucfirst($role)) ?>)</div>
        <?php endif; ?>
    </div>
</div>
<div class="subbar">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="fw-bold">Learning Management System</div>
        <div class="menu d-flex align-items-center gap-2">
            <?php if (!$isLogged): ?>
                <a href="<?= site_url('/') ?>" class="<?= (uri_string() === '' ? 'active' : '') ?>">Home</a>
                <a href="<?= site_url('about') ?>">About</a>
                <a href="<?= site_url('contact') ?>">Contact</a>
                <a href="<?= site_url('login') ?>">Login</a>
                <a href="<?= site_url('register') ?>">Sign Up</a>
            <?php else: ?>
                <?php if ($role === 'admin'): ?>
                    <a href="<?= site_url('admin/dashboard-simple') ?>">Dashboard</a>
                    <a href="<?= site_url('admin/users') ?>">User Management</a>
                    <a href="<?= site_url('admin/courses') ?>">Course Management</a>
                <?php elseif ($role === 'teacher' || $role === 'instructor'): ?>
                    <a href="<?= site_url('teacher/dashboard-simple') ?>">Dashboard</a>
                    <a href="#">My Courses</a>
                    <a href="#">Assignments</a>
                <?php elseif ($role === 'student'): ?>
                    <a href="<?= site_url('student/dashboard') ?>">Dashboard</a>
                    <a href="<?= site_url('announcements') ?>" class="<?= (uri_string() === 'announcements' ? 'active' : '') ?>">Announcements</a>
                    <a href="#">My Classes</a>
                    <a href="#">Grades</a>
                <?php else: ?>
                    <a href="<?= site_url('/') ?>">Home</a>
                <?php endif; ?>
                <a href="<?= site_url('logout') ?>" class="btn btn-sm logout-btn ms-2">Logout</a>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</div>

<div class="container my-3">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
</div>
