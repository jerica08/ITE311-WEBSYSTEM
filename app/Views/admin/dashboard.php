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
            background-color: #e0dfdc;
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
            background:#DAA520;
            color: black;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            
        }
        .stats-card {
            background: rgb(255, 255, 255);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-icon {
            font-size: 3rem;
            color: #DAA520;
            margin-bottom: 1rem;
        }
        .school-logo {
            max-width: 80px;
            height: auto;
            margin-right: 20px;
        }
        .user-avatar {
            width: 80px;
            height: 80px;
            background-color: #DAA520;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: black;
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
        .link-card {
            background: white;
            border-radius: 15px;
            padding: 1.25rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            height: 100%;
        }
        .link-card .btn {
            background-color: #DAA520;
            color: black;
            border: none;
        }
        .link-card .btn:hover {
            background-color: #B8860B;
            color: white;
        }
        .table thead th {
            background-color: #f1e4b3;
        }
    </style>
</head>
<body>
    <?= view('templates/header') ?>

    <main class="main-content">
        <div class="container">

             <!--Welcome Section -->
            <div class="welcome-card">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        <img src="../public/img/kawas_logo.jpg" alt="Kawas Logo" class="school-logo">
                    </div>
                    <div class="col-md-8">
                        <h2 class="mb-2">
                            Welcome to your Dashboard, <?= esc($user['name']) ?>!
                        </h2>
                        <p class="mb-0 fs-5">Kawas National High School Learning Management System</p>
                        <small class="opacity-75">Role: <?= ucfirst(esc($user['role'])) ?> | Email: <?= esc($user['email']) ?></small>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="user-avatar">
                            <?= strtoupper(substr($user['name'], 0, 2)) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="stats-card">
                        <div class="stats-icon"><i class="fas fa-users"></i></div>
                        <h5 class="mb-1">Total Users</h5>
                        <h2 class="fw-bold mb-0">
                           
                        </h2>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stats-card">
                        <div class="stats-icon"><i class="fas fa-book"></i></div>
                        <h5 class="mb-1">Total Courses</h5>
                        <h2 class="fw-bold mb-0">
                            
                        </h2>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-clock me-2"></i> Recent Activity
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Time</th>
                                    <th scope="col">User</th>
                                    <th scope="col">Action</th>
                                    <th scope="col">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentActivities) && is_array($recentActivities)): ?>
                                    <?php foreach ($recentActivities as $activity): ?>
                                        <tr>
                                            <td><?= esc($activity['time'] ?? '') ?></td>
                                            <td><?= esc($activity['user'] ?? '') ?></td>
                                            <td><?= esc($activity['action'] ?? '') ?></td>
                                            <td><?= esc($activity['details'] ?? '') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="far fa-smile me-2"></i>No recent activity to display.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</body>
</html>
