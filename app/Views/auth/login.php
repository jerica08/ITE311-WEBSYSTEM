<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width", initial-scale="1.0">
        <title>Login - Kawas National High School LMS</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
        <style>
            body {
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                min-height: 100vh;
                font-family: 'Times New Roman', serif;
            }
            .navbar {
                position: fixed;
                top: 0;
                width: 100%;
                z-index: 1000;
            }
            .main-content {
                margin-top: 120px;
                padding: 20px;
            }
            .button {
                background-color: #DAA520;
                color: black;
                border: none;
                padding: 10px 20px;
                cursor: pointer;
                transition: all 0.3s ease;
                border-radius: 8px;
            }
            .button:hover {
                background-color: #D2B55B;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(218, 165, 32, 0.3);
            }
            .card {
                border-radius: 15px;
                overflow: hidden;
            }
            .card-header {
                border-bottom: 3px solid #B8860B;
            }
            .form-control:focus {
                border-color: #DAA520;
                box-shadow: 0 0 0 0.2rem rgba(218, 165, 32, 0.25);
            }
            .form-control {
                border-radius: 8px;
                border: 2px solid #e9ecef;
                transition: all 0.3s ease;
            }
            .form-control:hover {
                border-color: #DAA520;
            }
            .alert {
                border-radius: 8px;
                border: none;
            }
            a {
                text-decoration: none;
                transition: color 0.3s ease;
            }
            a:hover {
                color: #B8860B !important;
            }
            .school-logo {
                width: 60px;
                height: 60px;
                object-fit: contain;
                margin-bottom: 10px;
            }
            .login-card {
                max-width: 450px;
                margin: 0 auto;
            }
            .forgot-password {
                font-size: 0.9rem;
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
                        <a class="navbar-brand text-white" href="<?= site_url('/') ?>"><h2>Learning Management System</h2></a>
                        <ul class="nav d-flex align-items-center gap-3">
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('auth/register') ?>"><button class="button">Sign Up</button></a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('/') ?>"><button class="button">Home</button></a></li>
                        </ul>
                    </div>
                </nav>
            </nav>
        </header>
        
        <main class="main-content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-5">
                        <div class="card shadow-lg border-0 login-card">
                            <div class="card-header text-center" style="background-color: #DAA520; color: black;">
                                <img src="<?= base_url('img/kawas_logo.jpg') ?>" alt="Kawas Logo" class="school-logo">
                                <h3 class="mb-0" style="font-family: 'Times New Roman', serif;">Welcome Back</h3>
                                <p class="mb-0 small">Login in to your LMS account</p>
                            </div>
                            <div class="card-body p-4">
                                <?php if (isset($validation)): ?>
                                    <div class="alert alert-danger">
                                        <?= $validation->listErrors() ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (session()->getFlashdata('error')): ?>
                                    <div class="alert alert-danger">
                                        <?= session()->getFlashdata('error') ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (session()->getFlashdata('success')): ?>
                                    <div class="alert alert-success">
                                        <?= session()->getFlashdata('success') ?>
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="<?= base_url('login') ?>">
                                    <?= csrf_field() ?>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-bold">Email Address</label>
                                        <input type="email" class="form-control form-control-lg" id="email" name="email" 
                                               value="<?= set_value('email') ?>" placeholder="Enter your email address" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label fw-bold">Password</label>
                                        <input type="password" class="form-control form-control-lg" id="password" name="password" 
                                               placeholder="Enter your password" required>
                                    </div>

                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                        <label class="form-check-label" for="remember">
                                            Remember me
                                        </label>
                                    </div>

                                    <div class="d-grid mb-3">
                                        <button type="submit" class="btn btn-lg button" style="background-color: #DAA520; color: black; font-weight: bold; font-family: 'Times New Roman', serif;">
                                            Log In
                                        </button>
                                    </div>
                                </form>

                                <div class="text-center">
                                    <p class="mb-2 forgot-password">
                                        <a href="#" style="color: #DAA520; font-weight: bold;">Forgot your password?</a>
                                    </p>
                                    <hr class="my-3">
                                    <p class="mb-0">Don't have an account? 
                                        <a href="<?= base_url('auth/register') ?>" style="color: #DAA520; font-weight: bold;">Create Account</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
