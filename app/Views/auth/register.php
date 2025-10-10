<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Kawas National High School LMS</title>
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
            max-width: 500px;
            margin: 0 auto;
        }
        .card-header {
            background-color: #DAA520;
            border-radius: 15px 15px 0 0 !important;
            color: black;
            text-align: center;
            padding: 2rem 1rem;
            font-family: 'Times New Roman', serif;
        }
        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
            font-family: 'Times New Roman', serif;
        }
        .form-control:focus {
            border-color: #DAA520;
            box-shadow: 0 0 0 0.2rem rgba(218, 165, 32, 0.25);
        }
        .btn-primary {
            background-color: #DAA520;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: black;
            font-family: 'Times New Roman', serif;
        }
        .btn-primary:hover {
            background-color: #D2B55B;
            color: black;
            transform: translateY(-1px);
        }
        .input-group-text {
            background-color: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 8px 0 0 8px;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 8px 8px 0;
        }
        .alert {
            border-radius: 8px;
        }
        .form-label {
            font-family: 'Times New Roman', serif;
            font-weight: bold;
        }
        .school-logo {
            max-width: 60px;
            height: auto;
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navigationbar">
            <nav class="text d-flex align-items-center" style="background-color:#000000;padding: 10px;">
                <p><h2 style="color: white;text-align:left;margin-bottom:none;font-family: 'Times New Roman', serif;">Kawas National University</h2></p>
            </nav>  
            <nav class="btm-navbar" style="background-color:#DAA520;font-family: 'Times New Roman', serif;">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                    <a class="navbar-brand text-white" href="#"><h4>Learning Management System</h4></a>
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
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-center">
                                <div>
                                    <h3 class="mb-0">
                                        <i class="fas fa-user-plus me-2"></i>
                                        Student Registration
                                    </h3>
                                    <p class="mb-0 mt-1">Join Kawas National University LMS</p>
                                </div>
                            </div>
                        </div>
                    <div class="card-body p-4">
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= session()->getFlashdata('success') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?= form_open('auth/register', ['class' => 'needs-validation', 'novalidate' => true]) ?>
                            <?= csrf_field() ?>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Full Name</label>
                                <input type="text" 
                                       class="form-control <?= isset($validation) && $validation->hasError('name') ? 'is-invalid' : '' ?>" 
                                       id="name" 
                                       name="name" 
                                       value="<?= old('name') ?>" 
                                       placeholder="Enter your full name"
                                       required>
                                <?php if (isset($validation) && $validation->hasError('name')): ?>
                                    <div class="invalid-feedback d-block">
                                        <?= $validation->getError('name') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Email Address</label>
                                <input type="email" 
                                       class="form-control <?= isset($validation) && $validation->hasError('email') ? 'is-invalid' : '' ?>" 
                                       id="email" 
                                       name="email" 
                                       value="<?= old('email') ?>" 
                                       placeholder="Enter your email address"
                                       required>
                                <?php if (isset($validation) && $validation->hasError('email')): ?>
                                    <div class="invalid-feedback d-block">
                                        <?= $validation->getError('email') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <div class="mb-3">
                            <label for="role" class="form-label fw-bold">Select Role</label>
                            <select 
                                class="form-control <?= isset($validation) && $validation->hasError('role') ? 'is-invalid' : '' ?>"
                                id="role"
                                name="role"
                                required>
                                <option value="" disabled <?= old('role') === null || old('role') === '' ? 'selected' : '' ?>>Select a role</option>
                                <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="teacher" <?= old('role') === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                                <option value="student" <?= old('role') === 'student' ? 'selected' : '' ?>>Student</option>
                            </select>
                            <?php if (isset($validation) && $validation->hasError('role')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= $validation->getError('role') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Password</label>
                                <input type="password" 
                                       class="form-control <?= isset($validation) && $validation->hasError('password') ? 'is-invalid' : '' ?>" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Create a strong password"
                                       required>
                                <?php if (isset($validation) && $validation->hasError('password')): ?>
                                    <div class="invalid-feedback d-block">
                                        <?= $validation->getError('password') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-4">
                                <label for="confirm_password" class="form-label fw-bold">Confirm Password</label>
                                <input type="password" 
                                       class="form-control <?= isset($validation) && $validation->hasError('confirm_password') ? 'is-invalid' : '' ?>" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       placeholder="Confirm your password"
                                       required>
                                <?php if (isset($validation) && $validation->hasError('confirm_password')): ?>
                                    <div class="invalid-feedback d-block">
                                        <?= $validation->getError('confirm_password') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">Create Account</button>
                            </div>
                        <?= form_close() ?>

                        <div class="text-center">
                            <p class="mb-0" style="font-family: 'Times New Roman', serif;">Already have an account?</p>
                            <a href="<?= base_url('auth/login') ?>" class="text-decoration-none fw-bold" style="color: #DAA520; font-family: 'Times New Roman', serif;">
                                Sign In Here
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <script>
        // Bootstrap form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
