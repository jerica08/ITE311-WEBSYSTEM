<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Kawas National High School LMS</title>
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
            max-width: 450px;
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
                        <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('/') ?>"><button class="button"> Home</button></a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('register') ?>"><button class="button-active"> Sign Up</button></a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('login') ?>"><button class="button"> Log-In</button></a></li>                
                        <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('about') ?>"><button class="button"> About Us</button></a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="<?= base_url('contact') ?>"><button class="button"> Contact Us</button></a></li>
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
                            <h3 class="mb-0">Student Registration</h3>
                            <p class="mb-0 mt-1">Join Kawas National High School LMS</p>
                        </div>
                        <div class="card-body p-4">
                            <?php if (session()->getFlashdata('success')): ?>
                                <div class="alert alert-success">
                                    <?= session()->getFlashdata('success') ?>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger">
                                    <?= $error ?>
                                </div>
                            <?php endif; ?>

                            <?= form_open('register') ?>
                                <?= csrf_field() ?>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" 
                                           class="form-control <?= isset($validation) && $validation->hasError('name') ? 'is-invalid' : '' ?>" 
                                           id="name" 
                                           name="name" 
                                           value="<?= old('name') ?>" 
                                           placeholder="Enter your full name"
                                           required>
                                    <?php if (isset($validation) && $validation->hasError('name')): ?>
                                        <div class="text-danger small mt-1">
                                            <?= $validation->getError('name') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

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

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" 
                                           class="form-control <?= isset($validation) && $validation->hasError('password') ? 'is-invalid' : '' ?>" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Create a password"
                                           required>
                                    <?php if (isset($validation) && $validation->hasError('password')): ?>
                                        <div class="text-danger small mt-1">
                                            <?= $validation->getError('password') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" 
                                           class="form-control <?= isset($validation) && $validation->hasError('confirm_password') ? 'is-invalid' : '' ?>" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           placeholder="Confirm your password"
                                           required>
                                    <?php if (isset($validation) && $validation->hasError('confirm_password')): ?>
                                        <div class="text-danger small mt-1">
                                            <?= $validation->getError('confirm_password') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-primary">Create Account</button>
                                </div>
                            <?= form_close() ?>

                            <div class="text-center">
                                <p class="mb-0">Already have an account?</p>
                                <a href="<?= base_url('login') ?>" class="text-decoration-none" style="color: #DAA520;">
                                    Sign In Here
                                </a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
