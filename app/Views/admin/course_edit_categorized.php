<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #e6e3dc; font-family: 'Times New Roman', serif; }
        .topbar { background:#000; color:#fff; padding:.5rem 1rem; font-family: 'Times New Roman', serif; }
        .subbar { background:#DAA520; color:#fff; padding:.5rem 1rem; font-family: 'Times New Roman', serif; }
        .menu a { color:#fff; text-decoration:none; padding:.4rem .8rem; border-radius:.3rem; }
        .menu a.active, .menu a:hover { background: rgba(0,0,0,.15); }
        .logout-btn { background:#E74C3C; color:#fff; border:none; padding:.4rem .8rem; border-radius:.3rem; }
        .section-title { background:#D1A11F; color:#000; padding:.5rem .75rem; border-radius:8px 8px 0 0; font-weight:600; }
        .form-section { border-radius:10px; overflow:hidden; box-shadow:0 8px 16px rgba(0,0,0,.08); background:#fff; margin-bottom:1.5rem; }
        .form-section .card-body { padding:1.5rem; }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="container-fluid fw-bold">Kawas National High School</div>
    </div>
    <div class="subbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="fw-bold">Learning Management System</div>
            <div class="menu d-flex align-items-center gap-2">
                <a href="<?= site_url('admin/dashboard') ?>">Dashboard</a>
                <a href="<?= site_url('admin/users') ?>">User Management</a>
                <a href="<?= site_url('admin/courses') ?>" class="active">Course Management</a>
                <a href="<?= site_url('logout') ?>" class="btn btn-sm logout-btn">Logout</a>
            </div>
        </div>
    </div>

<div class="container my-4">
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Edit Course</h4>
        <a href="<?= site_url('admin/courses') ?>" class="btn btn-sm btn-secondary">Back to Courses</a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('admin/courses/' . (int)($course['id'] ?? 0) . '/update') ?>">
        <?= csrf_field() ?>

        <!-- Course Information Section -->
        <div class="form-section">
            <div class="section-title"><i class="bi bi-info-circle me-2"></i>Course Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Course Title<span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="<?= esc($course['title'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Course Code</label>
                        <input type="text" name="code" class="form-control" value="<?= esc($course['code'] ?? '') ?>" placeholder="e.g., MATH101">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Units</label>
                        <input type="number" name="unit" class="form-control" min="0" max="10" value="<?= esc($course['unit'] ?? '') ?>">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?= esc($course['description'] ?? '') ?></textarea>
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
                        <label class="form-label">Instructor<span class="text-danger">*</span></label>
                        <select name="instructor_id" class="form-select" required>
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
                </div>
            </div>
        </div>

        <!-- Academic Details Section -->
        <div class="form-section">
            <div class="section-title"><i class="bi bi-mortarboard me-2"></i>Academic Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Course Level</label>
                        <select name="course_level" class="form-select">
                            <option value="">Select level</option>
                            <option value="1st Year" <?= (isset($course['course_level']) && $course['course_level'] === '1st Year') ? 'selected' : '' ?>>1st Year</option>
                            <option value="2nd Year" <?= (isset($course['course_level']) && $course['course_level'] === '2nd Year') ? 'selected' : '' ?>>2nd Year</option>
                            <option value="3rd Year" <?= (isset($course['course_level']) && $course['course_level'] === '3rd Year') ? 'selected' : '' ?>>3rd Year</option>
                            <option value="4th Year" <?= (isset($course['course_level']) && $course['course_level'] === '4th Year') ? 'selected' : '' ?>>4th Year</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Department</label>
                        <select name="department" class="form-select">
                            <option value="">Select department</option>
                            <option value="Mathematics" <?= (isset($course['department']) && $course['department'] === 'Mathematics') ? 'selected' : '' ?>>Mathematics</option>
                            <option value="Science" <?= (isset($course['department']) && $course['department'] === 'Science') ? 'selected' : '' ?>>Science</option>
                            <option value="English" <?= (isset($course['department']) && $course['department'] === 'English') ? 'selected' : '' ?>>English</option>
                            <option value="Filipino" <?= (isset($course['department']) && $course['department'] === 'Filipino') ? 'selected' : '' ?>>Filipino</option>
                            <option value="Social Studies" <?= (isset($course['department']) && $course['department'] === 'Social Studies') ? 'selected' : '' ?>>Social Studies</option>
                            <option value="Physical Education" <?= (isset($course['department']) && $course['department'] === 'Physical Education') ? 'selected' : '' ?>>Physical Education</option>
                            <option value="Arts" <?= (isset($course['department']) && $course['department'] === 'Arts') ? 'selected' : '' ?>>Arts</option>
                            <option value="Technology" <?= (isset($course['department']) && $course['department'] === 'Technology') ? 'selected' : '' ?>>Technology</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Academic Year</label>
                        <input type="text" name="academic_year" class="form-control" value="<?= esc($course['academic_year'] ?? '') ?>" placeholder="e.g., 2024-2025">
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Time Span Section -->
        <div class="form-section">
            <div class="section-title"><i class="bi bi-calendar-range me-2"></i>Course Time Span / Schedule</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Course Duration</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Course Start Date</label>
                                <input type="date" name="course_start_date" class="form-control" value="<?= esc($course['course_start_date'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Course End Date</label>
                                <input type="date" name="course_end_date" class="form-control" value="<?= esc($course['course_end_date'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Enrollment Period</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Enrollment Start Date</label>
                                <input type="date" name="enrollment_start_date" class="form-control" value="<?= esc($course['enrollment_start_date'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Enrollment End Date</label>
                                <input type="date" name="enrollment_end_date" class="form-control" value="<?= esc($course['enrollment_end_date'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Class Schedule (optional)</label>
                        <input type="text" name="class_schedule" class="form-control" value="<?= esc($course['class_schedule'] ?? '') ?>" placeholder="e.g., Tues & Thurs, 1:00 PM - 3:00 PM">
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary" style="background-color:#DAA520;border:none;color:#000">Save Changes</button>
            <a href="<?= site_url('admin/courses') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
