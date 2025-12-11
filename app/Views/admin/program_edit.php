<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Program</title>
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
<?= view('templates/header', ['title' => 'Edit Program']) ?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Edit Program</h4>
            <small class="text-muted">Update the information for this program</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= site_url('admin/programs/' . ($program['id'] ?? 0)) ?>" class="btn btn-sm btn-outline-secondary">View</a>
            <a href="<?= site_url('admin/courses') ?>" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>

    <div class="card card-wrap">
        <div class="card-header">Program Information</div>
        <div class="card-body">
            <form method="post" action="<?= site_url('admin/programs/' . ($program['id'] ?? 0) . '/update') ?>">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Program Name<span class="text-danger">*</span></label>
                        <input type="text" name="program_name" class="form-control" required value="<?= esc($program['program_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Program Code</label>
                        <input type="text" name="program_code" class="form-control" value="<?= esc($program['program_code'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Department<span class="text-danger">*</span></label>
                        <select name="department" class="form-select" required>
                            <option value="">Select department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= esc($dept['department_name']) ?>" <?= ($dept['department_name'] ?? '') === ($program['department'] ?? '') ? 'selected' : '' ?>>
                                    <?= esc($dept['department_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Duration (Years)</label>
                        <input type="number" name="duration" class="form-control" min="1" max="10" value="<?= esc($program['duration'] ?? 4) ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Describe the program..."><?= esc($program['description'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary" style="background:#D1A11F;border:none;color:#000;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
