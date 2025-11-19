<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <?= view('templates/header', ['title' => 'Announcements']) ?>
<div class="container my-4">
    <h1 class="mb-3">Announcements</h1>

    <?php if (!empty($announcements)): ?>
        <div class="list-group">
            <?php foreach ($announcements as $a): ?>
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><?= esc($a['title'] ?? '') ?></h5>
                        <small class="text-muted"><?= esc($a['created_at'] ?? '') ?></small>
                    </div>
                    <p class="mb-1"><?= esc($a['content'] ?? '') ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No announcements yet.</div>
    <?php endif; ?>
</div>
</body>
</html>
