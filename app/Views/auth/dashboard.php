<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
        <style>
            .button { background-color: #DAA520; color: black; border: none; padding: 10px 20px; cursor: pointer; transition: background 0.3s; }
            .button:hover { background-color: #D2B55B; }
            body { background-color: #f8f9fa; font-family: 'Times New Roman', serif; }
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
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('logout')?>"><button class="button"> Log Out</button></a></li>
                        </ul>
                    </div>
                </nav>
            </nav>
        </header>

        <main class="container mt-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="mb-2">Welcome, <?= esc($name ?? 'User') ?>!</h3>
                    <p class="mb-0">Your role: <strong><?= esc($role ?? 'student') ?></strong></p>
                </div>
            </div>
        </main>
    </body>
</html>

