<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color:#e6e3dc; font-family:'Times New Roman',serif; }
        .card-profile { background:#D1A11F; border:none; border-radius:16px; }
        .card-profile .label { font-weight:600; }
        .info-card { border:none; border-radius:12px; box-shadow:0 8px 16px rgba(0,0,0,.08); }
    </style>
</head>
<body>
    <?= view('templates/header', ['title' => 'My Profile']) ?>

    <div class="container my-4">
        <div class="card card-profile mb-4 px-3 py-3">
            <h5 class="mb-1">My Profile</h5>
            <div class="small">Kawas National University Learning Management System</div>
        </div>

        <div class="card info-card p-3">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-2"><span class="label">Name:</span> <?= esc($user['name'] ?? '') ?></div>
                    <div class="mb-2"><span class="label">Email:</span> <?= esc($user['email'] ?? '') ?></div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2"><span class="label">Role:</span> <?= esc(ucfirst((string)($user['role'] ?? ''))) ?></div>
                    <div class="mb-2"><span class="label">Joined:</span> <?= esc($user['created_at'] ?? '-') ?></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
