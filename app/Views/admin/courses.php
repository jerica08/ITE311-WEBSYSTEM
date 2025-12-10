<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { background-color: #e6e3dc; font-family: 'Times New Roman', serif; }
        .topbar { background:#000; color:#fff; padding:.5rem 1rem; font-family: 'Times New Roman', serif; }
        .subbar { background:#DAA520; color:#fff; padding:.5rem 1rem; font-family: 'Times New Roman', serif; }
        .menu a { color:#fff; text-decoration:none; padding:.4rem .8rem; border-radius:.3rem; }
        .menu a.active, .menu a:hover { background: rgba(0,0,0,.15); }
        .logout-btn { background:#E74C3C; color:#fff; border:none; padding:.4rem .8rem; border-radius:.3rem; }
        .section-title { background:#D1A11F; color:#000; padding:.5rem .75rem; border-radius:8px 8px 0 0; font-weight:600; }
        .table-wrap { border-radius:10px; overflow:hidden; box-shadow:0 8px 16px rgba(0,0,0,.08); background:#fff; }
        .table thead th { background:#f8f9fa; }
        .form-container {
            background:#fff;
            border-radius:10px;
            overflow:hidden;
            box-shadow:0 4px 8px rgba(0,0,0,.1);
            border:1px solid #dee2e6;
            margin-bottom:1.5rem;
        }
        .form-section { 
            margin-bottom:0; 
            border-bottom:1px solid #dee2e6;
        }
        .form-section:last-child {
            border-bottom:none;
        }
        .form-section .section-title { 
            background:#D1A11F; 
            color:#000; 
            padding:.5rem .75rem; 
            font-weight:600; 
            margin:0;
            border:1px solid #dee2e6;
            border-bottom:none;
            border-radius:0;
        }
        .form-section .card-body { 
            padding:1.5rem; 
            border-top:none;
            background:#fff;
        }
        .form-section .card-body .row {
            margin-bottom:0;
        }
        /* Modal specific styles */
        .modal .form-section {
            border:1px solid #dee2e6;
            border-radius:8px;
            overflow:hidden;
            margin-bottom:1rem;
        }
        .modal .form-section .section-title {
            border-radius:0;
            border:1px solid #dee2e6;
            border-bottom:none;
        }
        .modal .form-section .card-body {
            border:none;
            padding:1rem;
        }
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
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Course Management Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Course Management</h4>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" style="background-color:#DAA520;border:none;color:#000" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                    <i class="bi bi-building me-2"></i>Add Department
                </button>
                <button type="button" class="btn btn-primary" style="background-color:#DAA520;border:none;color:#000" data-bs-toggle="modal" data-bs-target="#addProgramModal">
                    <i class="bi bi-book me-2"></i>Add Program
                </button>
                <button type="button" class="btn btn-primary" style="background-color:#DAA520;border:none;color:#000" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                    <i class="bi bi-plus-circle me-2"></i>Add New Course
                </button>
            </div>
        </div>

        <!-- Courses Table -->
        <div class="table-wrap mb-4">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Title</th>
                        <th style="width:100px;">Code</th>
                        <th style="width:100px;">Level</th>
                        <th style="width:100px;">Department</th>
                        <th style="width:100px;">Status</th>
                        <th style="width:140px;">Instrct. ID</th>
                        <th style="width:180px;">Created</th>
                        <th style="width:140px;">View Students</th>
                        <th style="width:160px;">Upload Materials</th>
                        <th style="width:160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($courses ?? [])): ?>
                        <?php foreach ($courses as $i => $c): ?>
                            <?php $cid = (int)($c['id'] ?? 0); ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td><?= esc($c['title'] ?? '') ?></td>
                                <td><?= esc($c['code'] ?? '') ?></td>
                                <td><?= esc($c['course_level'] ?? '-') ?></td>
                                <td><?= esc($c['department'] ?? '-') ?></td>
                                <td>
                                    <?php 
                                    $status = $c['status'] ?? 'draft';
                                    if ($status === 'published'): ?>
                                        <span class="badge bg-success">Published</span>
                                    <?php elseif ($status === 'draft'): ?>
                                        <span class="badge bg-warning text-dark">Draft</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Archived</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($c['instructor_id'] ?? '') ?></td>
                                <td><?= esc($c['created_at'] ?? '') ?></td>
                                <td>
                                    <a class="btn btn-sm btn-outline-success m-0" href="<?= site_url('admin/courses/' . $cid . '/students') ?>">View Students</a>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-outline-warning m-0" href="<?= site_url('admin/course/' . $cid . '/upload') ?>">Upload Materials</a>
                                </td>
                                <td class="d-flex gap-1 align-items-center flex-wrap">
                                    <?php if ($status === 'draft'): ?>
                                        <a class="btn btn-sm btn-success m-0" href="<?= site_url('admin/courses/' . $cid . '/approve') ?>" onclick="return confirm('Approve this course?')">Approve</a>
                                        <a class="btn btn-sm btn-danger m-0" href="<?= site_url('admin/courses/' . $cid . '/reject') ?>" onclick="return confirm('Reject this course?')">Reject</a>
                                    <?php endif; ?>
                                    <a class="btn btn-sm btn-outline-secondary m-0" href="<?= site_url('admin/courses/' . $cid) ?>">View</a>
                                    <a class="btn btn-sm btn-outline-primary m-0" href="<?= site_url('admin/courses/' . $cid . '/edit') ?>">Edit</a>
                                    <form method="post" action="<?= site_url('admin/courses/' . $cid . '/delete') ?>" onsubmit="return confirm('Delete this course?');" class="m-0">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger m-0">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-muted">No courses found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Departments Table -->
        <div class="mb-2 section-title"><i class="bi bi-building me-2"></i>Departments</div>
        <div class="table-wrap mb-4">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Department Name</th>
                        <th style="width:100px;">Code</th>
                        <th>Description</th>
                        <th style="width:180px;">Created</th>
                        <th style="width:160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($departments ?? [])): ?>
                        <?php foreach ($departments as $i => $dept): ?>
                            <?php $deptId = (int)($dept['id'] ?? 0); ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td><?= esc($dept['department_name'] ?? '') ?></td>
                                <td><?= esc($dept['department_code'] ?? '-') ?></td>
                                <td><?= esc($dept['description'] ?? '-') ?></td>
                                <td><?= esc($dept['created_at'] ?? '') ?></td>
                                <td class="d-flex gap-1">
                                    <a class="btn btn-sm btn-outline-secondary m-0" href="<?= site_url('admin/departments/' . $deptId) ?>">View</a>
                                    <a class="btn btn-sm btn-outline-primary m-0" href="<?= site_url('admin/departments/' . $deptId . '/edit') ?>">Edit</a>
                                    <form method="post" action="<?= site_url('admin/departments/' . $deptId . '/delete') ?>" onsubmit="return confirm('Delete this department?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger m-0">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-muted">No departments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Programs Table -->
        <div class="mb-2 section-title"><i class="bi bi-book me-2"></i>Programs</div>
        <div class="table-wrap">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Program Name</th>
                        <th style="width:100px;">Code</th>
                        <th style="width:200px;">Department</th>
                        <th style="width:80px;">Duration</th>
                        <th style="width:180px;">Created</th>
                        <th style="width:160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($programs ?? [])): ?>
                        <?php foreach ($programs as $i => $prog): ?>
                            <?php $progId = (int)($prog['id'] ?? 0); ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td><?= esc($prog['program_name'] ?? '') ?></td>
                                <td><?= esc($prog['program_code'] ?? '-') ?></td>
                                <td><?= esc($prog['department'] ?? '') ?></td>
                                <td><?= esc($prog['duration'] ?? '-') ?> years</td>
                                <td><?= esc($prog['created_at'] ?? '') ?></td>
                                <td class="d-flex gap-1">
                                    <a class="btn btn-sm btn-outline-secondary m-0" href="<?= site_url('admin/programs/' . $progId) ?>">View</a>
                                    <a class="btn btn-sm btn-outline-primary m-0" href="<?= site_url('admin/programs/' . $progId . '/edit') ?>">Edit</a>
                                    <form method="post" action="<?= site_url('admin/programs/' . $progId . '/delete') ?>" onsubmit="return confirm('Delete this program?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger m-0">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-muted">No programs found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Course Modal -->
    <div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#DAA520; color:#000;">
                    <h5 class="modal-title" id="addCourseModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>Add New Course
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="<?= site_url('admin/courses/create') ?>" id="addCourseForm">
                        <?= csrf_field() ?>
                        <!-- Course Information Section -->
                        <div class="form-section mb-3">
                            <div class="section-title"><i class="bi bi-info-circle me-2"></i>Course Information</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Course Title<span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Course Code</label>
                                        <input type="text" name="code" class="form-control" placeholder="e.g., MATH101">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Department</label>
                                        <select name="department" class="form-select" id="departmentSelect">
                                            <option value="">Select department</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Program</label>
                                        <select name="program" class="form-select" id="programSelect">
                                            <option value="">Select program</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Instructor Information Section -->
                        <div class="form-section mb-3">
                            <div class="section-title"><i class="bi bi-person-badge me-2"></i>Instructor Information</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Instructor<span class="text-danger">*</span></label>
                                        <select name="instructor_id" class="form-select" required>
                                            <option value="">Select instructor</option>
                                            <?php if (!empty($teachers ?? [])): ?>
                                                <?php foreach ($teachers as $t): ?>
                                                    <option value="<?= (int)($t['id'] ?? 0) ?>">
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
                        <div class="form-section mb-3">
                            <div class="section-title"><i class="bi bi-mortarboard me-2"></i>Academic Details</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Year Level</label>
                                        <select name="course_level" class="form-select">
                                            <option value="">Select level</option>
                                            <option value="1st Year">1st Year</option>
                                            <option value="2nd Year">2nd Year</option>
                                            <option value="3rd Year">3rd Year</option>
                                            <option value="4th Year">4th Year</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Semester</label>
                                        <select name="semester" class="form-select">
                                            <option value="">Select semester</option>
                                            <option value="1st Semester">1st Semester</option>
                                            <option value="2nd Semester">2nd Semester</option>
                                            <option value="Summer">Summer</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Unit</label>
                                        <input type="number" name="unit" class="form-control" min="0" max="10">
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-md-12">
                                        <label class="form-label">Academic Year</label>
                                        <input type="text" name="academic_year" class="form-control" placeholder="e.g., 2025-2026">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Course Time Span / Schedule Section -->
                        <div class="form-section mb-3">
                            <div class="section-title"><i class="bi bi-calendar-range me-2"></i>Course Time Span / Schedule</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">Course Dates</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Course Start Date</label>
                                                <input type="date" name="course_start_date" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Course End Date</label>
                                                <input type="date" name="course_end_date" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">Enrollment Dates</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Enrollment Start Date</label>
                                                <input type="date" name="enrollment_start_date" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Enrollment End Date</label>
                                                <input type="date" name="enrollment_end_date" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Class Schedule</label>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label text-muted">Days</label>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="class_days[]" value="Monday" id="dayMonday">
                                                        <label class="form-check-label" for="dayMonday">Mon</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="class_days[]" value="Tuesday" id="dayTuesday">
                                                        <label class="form-check-label" for="dayTuesday">Tue</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="class_days[]" value="Wednesday" id="dayWednesday">
                                                        <label class="form-check-label" for="dayWednesday">Wed</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="class_days[]" value="Thursday" id="dayThursday">
                                                        <label class="form-check-label" for="dayThursday">Thu</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="class_days[]" value="Friday" id="dayFriday">
                                                        <label class="form-check-label" for="dayFriday">Fri</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="class_days[]" value="Saturday" id="daySaturday">
                                                        <label class="form-check-label" for="daySaturday">Sat</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="class_days[]" value="Sunday" id="daySunday">
                                                        <label class="form-check-label" for="daySunday">Sun</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted">Time</label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <input type="time" name="start_time" class="form-control" placeholder="Start">
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="time" name="end_time" class="form-control" placeholder="End">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="addCourseForm" class="btn btn-primary" style="background-color:#DAA520;border:none;color:#000">Create Course</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Department Modal -->
    <div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#DAA520; color:#000;">
                    <h5 class="modal-title" id="addDepartmentModalLabel">
                        <i class="bi bi-building me-2"></i>Add New Department
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="<?= site_url('admin/departments/create') ?>" id="addDepartmentForm">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Department Name<span class="text-danger">*</span></label>
                            <input type="text" name="department_name" class="form-control" required placeholder="e.g., College of Business Education">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department Code</label>
                            <input type="text" name="department_code" class="form-control" placeholder="e.g., CBE">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Enter department description..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="addDepartmentForm" class="btn btn-primary" style="background-color:#DAA520;border:none;color:#000">Add Department</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Program Modal -->
    <div class="modal fade" id="addProgramModal" tabindex="-1" aria-labelledby="addProgramModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#DAA520; color:#000;">
                    <h5 class="modal-title" id="addProgramModalLabel">
                        <i class="bi bi-book me-2"></i>Add New Program
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="<?= site_url('admin/programs/create') ?>" id="addProgramForm">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Department<span class="text-danger">*</span></label>
                            <select name="department" class="form-select" id="programDepartmentSelect" required>
                                <option value="">Select department</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Program Name<span class="text-danger">*</span></label>
                            <input type="text" name="program_name" class="form-control" required placeholder="e.g., BS in Computer Science">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Program Code</label>
                            <input type="text" name="program_code" class="form-control" placeholder="e.g., BSCS">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Enter program description..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Duration (Years)</label>
                            <input type="number" name="duration" class="form-control" min="1" max="10" placeholder="e.g., 4">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="addProgramForm" class="btn btn-primary" style="background-color:#DAA520;border:none;color:#000">Add Program</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load departments into dropdowns
        const departments = <?= json_encode($departments ?? []) ?>;
        const programs = <?= json_encode($programs ?? []) ?>;
        
        // Populate department dropdowns
        function populateDepartments() {
            const departmentSelects = ['departmentSelect', 'programDepartmentSelect'];
            
            departmentSelects.forEach(selectId => {
                const select = document.getElementById(selectId);
                if (select) {
                    // Clear existing options except placeholder
                    select.innerHTML = '<option value="">Select department</option>';
                    
                    // Add departments
                    departments.forEach(dept => {
                        const option = document.createElement('option');
                        option.value = dept.department_name;
                        option.textContent = dept.department_name;
                        select.appendChild(option);
                    });
                }
            });
        }
        
        // Populate programs based on selected department
        function populateProgramsByDepartment(department) {
            const programSelect = document.getElementById('programSelect');
            if (!programSelect) return;
            
            // Clear existing options except placeholder
            programSelect.innerHTML = '<option value="">Select program</option>';
            
            if (department && programs.length > 0) {
                // Filter programs by selected department
                const filteredPrograms = programs.filter(prog => prog.department === department);
                
                filteredPrograms.forEach(prog => {
                    const option = document.createElement('option');
                    option.value = prog.program_name;
                    option.textContent = prog.program_name;
                    programSelect.appendChild(option);
                });
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            populateDepartments();
            
            // Add event listeners
            const departmentSelect = document.getElementById('departmentSelect');
            if (departmentSelect) {
                departmentSelect.addEventListener('change', function() {
                    populateProgramsByDepartment(this.value);
                });
            }
            
            const programDepartmentSelect = document.getElementById('programDepartmentSelect');
            if (programDepartmentSelect) {
                programDepartmentSelect.addEventListener('change', function() {
                    console.log('Program department selected:', this.value);
                });
            }
        });
    </script>
</body>
</html>
