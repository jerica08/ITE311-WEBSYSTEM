<?php helper('url'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Simple Authentication Test</h1>
        
        <div class="row">
            <div class="col-md-6">
                <h3>Test Login</h3>
                <form method="post" action="<?= site_url('/login') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label>Email:</label>
                        <input type="email" name="email" class="form-control" value="admin@kawas.edu" required>
                    </div>
                    <div class="mb-3">
                        <label>Password:</label>
                        <input type="password" name="password" class="form-control" value="admin123" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Test Login</button>
                </form>
            </div>
            
            <div class="col-md-6">
                <h3>Test Registration</h3>
                <form method="post" action="<?= site_url('/register') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label>Username:</label>
                        <input type="text" name="username" class="form-control" value="testuser123" required>
                    </div>
                    <div class="mb-3">
                        <label>First Name:</label>
                        <input type="text" name="first_name" class="form-control" value="Test" required>
                    </div>
                    <div class="mb-3">
                        <label>Last Name:</label>
                        <input type="text" name="last_name" class="form-control" value="User" required>
                    </div>
                    <div class="mb-3">
                        <label>Email:</label>
                        <input type="email" name="email" class="form-control" value="testuser123@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label>Password:</label>
                        <input type="password" name="password" class="form-control" value="test123" required>
                    </div>
                    <div class="mb-3">
                        <label>Confirm Password:</label>
                        <input type="password" name="password_confirm" class="form-control" value="test123" required>
                    </div>
                    <div class="mb-3">
                        <label>Role:</label>
                        <select name="role" class="form-control" required>
                            <option value="student">Student</option>
                            <option value="instructor">Instructor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Test Register</button>
                </form>
            </div>
        </div>
        
        <div class="mt-4">
            <h4>Current Session:</h4>
            <pre><?= print_r(session()->get(), true) ?></pre>
        </div>
        
        <div class="mt-4">
            <h4>Flash Messages:</h4>
            <p><strong>Success:</strong> <?= session()->getFlashdata('success') ?? 'None' ?></p>
            <p><strong>Errors:</strong> <?= print_r(session()->getFlashdata('errors'), true) ?></p>
        </div>
        
        <div class="mt-4">
            <a href="<?= site_url('/login') ?>" class="btn btn-primary">Go to Login</a>
            <a href="<?= site_url('/register') ?>" class="btn btn-secondary">Go to Register</a>
        </div>
    </div>
</body>
</html>
