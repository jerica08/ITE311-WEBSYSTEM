<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kawas National High School LMS</title>
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
        .button-active {
            background-color: #B8860B;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .button:hover {
            background-color: #D2B55B;
        }
        .main-content {
            margin-top: 50px;
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
            padding: 20px;
            margin-bottom: 30px;
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-icon {
            font-size: 3rem;
            color: #DAA520;
            margin-bottom: 1rem;
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .stats-label {
            color: #666;
            font-size: 1.1rem;
        }
        .school-logo {
            max-width: 80px;
            height: auto;
            margin-right: 20px;
        }
        .user-avatar {
            width: 80px;
            height: 80px;
            background-color: #dc3545;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
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
        .admin-actions {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .action-btn {
            background-color: #DAA520;
            color: black;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .action-btn:hover {
            background-color: #D2B55B;
            color: black;
            transform: translateY(-2px);
        }
        .admin-badge {
            background-color: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
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
                    <a class="navbar-brand text-white" href="#"><h2>Learning Management System - Admin Panel</h2></a>
                    <ul class="nav d-flex align-items-center gap-3">
                        <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('auth/admin_dashboard') ?>"><button class="button-active"> Admin Dashboard</button></a></li>
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

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
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
                            <i class="fas fa-user-shield me-2"></i>
                            Welcome Administrator, <?= esc($user['name']) ?>!
                        </h2>
                        <p class="mb-0 fs-5">Kawas National High School Learning Management System</p>
                        <small class="opacity-75">
                            <span class="admin-badge">ADMIN</span> | Email: <?= esc($user['email']) ?>
                        </small>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="user-avatar">
                            <?= strtoupper(substr($user['name'], 0, 2)) ?>
                        </div>
                    </div>
                </div>
            </div>         
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</body>
</html>
