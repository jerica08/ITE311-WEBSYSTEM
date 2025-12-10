<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { 
            background-color: #e6e3dc;  
            font-family: 'Times New Roman', serif; 
        }
        .topbar { 
            background:#000; 
            color:#fff; 
            padding:.5rem 1rem; 
            font-family: 'Times New Roman', serif;
         }
        .subbar { 
            background:#DAA520; 
            color:#fff; 
            padding:.5rem 1rem; 
            font-family: 'Times New Roman', serif; 
        }
        .menu a {
             color:#fff; 
             text-decoration:none; 
             padding:.4rem .8rem;
              border-radius:.3rem;       
            }
        .menu a.active, .menu a:hover { 
            background: rgba(0,0,0,.15); 
        }
        .logout-btn { 
            background:#E74C3C; 
            color:#fff; border:none; 
            padding:.4rem .8rem; 
            border-radius:.3rem; 
        }
        .welcome-card { 
            background:#D1A11F; 
            color:#000; 
            border:none; 
            border-radius:16px;
        }
        .welcome-card .avatar { 
            width:64px; 
            height:64px; 
            border-radius:50%; 
            object-fit:cover; 
        }
        .initials {
             background:#b98e19; 
             color:#000; 
             font-weight:700;
              width:64px; height:64px;
               display:flex; align-items:center;
                justify-content:center; 
                border-radius:12px; 
                font-size:1.25rem;
             }
        .btn-dark-gold {
             background:#111; 
             color:#fff; 
             border:none; 
            }
        .btn-dark-gold:hover { 
            background:#222; 
        }
        .section-title {
             background:#D1A11F; 
             color:#000; padding:.5rem .75rem;
              border-radius:8px 8px 0 0; 
              font-weight:600; 
            }
        .table-wrap { 
            border-radius:10px; 
            overflow:hidden; 
            box-shadow:0 8px 16px rgba(0,0,0,.08);
             background:#fff; }
        .table thead th {
             background:#f8f9fa; 
            }
        .form-section { 
            border-radius:10px; 
            overflow:hidden; 
            box-shadow:0 8px 16px rgba(0,0,0,.08); 
            background:#fff; 
            margin-bottom:1.5rem; 
        }
        .form-section .card-body { 
            padding:1.5rem; 
        }
    </style>
</head>
<body>
    <?= view('templates/header', ['title' => 'Teacher Dashboard']) ?>

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
        <!-- Welcome Card -->
        <div class="card welcome-card mb-4 px-3 py-3">
            <div class="row align-items-center g-3">
                <div class="col">
                    <h5 class="mb-1">Welcome to your Dashboard, <?= esc($user['name'] ?? '') ?>!</h5>
                    <div class="small">
                        Kawas National University Learning Management System<br>
                        Role: <?= esc(ucfirst((string)($user['role'] ?? ''))) ?> · Email: <?= esc($user['email'] ?? '') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex gap-3 mb-3">
            <button class="btn btn-dark-gold w-50" type="button" data-bs-toggle="collapse" data-bs-target="#createCourseForm" aria-expanded="false" aria-controls="createCourseForm">
                <i class="bi bi-plus-square me-2"></i>Create New Course
            </button>
            <a href="#" class="btn btn-dark-gold w-50">Create New Lesson</a>
        </div>

        <div class="collapse mb-4" id="createCourseForm">
            <div class="form-section">
                <div class="card-body">
                    <form method="post" action="<?= site_url('teacher/courses/create') ?>">
                        <?= csrf_field() ?>

                        <!-- Course Information Section -->
                        <div class="mb-4">
                            <div class="section-title"><i class="bi bi-info-circle me-2"></i>Course Information</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Course Title<span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Course Code</label>
                                        <input type="text" name="code" class="form-control" placeholder="e.g., SCI101">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Units</label>
                                        <input type="number" name="unit" class="form-control" min="0" max="10">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" rows="3" placeholder="Enter course description..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Instructor Information Section -->
                        <div class="mb-4">
                            <div class="section-title"><i class="bi bi-person-badge me-2"></i>Instructor Information</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            You will be automatically assigned as the instructor for this course.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Details Section -->
                        <div class="mb-4">
                            <div class="section-title"><i class="bi bi-mortarboard me-2"></i>Academic Details</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Course Level</label>
                                        <select name="course_level" class="form-select">
                                            <option value="">Select level</option>
                                            <option value="1st Year">1st Year</option>
                                            <option value="2nd Year">2nd Year</option>
                                            <option value="3rd Year">3rd Year</option>
                                            <option value="4th Year">4th Year</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Department</label>
                                        <select name="department" class="form-select">
                                            <option value="">Select department</option>
                                            <option value="Mathematics">Mathematics</option>
                                            <option value="Science">Science</option>
                                            <option value="English">English</option>
                                            <option value="Filipino">Filipino</option>
                                            <option value="Social Studies">Social Studies</option>
                                            <option value="Physical Education">Physical Education</option>
                                            <option value="Arts">Arts</option>
                                            <option value="Technology">Technology</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Academic Year</label>
                                        <input type="text" name="academic_year" class="form-control" placeholder="e.g., 2024-2025">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Course Time Span / Schedule Section -->
                        <div class="mb-4">
                            <div class="section-title"><i class="bi bi-calendar-range me-2"></i>Course Time Span / Schedule</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">Course Duration</h6>
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
                                        <h6 class="text-muted mb-3">Enrollment Period</h6>
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
                                        <label class="form-label">Class Schedule (optional)</label>
                                        <input type="text" name="class_schedule" class="form-control" placeholder="e.g., Tues & Thurs, 1:00 PM - 3:00 PM">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" style="background-color:#DAA520;border:none;color:#000">Create Course</button>
                            <button type="button" class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#createCourseForm">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Courses You Teach -->
        <div class="mb-2 section-title"><i class="bi bi-journal-text me-2"></i>Courses You Teach</div>
        <div class="table-wrap mb-4">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Title</th>
                        <th style="width:120px;">Code</th>
                        <th style="width:80px;">Unit</th>
                        <th style="width:100px;">Level</th>
                        <th style="width:100px;">Department</th>
                        <th style="width:180px;">Created</th>
                        <th style="width:140px;">View Students</th>
                        <th style="width:160px;">Upload Materials</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($courses ?? [])): ?>
                        <?php foreach ($courses as $i => $c): ?>
                            <?php $cid = (int)($c['id'] ?? 0); ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td><?= esc($c['title'] ?? '-') ?></td>
                                <td><?= esc($c['code'] ?? '-') ?></td>
                                <td><?= esc($c['unit'] ?? '-') ?></td>
                                <td><?= esc($c['course_level'] ?? '-') ?></td>
                                <td><?= esc($c['department'] ?? '-') ?></td>
                                <td><?= esc($c['created_at'] ?? '-') ?></td>
                                <td>
                                    <?php if ($cid > 0): ?>
                                        <a class="btn btn-sm btn-outline-success w-100" href="<?= site_url('teacher/courses/' . $cid . '/students') ?>">
                                            View Students
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($cid > 0): ?>
                                        <a class="btn btn-sm btn-outline-warning w-100" href="<?= site_url('materials/upload/' . $cid) ?>">
                                            Upload Materials
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-muted">You are not assigned to any courses yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- New Assignment Submissions -->
        <div class="mb-2 section-title"><i class="bi bi-clipboard-check me-2"></i>New Assignment Submissions</div>
        <div class="table-wrap">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:200px;">Submitted At</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Assignment</th>
                        <th style="width:120px;">Status</th>
                        <th style="width:120px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($submissions ?? [])): ?>
                        <?php foreach ($submissions as $s): ?>
                            <tr>
                                <td><?= esc($s['submitted_at'] ?? '-') ?></td>
                                <td><?= esc($s['student_name'] ?? '-') ?></td>
                                <td><?= esc($s['course_title'] ?? '-') ?></td>
                                <td><?= esc($s['assignment_title'] ?? '-') ?></td>
                                <td><?= esc($s['status'] ?? '-') ?></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-muted">No new submissions.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
