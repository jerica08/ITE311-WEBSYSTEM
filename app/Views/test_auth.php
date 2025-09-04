<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Authentication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Authentication Test</h1>
        
        <?php
        use App\Models\UserModel;
        
        $userModel = new UserModel();
        
        // Test database connection
        try {
            $users = $userModel->findAll();
            echo "<div class='alert alert-success'>Database connection successful! Found " . count($users) . " users.</div>";
            
            // Test authentication
            $testEmail = 'admin@kawas.edu';
            $testPassword = 'admin123';
            
            $user = $userModel->authenticate($testEmail, $testPassword);
            
            if ($user) {
                echo "<div class='alert alert-success'>Authentication test successful for $testEmail!</div>";
                echo "<pre>" . print_r($user, true) . "</pre>";
            } else {
                echo "<div class='alert alert-danger'>Authentication test failed for $testEmail!</div>";
                
                // Check if user exists
                $userExists = $userModel->where('email', $testEmail)->first();
                if ($userExists) {
                    echo "<div class='alert alert-warning'>User exists but password verification failed.</div>";
                    echo "<p>Stored password hash: " . substr($userExists['password'], 0, 30) . "...</p>";
                } else {
                    echo "<div class='alert alert-warning'>User does not exist in database.</div>";
                }
            }
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Database error: " . $e->getMessage() . "</div>";
        }
        ?>
        
        <div class="mt-3">
            <a href="<?= site_url('auth/login') ?>" class="btn btn-primary">Go to Login</a>
            <a href="<?= site_url('auth/register') ?>" class="btn btn-secondary">Go to Register</a>
        </div>
    </div>
</body>
</html>
