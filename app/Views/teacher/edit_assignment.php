<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Assignment - Teacher Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #e6e3dc;
            font-family: 'Times New Roman', serif;
        }
        .welcome-card { 
            background:#D1A11F; 
            color:#000; 
            border:none; 
            border-radius:16px; 
        }
        .section-title { 
            background:#D1A11F; 
            color:#000; 
            padding:.5rem .75rem; 
            border-radius:8px 8px 0 0;
            font-weight:600; 
        }
        .modal-header {
            background-color: #D1A11F;
            color: #000;
            border-bottom: 2px solid #DAA520;
        }
        .modal-title {
            color: #000;
        }
        .btn-close {
            background-color: transparent;
            border: none;
            color: #000;
        }
        .btn-primary {
            background-color: #D1A11F;
            border-color: #D1A11F;
            color: #000;
        }
        .btn-primary:hover {
            background-color: #B8941F;
            border-color: #B8941F;
            color: #000;
        }
    </style>
</head>
<body>
    <?= view('templates/header') ?>

    <div class="container my-4">
        <!-- Edit Assignment Header -->
        <div class="section-title mb-3">
            <i class="bi bi-pencil-square me-2"></i>Edit Assignment
            <a href="<?= site_url('teacher/assignments') ?>" class="btn btn-sm btn-light float-end">
                <i class="bi bi-arrow-left me-1"></i>Back to Assignments
            </a>
        </div>

        <!-- Edit Assignment Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="post" action="<?= site_url('teacher/assignments/update/' . $assignment->id) ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Course<span class="text-danger">*</span></label>
                            <select name="course_id" class="form-select" required>
                                <option value="">Select course</option>
                                <?php foreach ($courses ?? [] as $course): ?>
                                    <option value="<?= $course['id'] ?>" <?= ($course['id'] == $assignment->course_id) ? 'selected' : '' ?>>
                                        <?= esc($course['title']) ?> (<?= esc($course['code']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Assignment Title<span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required 
                                   value="<?= esc($assignment->title) ?>">
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Description<span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="4" required><?= esc($assignment->description) ?></textarea>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Due Date<span class="text-danger">*</span></label>
                            <input type="datetime-local" name="due_date" class="form-control" required 
                                   value="<?= esc($assignment->due_date) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Max Score</label>
                            <input type="number" name="max_score" class="form-control" min="1" max="1000" 
                                   value="<?= esc($assignment->max_score) ?>">
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Attachment File</label>
                            <input type="file" name="attachment" class="form-control" 
                                   accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                            <small class="text-muted">
                                <?php if (!empty($assignment->attachment_file)): ?>
                                    Current file: <?= esc($assignment->attachment_file) ?>. 
                                    Upload a new file to replace it, or leave empty to keep current file.
                                <?php else: ?>
                                    No current attachment. Upload a file to add one.
                                <?php endif; ?>
                                <br>Supported formats: PDF, DOC, DOCX, TXT, JPG, JPEG, PNG (Max size: 10MB)
                            </small>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" <?= ($assignment->status == 'active') ? 'selected' : '' ?>>Active</option>
                                <option value="draft" <?= ($assignment->status == 'draft') ? 'selected' : '' ?>>Draft</option>
                                <option value="archived" <?= ($assignment->status == 'archived') ? 'selected' : '' ?>>Archived</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= site_url('teacher/assignments') ?>" class="btn btn-secondary">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Update Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
