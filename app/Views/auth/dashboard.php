<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard - Kawas National High School LMS</title>
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
                margin-bottom: 20px;
            }
            .card-header {
                border-bottom: 3px solid #B8860B;
            }
            .alert {
                border-radius: 8px;
                border: none;
            }
            .user-info {
                background: linear-gradient(45deg, #DAA520, #B8860B);
                color: white;
                padding: 20px;
                border-radius: 15px;
                margin-bottom: 20px;
            }
            .role-badge {
                background-color: rgba(255, 255, 255, 0.2);
                padding: 5px 15px;
                border-radius: 20px;
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
                        <a class="navbar-brand text-white" href="#"><h2>Learning Management System</h2></a>
                        <ul class="nav d-flex align-items-center gap-3">
                            <li class="nav-item">
                                <span class="text-white">Welcome, <?= session()->get('name') ?>!</span>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="<?= site_url('logout') ?>">
                                    <button class="button">Logout</button>
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
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-4">
                        <div class="user-info">
                            <h4 class="mb-3">User Information</h4>
                            <p><strong>Name:</strong> <?= session()->get('name') ?></p>
                            <p><strong>Email:</strong> <?= session()->get('email') ?></p>
                            <p><strong>Role:</strong> 
                                <span class="role-badge"><?= ucfirst(session()->get('role')) ?></span>
                            </p>
                            <p class="mb-0"><strong>User ID:</strong> <?= session()->get('userID') ?></p>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="card shadow-lg border-0">
                            <div class="card-header text-center" style="background-color: #DAA520; color: black;">
                                <h3 class="mb-0">Dashboard</h3>
                                <p class="mb-0 small">Welcome to Kawas National High School LMS</p>
                            </div>
                            <div class="card-body p-4">
                                <h5>Welcome to your Learning Management System!</h5>
                                <p>You have successfully logged into the Kawas National High School Learning Management System.</p>
                                
                                <?php if (session()->get('role') === 'student'): ?>
                                    <div class="alert alert-info">
                                        <h6>Student Features:</h6>
                                        <ul class="mb-0">
                                            <li>View your courses and assignments</li>
                                            <li>Submit homework and projects</li>
                                            <li>Check your grades and progress</li>
                                            <li>Communicate with teachers</li>
                                        </ul>
                                    </div>
                                <?php elseif (session()->get('role') === 'teacher'): ?>
                                    <div class="alert alert-warning">
                                        <h6>Teacher Features:</h6>
                                        <ul class="mb-0">
                                            <li>Manage your classes and students</li>
                                            <li>Create and grade assignments</li>
                                            <li>Track student progress</li>
                                            <li>Upload course materials</li>
                                        </ul>
                                    </div>
                                <?php elseif (session()->get('role') === 'admin'): ?>
                                    <div class="alert alert-danger">
                                        <h6>Administrator Features:</h6>
                                        <ul class="mb-0">
                                            <li>Manage users and permissions</li>
                                            <li>Oversee all courses and classes</li>
                                            <li>Generate reports and analytics</li>
                                            <li>System configuration and maintenance</li>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="mt-4">
                                    <p class="text-muted">More features will be available soon. Stay tuned for updates!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
