<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Department</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #e6e3dc; font-family: 'Times New Roman', serif; }
        .card-wrap { background:#fff; border-radius:12px; border:1px solid #d9c38f; box-shadow:0 6px 16px rgba(0,0,0,.08); }
        .card-header { background:#D1A11F; color:#000; font-weight:600; }
        .label { font-weight:600; color:#4a3b1b; }
        .value-box { background:#f7f2e4; border-radius:8px; padding:.65rem 1rem; border:1px solid #ead9b0; }
    </style>
</head>
<body>
<?= view('templates/header', ['title' => 'View Department']) ?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Department Details</h4>
            <small class="text-muted">Full information of the selected department</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= site_url('admin/departments/' . ($department['id'] ?? 0) . '/edit') ?>" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            <a href="<?= site_url('admin/courses') ?>" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="card card-wrap">
        <div class="card-header"><i class="bi bi-building me-2"></i><?= esc($department['department_name'] ?? 'Department') ?></div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="label">Department Name</div>
                    <div class="value-box"><?= esc($department['department_name'] ?? '-') ?></div>
                </div>
                <div class="col-md-6">
                    <div class="label">Department Code</div>
                    <div class="value-box"><?= esc($department['department_code'] ?? '-') ?></div>
                </div>
                <div class="col-12">
                    <div class="label">Description</div>
                    <div class="value-box"><?= esc($department['description'] ?? 'No description provided.') ?></div>
                </div>
                <div class="col-md-6">
                    <div class="label">Created At</div>
                    <div class="value-box"><?= esc($department['created_at'] ?? '-') ?></div>
                </div>
                <div class="col-md-6">
                    <div class="label">Last Updated</div>
                    <div class="value-box"><?= esc($department['updated_at'] ?? '-') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
