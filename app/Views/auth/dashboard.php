<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kawas National High School LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            font-family: 'Times New Roman', serif;
        }
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        .button {
            background-color: #DAA520;
            color: black;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            transition: background 0.3s;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #D2B55B;
        }
        .main-content {
            margin-top: 140px;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #DAA520;
            border-radius: 15px 15px 0 0 !important;
            color: black;
            font-family: 'Times New Roman', serif;
            font-weight: bold;
        }
        .welcome-card {
            background: linear-gradient(135deg, #DAA520 0%, #D2B55B 100%);
            color: black;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 30px;
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-icon {
            font-size: 3rem;
            color: #DAA520;
            margin-bottom: 1rem;
        }
        .school-logo {
            max-width: 80px;
            height: auto;
            margin-right: 20px;
        }
        .user-avatar {
            width: 80px;
            height: 80px;
            background-color: #DAA520;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: black;
            font-weight: bold;
        }
        .btn-logout {
            background-color: #dc3545;
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .btn-logout:hover {
            background-color: #c82333;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navigationbar">
            <nav class="text d-flex align-items-center" style="background-color:#000000;padding: 10px;">
                <p><h4 style="color: white;text-align:left;margin-bottom:none;font-family: 'Times New Roman', serif;">Kawas National High School</h4></p>
            </nav>  
            <nav class="btm-navbar" style="background-color:#DAA520;font-family: 'Times New Roman', serif;">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                    <a class="navbar-brand text-white" href="#"><h2>Learning Management System</h2></a>
                    <ul class="nav d-flex align-items-center gap-3">
                        <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('/') ?>"><button class="button"> Home</button></a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('auth/dashboard') ?>"><button class="button"> Dashboard</button></a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('about') ?>"><button class="button"> About Us</button></a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('contact') ?>"><button class="button"> Contact Us</button></a></li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?= site_url('auth/logout') ?>">
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

    <main class="main-content">
        <div class="container">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Welcome Section -->
            <div class="welcome-card">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        <img src="<?= base_url('img/kawas_logo.jpg') ?>" alt="Kawas Logo" class="school-logo">
                    </div>
                    <div class="col-md-8">
                        <h2 class="mb-2">
                            <i class="fas fa-graduation-cap me-2"></i>
                            Welcome to your Dashboard, <?= esc($user['name']) ?>!
                        </h2>
                        <p class="mb-0 fs-5">Kawas National High School Learning Management System</p>
                        <small class="opacity-75">Role: <?= ucfirst(esc($user['role'])) ?> | Email: <?= esc($user['email']) ?></small>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="user-avatar">
                            <?= strtoupper(substr($user['name'], 0, 2)) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="stats-card">
                        <div class="stats-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h4>0</h4>
                        <p class="text-muted">Enrolled Courses</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card">
                        <div class="stats-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h4>0</h4>
                        <p class="text-muted">Assignments</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card">
                        <div class="stats-icon">
                            <i class="fas fa-quiz"></i>
                        </div>
                        <h4>0</h4>
                        <p class="text-muted">Quizzes</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card">
                        <div class="stats-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>0%</h4>
                        <p class="text-muted">Progress</p>
                    </div>
                </div>
            </div>

            <!-- Main Content Cards -->
            <div class="row">
                <div class="col-md-8">
                    <!-- Recent Activity -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-clock me-2"></i>
                                Recent Activity
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No recent activity to display.</p>
                                <small class="text-muted">Your course activities will appear here once you enroll in courses.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Assignments -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Upcoming Assignments
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No upcoming assignments.</p>
                                <small class="text-muted">Assignment deadlines will be shown here.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- User Profile -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>
                                Profile Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="user-avatar mx-auto mb-3">
                                    <?= strtoupper(substr($user['name'], 0, 2)) ?>
                                </div>
                                <h6><?= esc($user['name']) ?></h6>
                                <small class="text-muted"><?= ucfirst(esc($user['role'])) ?></small>
                            </div>
                            <hr>
                            <div class="mb-2">
                                <strong>Email:</strong><br>
                                <small><?= esc($user['email']) ?></small>
                            </div>
                            <div class="mb-2">
                                <strong>Student ID:</strong><br>
                                <small>KNHS-<?= str_pad($user['id'], 4, '0', STR_PAD_LEFT) ?></small>
                            </div>
                            <div class="mb-3">
                                <strong>Role:</strong><br>
                                <span class="badge bg-secondary"><?= ucfirst(esc($user['role'])) ?></span>
                            </div>
                            <div class="d-grid">
                                <a href="#" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-edit me-1"></i>
                                    Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-bolt me-2"></i>
                                Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="#" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-search me-1"></i>
                                    Browse Courses
                                </a>
                                <a href="#" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-book-open me-1"></i>
                                    View Grades
                                </a>
                                <a href="#" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-calendar me-1"></i>
                                    View Schedule
                                </a>
                                <a href="#" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-comments me-1"></i>
                                    Messages
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</body>
</html>
