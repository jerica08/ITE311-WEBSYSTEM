<?php
// Shared dynamic navigation bar based on user role
// Accept either 'user_role' (used in this app) or a generic 'role' (for compatibility)
$role = session('user_role') ?? session('role');
$name = session('user_name') ?? session('name');
?>
<header>
    <nav class="navigationbar">
        <nav class="text d-flex align-items-center" style="background-color:#000000;padding: 10px;">
            <h1 style="color: white;margin-bottom:0;font-family: 'Times New Roman', serif;">Kawas National High School</h1>
        </nav>
        <nav class="btm-navbar" style="background-color:#DAA520;font-family: 'Times New Roman', serif;">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <a class="navbar-brand text-white" href="#"><h4>Learning Management System</h4></a>
                <ul class="nav d-flex align-items-center gap-3">
                    <?php if ($role === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('/admin/dashboard') ?>"><button class="button-active">Dashboard</button></a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="#"><button class="button">User Management</button></a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="#"><button class="button">Course Management</button></a></li>
                    <?php elseif ($role === 'teacher'): ?>
                        <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('/teacher/dashboard') ?>"><button class="button-active">Dashboard</button></a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="#"><button class="button">My Courses</button></a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="#"><button class="button">Assignments</button></a></li>
                    <?php elseif ($role === 'student'): ?>
                        <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('/student/dashboard') ?>"><button class="button-active">Dashboard</button></a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="#"><button class="button">My Classes</button></a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="#"><button class="button">Grades</button></a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('/dashboard') ?>"><button class="button-active">Dashboard</button></a></li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?= base_url('/logout') ?>">
                            <button class="btn-logout">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </button>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </nav>
</header>
