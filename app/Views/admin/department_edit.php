<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Department</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color:#e6e3dc; font-family: 'Times New Roman', serif; }
        .card-wrap { background:#fff; border-radius:12px; border:1px solid #d9c38f; box-shadow:0 6px 16px rgba(0,0,0,.08); }
        .card-header { background:#D1A11F; color:#000; font-weight:600; }
        label { font-weight:600; color:#4a3b1b; }
        .form-control, .form-select { border:1px solid #d6c396; }
    </style>
</head>
<body>
<?= view('templates/header', ['title' => 'Edit Department']) ?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Edit Department</h4>
            <small class="text-muted">Update the information for this department</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= site_url('admin/departments/' . ($department['id'] ?? 0)) ?>" class="btn btn-sm btn-outline-secondary">View</a>
            <a href="<?= site_url('admin/courses') ?>" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>

    <div class="card card-wrap">
        <div class="card-header">Department Information</div>
        <div class="card-body">
            <form method="post" action="<?= site_url('admin/departments/' . ($department['id'] ?? 0) . '/update') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Department Name<span class="text-danger">*</span></label>
                    <input type="text" name="department_name" class="form-control" required value="<?= esc($department['department_name'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Department Code</label>
                    <input type="text" name="department_code" class="form-control" value="<?= esc($department['department_code'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Describe the department..."><?= esc($department['description'] ?? '') ?></textarea>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary" style="background:#D1A11F;border:none;color:#000;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
