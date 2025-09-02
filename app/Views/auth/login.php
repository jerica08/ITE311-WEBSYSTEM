<?php helper('url'); /** @var \CodeIgniter\Validation\Validation|null $validation */ ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Log In</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
        <style>
            .navbar { position: fixed; top: 0; width: 100%; z-index: 1000; }
            .main-content { margin-top: 20px; padding: 20px; }
            .button { background-color: #DAA520; color: black; border: none; padding: 10px 20px; cursor: pointer; transition: background 0.3s; }
            .button:hover { background-color: #D2B55B; }
            body { background-color: #f8f9fa; font-family: 'Times New Roman', serif; }
            .card { max-width: 520px; margin: 40px auto; }
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
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('auth/register')?>"><button class="button"> Sign Up</button></a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('auth/login')?>"><button class="button"> Log-In</button></a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('about') ?>"><button class="button"> About Us</button></a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('contact') ?>"><button class="button"> Contact Us</button></a></li>
                        </ul>
                    </div>
                </nav>
            </nav>
        </header>

        <main class="main-content">
            <div class="container" style="text-align:center; margin-top: 50px;">
                <h1 style="font-weight:1000; font-family: 'Times New Roman', serif;">Log in to your account</h1>
            </div>

            <div class="container">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success" role="alert">
                                <?= esc(session()->getFlashdata('success')) ?>
                            </div>
                        <?php endif; ?>

                        <?php $errors = session('errors'); ?>
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $msg): ?>
                                        <li><?= esc($msg) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="<?= site_url('login') ?>">
                            <?= csrf_field() ?>

                            <div class="mb-3 text-start">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                            </div>

                            <div class="mb-3 text-start">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <button type="submit" class="button w-100">Log In</button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="<?= site_url('register') ?>">Don’t have an account? Sign up</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>

