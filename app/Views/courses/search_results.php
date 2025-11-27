<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Search Results</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container py-5">
        <h2 class="mb-3">Search results for <?= esc($searchTerm ?: 'all courses') ?></h2>
        <form method="get" action="<?= site_url('course/search') ?>" class="mb-4">
            <?= csrf_field() ?>
            <div class="input-group">
                <input type="text" name="search_term" class="form-control" placeholder="Search by title or description" value="<?= esc($searchTerm) ?>">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>

        <?php if (!empty($courses)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Instructor</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $index => $course): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= esc($course['title'] ?? '') ?></td>
                                <td><?= esc(substr($course['description'] ?? '', 0, 80)) ?><?= !empty($course['description']) && strlen($course['description']) > 80 ? '...' : '' ?></td>
                                <td><?= esc($course['instructor_id'] ?? '') ?></td>
                                <td><?= esc($course['created_at'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No courses matched your search.</div>
        <?php endif; ?>
    </div>
</body>
</html>
