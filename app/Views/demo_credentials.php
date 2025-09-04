<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Demo Credentials</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
        <style>
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
                transition: background 0.3s;
            }
            .button:hover {
                background-color: #D2B55B;
            }
            .credential-card {
                background: white;
                border-radius: 10px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                border-left: 4px solid #DAA520;
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
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('auth/login') ?>"><button class="button"> Log-In</button></a></li>
                        </ul>
                    </div>
                </nav>
            </nav>
        </header>
        
        <main class="main-content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <h1 class="text-center mb-4" style="font-family: 'Times New Roman', serif;">Demo Login Credentials</h1>
                        
                        <div class="credential-card">
                            <h4>👨‍🎓 Student Account</h4>
                            <p><strong>Email:</strong> student@kawas.edu</p>
                            <p><strong>Password:</strong> student123</p>
                            <p><strong>Dashboard:</strong> Student Dashboard with course management and assignments</p>
                        </div>
                        
                        <div class="credential-card">
                            <h4>👨‍🏫 Instructor Account</h4>
                            <p><strong>Email:</strong> instructor@kawas.edu</p>
                            <p><strong>Password:</strong> instructor123</p>
                            <p><strong>Dashboard:</strong> Instructor Dashboard with course management and student tracking</p>
                        </div>
                        
                        <div class="credential-card">
                            <h4>👨‍💼 Admin Account</h4>
                            <p><strong>Email:</strong> admin@kawas.edu</p>
                            <p><strong>Password:</strong> admin123</p>
                            <p><strong>Dashboard:</strong> Admin Dashboard with system management and user oversight</p>
                        </div>
                        
                        <div class="credential-card">
                            <h4>📚 Additional Test Accounts</h4>
                            <p><strong>Jason (Admin):</strong> jason@lms / jason123</p>
                            <p><strong>Karl (Instructor):</strong> karl@lms / karl123</p>
                            <p><strong>Kendra (Student):</strong> kendra@lms / kendra123</p>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="<?= site_url('auth/login') ?>" class="btn btn-primary btn-lg">Go to Login Page</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
