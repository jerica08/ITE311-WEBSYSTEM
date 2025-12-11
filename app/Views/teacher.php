<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYgpC" crossorigin="anonymous"></script>
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
    </style>
</head>
<body>
    <?= view('templates/header', ['title' => 'Teacher Dashboard']) ?>

    <div class="container my-4">
        <!-- Welcome Card -->
        <div class="card welcome-card mb-4 px-3 py-3">
            <div class="row align-items-center g-3">
                <div class="col">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Welcome to your Dashboard, <?= esc($user['name'] ?? '') ?>!</h5>
                            <div class="small">
                                Kawas National University Learning Management System<br>
                                Role: <?= esc(ucfirst((string)($user['role'] ?? ''))) ?> · Email: <?= esc($user['email'] ?? '') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Enrollments -->
        <div class="mb-2 section-title">
            <i class="bi bi-person-check me-2"></i>Pending Enrollment Requests
            <?php if (!empty($pendingEnrollments ?? [])): ?>
                <span class="badge bg-danger ms-2"><?= count($pendingEnrollments) ?></span>
            <?php endif; ?>
        </div>
        <div class="table-wrap mb-4">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Code</th>
                        <th>Requested On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pendingEnrollments ?? [])): ?>
                        <?php foreach ($pendingEnrollments as $enrollment): ?>
                            <tr class="table-warning">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-plus text-primary me-2"></i>
                                        <strong><?= esc($enrollment['student_name'] ?? '') ?></strong>
                                    </div>
                                </td>
                                <td><?= esc($enrollment['student_email'] ?? '') ?></td>
                                <td>
                                    <div class="fw-bold"><?= esc($enrollment['course_title'] ?? '') ?></div>
                                    <small class="text-muted"><?= esc($enrollment['course_code'] ?? '') ?></small>
                                </td>
                                <td><?= esc($enrollment['course_code'] ?? '') ?></td>
                                <td>
                                    <small><?= esc($enrollment['created_at'] ?? '') ?></small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= site_url('teacher/approve-enrollment/' . (int)($enrollment['id'] ?? 0)) ?>" 
                                           class="btn btn-sm btn-success" 
                                           onclick="return confirm('Approve this enrollment request?')">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </a>
                                        <a href="<?= site_url('teacher/reject-enrollment/' . (int)($enrollment['id'] ?? 0)) ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Reject this enrollment request?')">
                                            <i class="bi bi-x-circle"></i> Reject
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-3">
                                <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">No pending enrollment requests.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Create Course Modal -->
        <div class="modal fade" id="createCourseModal" tabindex="-1" aria-labelledby="createCourseModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background-color:#DAA520; color:#000;">
                        <h5 class="modal-title" id="createCourseModalLabel">
                            <i class="bi bi-plus-circle me-2"></i>Create New Course
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="<?= site_url('teacher/courses/create') ?>" id="createCourseForm">
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
                                            <select name="department" class="form-select" id="teacherDepartmentSelect">
                                                <option value="">Select department</option>
                                                <?php if (!empty($departments ?? [])): ?>
                                                    <?php foreach ($departments as $dept): ?>
                                                        <option value="<?= esc($dept['department_name'] ?? '') ?>">
                                                            <?= esc($dept['department_name'] ?? '') ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Program</label>
                                            <select name="program" class="form-select" id="teacherProgramSelect">
                                                <option value="">Select program</option>
                                                <?php if (!empty($programs ?? [])): ?>
                                                    <?php foreach ($programs as $prog): ?>
                                                        <option value="<?= esc($prog['program_name'] ?? '') ?>" 
                                                                data-department="<?= esc($prog['department'] ?? '') ?>">
                                                            <?= esc($prog['program_name'] ?? '') ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
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
                        <button type="submit" form="createCourseForm" class="btn btn-primary" style="background-color:#DAA520;border:none;color:#000">Create Course</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses You Teach -->
        <div class="mb-2 section-title">
            <i class="bi bi-journal-text me-2"></i>Courses You Teach
            <button class="btn btn-dark-gold btn-sm float-end" type="button" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                <i class="bi bi-plus-square me-2"></i>Create New Course
            </button>
        </div>
        <div class="table-wrap mb-4">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Course Title</th>
                        <th style="width:120px;">Code</th>
                        <th style="width:150px;">Program</th>
                        <th style="width:120px;">Department</th>
                        <th style="width:150px;">Class Schedule</th>
                        <th style="width:100px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($courses ?? [])): ?>
                        <?php foreach ($courses as $i => $c): ?>
                            <?php $cid = (int)($c['id'] ?? 0); ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td>
                                    <div class="fw-bold"><?= esc($c['title'] ?? '-') ?></div>
                                    <small class="text-muted">Created: <?= esc($c['created_at'] ?? '-') ?></small>
                                </td>
                                <td><?= esc($c['code'] ?? '-') ?></td>
                                <td><?= esc($c['program'] ?? '-') ?></td>
                                <td><?= esc($c['department'] ?? '-') ?></td>
                                <td><?= esc($c['class_schedule'] ?? '-') ?></td>
                                <td>
                                    <?php 
                                    $status = $c['status'] ?? 'draft';
                                    $badgeClass = $status === 'published' ? 'bg-success' : 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= esc(ucfirst($status)) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-3">
                                <i class="bi bi-journal-x text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">You are not assigned to any courses yet.</p>
                                <small class="text-muted">Create a new course using the button above.</small>
                            </td>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript for alert dismissal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle alert dismissal for all close buttons
            var closeButtons = document.querySelectorAll('[data-bs-dismiss="alert"]');
            closeButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var alert = this.closest('.alert');
                    if (alert) {
                        alert.style.transition = 'opacity 0.15s ease';
                        alert.style.opacity = '0';
                        setTimeout(function() {
                            alert.remove();
                        }, 150);
                    }
                });
            });
        });
    </script>

    <!-- Department and Program Dropdown Functionality for Teacher Course Creation -->
    <script>
        const teacherDepartments = <?= json_encode($departments ?? []) ?>;
        const teacherPrograms = <?= json_encode($programs ?? []) ?>;

        // Populate department dropdown
        function populateTeacherDepartments() {
            const select = document.getElementById('teacherDepartmentSelect');
            if (select) {
                select.innerHTML = '<option value="">Select department</option>';
                
                teacherDepartments.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept.department_name;
                    option.textContent = dept.department_name;
                    select.appendChild(option);
                });
            }
        }

        // Populate programs based on selected department
        function populateTeacherProgramsByDepartment(department) {
            const programSelect = document.getElementById('teacherProgramSelect');
            if (!programSelect) return;
            
            // Clear existing options except placeholder
            programSelect.innerHTML = '<option value="">Select program</option>';
            
            if (department && teacherPrograms.length > 0) {
                // Filter programs by selected department
                const filteredPrograms = teacherPrograms.filter(prog => prog.department_name === department);
                
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
            populateTeacherDepartments();
            
            // Add change event listener to department select
            const departmentSelect = document.getElementById('teacherDepartmentSelect');
            if (departmentSelect) {
                departmentSelect.addEventListener('change', function() {
                    populateTeacherProgramsByDepartment(this.value);
                });
            }
        });
    </script>
</body>
</html>
