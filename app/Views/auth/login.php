<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kawas National High School LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            font-family: 'Times New Roman', serif;
        }
        .button {
            background-color: #DAA520;
            color: black;
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
            max-width: 400px;
            margin: 0 auto;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #DAA520;
            color: black;
            text-align: center;
            padding: 1.5rem;
            border-radius: 10px 10px 0 0 !important;
        }
        .form-control {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            font-family: 'Times New Roman', serif;
        }
        .form-control:focus {
            border-color: #DAA520;
            box-shadow: 0 0 5px rgba(218, 165, 32, 0.3);
        }
        .btn-primary {
            background-color: #DAA520;
            border: none;
            color: black;
            font-weight: bold;
            padding: 10px;
        }
        .btn-primary:hover {
            background-color: #D2B55B;
            color: black;
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
                        <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('auth/register') ?>"><button class="button"> Sign Up</button></a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('auth/login') ?>"><button class="button"> Log-In</button></a></li>                
                        <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('about') ?>"><button class="button"> About Us</button></a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('contact') ?>"><button class="button"> Contact Us</button></a></li>
                    </ul>
                </div>
            </nav>
        </nav>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="mb-0">Student Login</h3>
                            <p class="mb-0 mt-1">Access your LMS account</p>
                        </div>
                        <div class="card-body p-4">
                            <?php if (session()->getFlashdata('success')): ?>
                                <div class="alert alert-success">
                                    <?= session()->getFlashdata('success') ?>
                                </div>
                            <?php endif; ?>

                            <?php if (session()->getFlashdata('error')): ?>
                                <div class="alert alert-danger">
                                    <?= session()->getFlashdata('error') ?>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger">
                                    <?= $error ?>
                                </div>
                            <?php endif; ?>

                            <?= form_open('auth/login') ?>
                                <?= csrf_field() ?>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" 
                                           class="form-control <?= isset($validation) && $validation->hasError('email') ? 'is-invalid' : '' ?>" 
                                           id="email" 
                                           name="email" 
                                           value="<?= old('email') ?>" 
                                           placeholder="Enter your email address"
                                           required>
                                    <?php if (isset($validation) && $validation->hasError('email')): ?>
                                        <div class="text-danger small mt-1">
                                            <?= $validation->getError('email') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" 
                                           class="form-control <?= isset($validation) && $validation->hasError('password') ? 'is-invalid' : '' ?>" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Enter your password"
                                           required>
                                    <?php if (isset($validation) && $validation->hasError('password')): ?>
                                        <div class="text-danger small mt-1">
                                            <?= $validation->getError('password') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-primary">Sign In</button>
                                </div>
                            <?= form_close() ?>

                            <div class="text-center">
                                <p class="mb-0">Don't have an account?</p>
                                <a href="<?= base_url('auth/register') ?>" class="text-decoration-none" style="color: #DAA520;">
                                    Create Account Here
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
