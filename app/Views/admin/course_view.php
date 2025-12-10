<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #e6e3dc; font-family: 'Times New Roman', serif; }
        .topbar { background:#000; color:#fff; padding:.5rem 1rem; font-family: 'Times New Roman', serif; }
        .subbar { background:#DAA520; color:#fff; padding:.5rem 1rem; font-family: 'Times New Roman', serif; }
        .menu a { color:#fff; text-decoration:none; padding:.4rem .8rem; border-radius:.3rem; }
        .menu a.active, .menu a:hover { background: rgba(0,0,0,.15); }
        .logout-btn { background:#E74C3C; color:#fff; border:none; padding:.4rem .8rem; border-radius:.3rem; }
        .section-title { 
            background:#D1A11F; 
            color:#000; 
            padding:.5rem .75rem; 
            font-weight:600; 
            margin:0;
            border:1px solid #dee2e6;
            border-bottom:none;
        }
        .form-container {
            background:#fff;
            border-radius:10px;
            overflow:hidden;
            box-shadow:0 4px 8px rgba(0,0,0,.1);
            border:1px solid #dee2e6;
        }
        .form-section { 
            margin-bottom:0; 
            border-bottom:1px solid #dee2e6;
        }
        .form-section:last-child {
            border-bottom:none;
        }
        .form-section .card-body { 
            padding:1.5rem; 
            border-top:none;
            background:#fff;
        }
        .form-section .card-body .row {
            margin-bottom:0;
        }
    </style>
</head>
<body>
<?= view('templates/header', ['title' => 'View Course']) ?>

<div class="container my-4">
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h4 class="mb-0">View Course Details</h4>
        <a href="<?= site_url('admin/courses') ?>" class="btn btn-sm btn-secondary">Back to Courses</a>
    </div>

    <div class="form-container">
        <!-- Course Information Section -->
        <div class="form-section">
            <div class="section-title"><i class="bi bi-info-circle me-2"></i>Course Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Title</label>
                        <input type="text" class="form-control" value="<?= esc($course['title'] ?? '') ?>" disabled>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Code</label>
                        <input type="text" class="form-control" value="<?= esc($course['code'] ?? '') ?>" disabled>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Department</label>
                        <input type="text" class="form-control" value="<?= esc($course['department'] ?? '') ?>" disabled>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Program</label>
                        <input type="text" class="form-control" value="<?= esc($course['program'] ?? '') ?>" disabled>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructor Information Section -->
        <div class="form-section">
            <div class="section-title"><i class="bi bi-person-badge me-2"></i>Instructor Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Instructor</label>
                        <input type="text" class="form-control" value="<?= esc($course['instructor_name'] ?? 'Instructor ID: ' . ($course['instructor_id'] ?? '')) ?>" disabled>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Details Section -->
        <div class="form-section">
            <div class="section-title"><i class="bi bi-mortarboard me-2"></i>Academic Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Year Level</label>
                        <input type="text" class="form-control" value="<?= esc($course['course_level'] ?? '') ?>" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Semester</label>
                        <input type="text" class="form-control" value="<?= esc($course['semester'] ?? '') ?>" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Unit</label>
                        <input type="text" class="form-control" value="<?= esc($course['unit'] ?? '') ?>" disabled>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Time Span / Schedule Section -->
        <div class="form-section">
            <div class="section-title"><i class="bi bi-calendar-range me-2"></i>Course Time Span / Schedule</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3 fw-bold">Course Dates</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Course Start Date</label>
                                <input type="date" class="form-control" value="<?= esc($course['course_start_date'] ?? '') ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Course End Date</label>
                                <input type="date" class="form-control" value="<?= esc($course['course_end_date'] ?? '') ?>" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3 fw-bold">Enrollment Dates</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Enrollment Start Date</label>
                                <input type="date" class="form-control" value="<?= esc($course['enrollment_start_date'] ?? '') ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Enrollment End Date</label>
                                <input type="date" class="form-control" value="<?= esc($course['enrollment_end_date'] ?? '') ?>" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Class Schedule</label>
                        <input type="text" class="form-control" value="<?= esc($course['class_schedule'] ?? '') ?>" disabled>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Details Section -->
        <div class="form-section">
            <div class="section-title"><i class="bi bi-info-square me-2"></i>Additional Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Academic Year</label>
                        <input type="text" class="form-control" value="<?= esc($course['academic_year'] ?? '') ?>" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Created</label>
                        <input type="text" class="form-control" value="<?= esc($course['created_at'] ?? '') ?>" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Updated</label>
                        <input type="text" class="form-control" value="<?= esc($course['updated_at'] ?? '') ?>" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
