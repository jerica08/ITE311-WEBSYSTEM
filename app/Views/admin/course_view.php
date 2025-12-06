<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?= view('templates/header', ['title' => 'View Course']) ?>

<div class="container my-4">
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h4 class="mb-0">View Course</h4>
        <a href="<?= site_url('admin/courses') ?>" class="btn btn-sm btn-secondary">Back to Courses</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" value="<?= esc($course['title'] ?? '') ?>" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Code</label>
                    <input type="text" class="form-control" value="<?= esc($course['code'] ?? '') ?>" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Unit</label>
                    <input type="text" class="form-control" value="<?= esc($course['unit'] ?? '') ?>" disabled>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Academic Year</label>
                    <input type="text" class="form-control" value="<?= esc($course['academic_year'] ?? '') ?>" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" value="<?= esc($course['start_date'] ?? '') ?>" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" value="<?= esc($course['end_date'] ?? '') ?>" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Instructor ID</label>
                    <input type="text" class="form-control" value="<?= esc($course['instructor_id'] ?? '') ?>" disabled>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Created</label>
                    <input type="text" class="form-control" value="<?= esc($course['created_at'] ?? '') ?>" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Updated</label>
                    <input type="text" class="form-control" value="<?= esc($course['updated_at'] ?? '') ?>" disabled>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
