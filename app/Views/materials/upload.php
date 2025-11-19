<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Material</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color:#e6e3dc; font-family:'Times New Roman', serif; }
        .section-title { background:#D1A11F; color:#000; padding:.5rem .75rem; border-radius:8px 8px 0 0; font-weight:600; }
        .card-wrap { border-radius:10px; overflow:hidden; box-shadow:0 8px 16px rgba(0,0,0,.08); background:#fff; }
        .btn-gold { background-color:#DAA520; border:none; color:#000; }
        .btn-gold:hover { background-color:#c9991c; color:#000; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php /* Header view will render top/sub bars and flash messages */ ?>
</head>
<body>
    <?= view('templates/header', ['title' => 'Upload Material']) ?>

    <div class="container my-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="section-title flex-grow-1 me-3"><i class="bi bi-upload me-2"></i>Upload Material<?= isset($course_id) ? ' (Course ID: '.esc($course_id).')' : '' ?></div>
            <a href="<?= site_url((session('role') === 'teacher' || session('role') === 'instructor') ? 'teacher/dashboard' : ((session('role') === 'admin') ? 'admin/dashboard' : 'student/dashboard')) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
            </a>
        </div>
        <div class="card-wrap">
            <div class="card border-0">
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data" action="<?= current_url() ?>" class="row g-3">
                        <?= csrf_field() ?>
                        <div class="col-12">
                            <label for="material" class="form-label">Choose file</label>
                            <input type="file" name="material" id="material" class="form-control" required>
                            <div class="form-text">Allowed: PDF, DOC/DOCX, PPT/PPTX, XLS/XLSX, ZIP, Images, MP4</div>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-gold"><i class="bi bi-cloud-arrow-up me-1"></i>Upload</button>
                            <a href="<?= site_url((session('role') === 'teacher' || session('role') === 'instructor') ? 'teacher/dashboard' : 'admin/dashboard') ?>" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
