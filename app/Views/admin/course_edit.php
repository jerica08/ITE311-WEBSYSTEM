<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?= view('templates/header', ['title' => 'Edit Course']) ?>
<div class="container my-4">
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Edit Course</h4>
        <a href="<?= site_url('admin/courses') ?>" class="btn btn-sm btn-secondary">Back to Courses</a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="post" action="<?= site_url('admin/courses/' . (int)($course['id'] ?? 0) . '/update') ?>" class="row g-3">
                <?= csrf_field() ?>
                <div class="col-md-6">
                    <label class="form-label">Title<span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="<?= esc($course['title'] ?? '') ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Code</label>
                    <input type="text" name="code" class="form-control" value="<?= esc($course['code'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Unit</label>
                    <input type="number" name="unit" class="form-control" min="0" max="10" value="<?= esc($course['unit'] ?? '') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Academic Year</label>
                    <input type="text" name="academic_year" class="form-control" value="<?= esc($course['academic_year'] ?? '') ?>" placeholder="e.g., 2024-2025">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="<?= esc($course['start_date'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="<?= esc($course['end_date'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Instructor</label>
                    <select name="instructor_id" class="form-select">
                        <option value="">Select instructor</option>
                        <?php if (!empty($teachers ?? [])): ?>
                            <?php foreach ($teachers as $t): ?>
                                <option value="<?= (int)($t['id'] ?? 0) ?>" <?= ((int)($course['instructor_id'] ?? 0) === (int)($t['id'] ?? 0)) ? 'selected' : '' ?> >
                                    <?= esc($t['name'] ?? '') ?> (<?= esc($t['email'] ?? '') ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary" style="background-color:#DAA520;border:none;color:#000">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
