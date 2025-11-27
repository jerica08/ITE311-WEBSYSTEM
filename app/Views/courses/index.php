<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-6">
            <form id="searchForm" class="d-flex" method="get" action="<?= site_url('course/search') ?>">
                <?= csrf_field() ?>
                <div class="input-group">
                    <input type="text" id="searchInput" name="search_term" class="form-control" placeholder="Search courses...">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
