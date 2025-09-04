<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Student Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
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
            .dashboard-card {
                background: linear-gradient(135deg, #DAA520, #D2B55B);
                color: white;
                border-radius: 15px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            }
            .stats-card {
                background: white;
                border-radius: 10px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                border-left: 4px solid #DAA520;
            }
            .welcome-section {
                background: linear-gradient(135deg, #000000, #333333);
                color: white;
                border-radius: 15px;
                padding: 30px;
                margin-bottom: 30px;
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
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('dashboard/student') ?>"><button class="button"> Dashboard</button></a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('courses') ?>"><button class="button"> My Courses</button></a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('assignments') ?>"><button class="button"> Assignments</button></a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('profile') ?>"><button class="button"> Profile</button></a></li>
                            <li class="nav-item"><a class="nav-link text-white" href="<?= site_url('auth/logout') ?>"><button class="button"> Logout</button></a></li>
                        </ul>
                    </div>
                </nav>
            </nav>
        </header>
        
        <main class="main-content">
            <div class="container">
                <!-- Welcome Section -->
                <div class="welcome-section">
                    <h1 style="font-weight:1000; font-family: 'Times New Roman', serif;">Welcome, Student!</h1>
                    <p class="lead">Manage your learning journey and track your progress</p>
                </div>

                <!-- Quick Stats -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h5>Enrolled Courses</h5>
                            <h2 class="text-primary">5</h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h5>Assignments Due</h5>
                            <h2 class="text-warning">3</h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h5>Completed</h5>
                            <h2 class="text-success">12</h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h5>Average Grade</h5>
                            <h2 class="text-info">85%</h2>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Cards -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="dashboard-card">
                            <h4><i class="fas fa-book"></i> Recent Courses</h4>
                            <ul class="list-unstyled">
                                <li>• Mathematics 101</li>
                                <li>• Science 102</li>
                                <li>• English 103</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dashboard-card">
                            <h4><i class="fas fa-tasks"></i> Upcoming Assignments</h4>
                            <ul class="list-unstyled">
                                <li>• Math Quiz - Due Tomorrow</li>
                                <li>• Science Project - Due Next Week</li>
                                <li>• English Essay - Due Friday</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="dashboard-card">
                            <h4><i class="fas fa-chart-line"></i> Recent Activity</h4>
                            <p>You completed the Mathematics Chapter 5 quiz with a score of 92%</p>
                            <p>New assignment posted in Science 102</p>
                            <p>Grade posted for English Essay</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
