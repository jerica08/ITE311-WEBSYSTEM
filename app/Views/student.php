<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
             color:#fff;
              border:none; 
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
            width:64px;
            height:64px;
            display:flex;
            align-items:center;
            justify-content:center;
            border-radius:12px;
            font-size:1.25rem;
            }
        .section-title { 
            background:#D1A11F; 
            color:#000;
            padding:.5rem .75rem;
            border-radius:8px 8px 0 0; 
            font-weight:600; 
            }
        .table-wrap {
             border-radius:10px; 
             overflow:hidden;
              box-shadow:0 8px 16px rgba(0,0,0,.08); 
              background:#fff; 
            }
        .table thead th { 
            background:#f8f9fa; 
        }
    </style>
</head>
<body>
    <?= view('templates/header', ['title' => 'Student Dashboard']) ?>

    <div class="container my-4">
        <!-- Welcome Card -->
        <div class="card welcome-card mb-4 px-3 py-3">
            <div class="d-flex align-items-center gap-3">
                <span><i class="bi bi-person-circle me-1"></i><?= esc($user['name']) ?></span>
            </div>
            <div class="small">
                Kawas National University Learning Management System<br>
                Role: <?= esc(ucfirst((string)($user['role'] ?? ''))) ?> · Email: <?= esc($user['email'] ?? '') ?>
            </div>
        </div>

        <!-- Enrolled Courses -->
        <div class="mb-2 section-title"><i class="bi bi-mortarboard-fill me-2"></i>Enrolled Courses</div>
        <div class="table-wrap mb-4">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th style="width:160px;">Subject Code</th>
                        <th style="width:100px;">Unit</th>
                        <th style="width:120px;">Year Level</th>
                        <th style="width:120px;">Department</th>
                        <th style="width:150px;">Class Schedule</th>
                        <th style="width:140px;">Academic Year</th>
                        <th style="width:180px;">Enrolled On</th>
                    </tr>
                </thead>
                <tbody id="enrolled-tbody">
                    <?php if (!empty($enrolledCourses ?? [])): ?>
                        <?php foreach ($enrolledCourses as $c): ?>
                            <tr>
                                <td><?= esc($c['title'] ?? '-') ?></td>
                                <td><?= esc($c['code'] ?? '-') ?></td>
                                <td><?= esc($c['unit'] ?? '-') ?></td>
                                <td><?= esc($c['course_level'] ?? '-') ?></td>
                                <td><?= esc($c['department'] ?? '-') ?></td>
                                <td><?= esc($c['academic_year'] ?? '-') ?></td>
                                <td><?= esc($c['enrollment_date'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-muted">No enrolled courses.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Available Courses -->
        <div class="mb-2 section-title"><i class="bi bi-journal-bookmark-fill me-2"></i>Available Courses</div>
        <div class="table-wrap mb-4">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th style="width:160px;">Subject Code</th>
                        <th style="width:100px;">Unit</th>
                        <th style="width:120px;">Year Level</th>
                        <th style="width:120px;">Department</th>
                        <th style="width:180px;">Instructor</th>
                        <th style="width:140px;">Academic Year</th>
                        <th style="width:140px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($availableCourses ?? [])): ?>
                        <?php foreach ($availableCourses as $ac): ?>
                            <tr id="course-row-<?= (int)($ac['id'] ?? 0) ?>">
                                <td><?= esc($ac['title'] ?? '-') ?></td>
                                <td><?= esc($ac['code'] ?? '-') ?></td>
                                <td><?= esc($ac['unit'] ?? '-') ?></td>
                                <td><?= esc($ac['course_level'] ?? '-') ?></td>
                                <td><?= esc($ac['department'] ?? '-') ?></td>
                                <td><?= esc($ac['instructor_name'] ?? '-') ?></td>
                                <td><?= esc($ac['academic_year'] ?? '-') ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary btn-enroll" style="background-color:#DAA520;border:none;color:#000" data-course-id="<?= (int)($ac['id'] ?? 0) ?>">
                                        Enroll
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-muted">No available courses to enroll.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Assignments -->
        <div class="mb-2 section-title"><i class="bi bi-journal-text-fill me-2"></i>Assignments</div>
        <div class="table-wrap mb-4">
            <?php if (!empty($assignmentsByCourse ?? [])): ?>
                <?php foreach ($assignmentsByCourse as $courseId => $courseData): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="text-primary mb-0">
                                <?= esc($courseData['course_title']) ?> 
                                <small class="text-muted">(<?= esc($courseData['course_code']) ?>)</small>
                            </h6>
                            <div>
                                <?php 
                                $completionStatus = $courseData['completion_status'] ?? 'pending';
                                $submittedCount = $courseData['submitted_assignments'] ?? 0;
                                $totalCount = $courseData['total_assignments'] ?? 0;
                                ?>
                                <?php if ($completionStatus === 'completed'): ?>
                                    <span class="badge bg-success text-white">
                                        <i class="bi bi-check-circle me-1"></i>Completed
                                    </span>
                                <?php elseif ($completionStatus === 'in_progress'): ?>
                                    <span class="badge bg-info text-white">
                                        <i class="bi bi-clock me-1"></i>In Progress (<?= $submittedCount ?>/<?= $totalCount ?>)
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Assignment Title</th>
                                        <th style="width:120px;">Due Date</th>
                                        <th style="width:100px;">Status</th>
                                        <th style="width:100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($courseData['assignments'])): ?>
                                        <?php foreach ($courseData['assignments'] as $assignment): ?>
                                            <?php 
                                            $status = $assignment['status'] ?? 'pending';
                                            $dueDate = $assignment['due_date'] ?? '';
                                            ?>
                                            <tr>
                                                <td><?= esc($assignment['title'] ?? '') ?></td>
                                                <td>
                                                    <small class="<?= $status === 'overdue' ? 'text-danger' : 'text-muted' ?>">
                                                        <?= $dueDate ? date('M j, Y', strtotime($dueDate)) : 'No due date' ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?php if ($status === 'submitted'): ?>
                                                        <span class="badge bg-success text-white">Submitted</span>
                                                    <?php elseif ($status === 'graded'): ?>
                                                        <span class="badge bg-info text-white">Graded</span>
                                                    <?php elseif ($status === 'overdue'): ?>
                                                        <span class="badge bg-danger text-white">Overdue</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning text-dark">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?= site_url('student/course/' . $courseId . '/assignments') ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-muted">No assignments posted for this course.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center p-4 text-muted">
                    <i class="bi bi-journal-text" style="font-size: 2rem;"></i>
                    <p class="mb-0">No assignments available yet.</p>
                    <small>Assignments posted by your teachers will appear here.</small>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- jQuery for AJAX enrollment -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        $(function() {
            $(document).on('click', '.btn-enroll', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const courseId = parseInt($btn.data('course-id')) || 0;
                if (!courseId) return;

                const tokenName = '<?= csrf_token() ?>';
                const tokenHash = '<?= csrf_hash() ?>';
                $btn.prop('disabled', true).text('Enrolling...');

                $.post('<?= site_url('course/enroll') ?>', {
                    course_id: courseId,
                    [tokenName]: tokenHash,
                }).done(function(data, textStatus, jqXHR) {
                    // On success: handle pending enrollment
                    if (data && data.status === 'success') {
                        const $row = $btn.closest('tr');
                        const title = $row.find('td').eq(0).text();
                        const code = $row.find('td').eq(1).text();
                        const unit = $row.find('td').eq(2).text();
                        const level = $row.find('td').eq(3).text();
                        const dept = $row.find('td').eq(4).text();
                        const year = $row.find('td').eq(6).text();

                        // Add to pending enrollments
                        $('#pending-tbody').prepend(
                            `<tr><td>${$('<div>').text(title).html()}</td><td>${$('<div>').text(code).html()}</td><td>${$('<div>').text(unit).html()}</td><td>${$('<div>').text(level).html()}</td><td>${$('<div>').text(dept).html()}</td><td>${$('<div>').text(year).html()}</td><td><span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Waiting for approval</span></td></tr>`
                        );

                        // Remove available row
                        $('#course-row-' + courseId).remove();

                        // Show alert
                        $('<div class="alert alert-info alert-dismissible fade show" role="alert">' +
                          data.message + 
                          '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                          '</div>').insertBefore($('.section-title').first());
                    } else if (jqXHR.status === 409) {
                        $('<div class="alert alert-warning alert-dismissible fade show" role="alert">' +
                          'You are already enrolled in this course.' +
                          '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                          '</div>').insertBefore($('.section-title').first());
                        $btn.prop('disabled', true).text('Enrolled');
                    } else if (jqXHR.status === 401) {
                        window.location.href = '<?= site_url('auth/login') ?>';
                    } else {
                        $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                          (data && data.message ? data.message : 'Failed to enroll.') +
                          '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                          '</div>').insertBefore($('.section-title').first());
                        $btn.prop('disabled', false).text('Enroll');
                    }
                }).fail(function(jqXHR) {
                    if (jqXHR.status === 401) {
                        window.location.href = '<?= site_url('auth/login') ?>';
                        return;
                    }
                    $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                      'Network error while enrolling.' +
                      '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                      '</div>').insertBefore($('.section-title').first());
                    $btn.prop('disabled', false).text('Enroll');
                });
            });
        });
    </script>
</body>
</html>
