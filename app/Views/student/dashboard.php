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

        <!-- Enrolled Courses -->
        <div class="mb-2 section-title"><i class="bi bi-mortarboard-fill me-2"></i>Enrolled Courses</div>
        <div class="table-wrap mb-4">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th style="width:160px;">Subject Code</th>
                        <th style="width:100px;">Unit</th>
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
                                <td><?= esc($c['enrollment_date'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-muted">No enrolled courses.</td>
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
                                <td>
                                    <button class="btn btn-sm btn-primary btn-enroll" style="background-color:#DAA520;border:none;color:#000" data-course-id="<?= (int)($ac['id'] ?? 0) ?>">
                                        Enroll
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-muted">No available courses to enroll.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Upcoming Deadlines -->
        <div class="mb-2 section-title"><i class="bi bi-alarm-fill me-2"></i>Upcoming Deadlines</div>
        <div class="table-wrap mb-4">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:160px;">Due Date</th>
                        <th>Course</th>
                        <th>Item</th>
                        <th style="width:120px;">Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($deadlines ?? [])): ?>
                        <?php foreach ($deadlines as $d): ?>
                            <tr>
                                <td><?= esc($d['due'] ?? '-') ?></td>
                                <td><?= esc($d['course'] ?? '-') ?></td>
                                <td><?= esc($d['item'] ?? '-') ?></td>
                                <td><?= esc($d['type'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-muted">No upcoming deadlines.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Recent Grades / Feedback -->
        <div class="mb-2 section-title"><i class="bi bi-clipboard2-check-fill me-2"></i>Recent Grades / Feedback</div>
        <div class="table-wrap">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:160px;">Date</th>
                        <th>Course</th>
                        <th>Item</th>
                        <th style="width:120px;">Grade</th>
                        <th>Feedback</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($grades ?? [])): ?>
                        <?php foreach ($grades as $g): ?>
                            <tr>
                                <td><?= esc($g['date'] ?? '-') ?></td>
                                <td><?= esc($g['course'] ?? '-') ?></td>
                                <td><?= esc($g['item'] ?? '-') ?></td>
                                <td><?= esc($g['grade'] ?? '-') ?></td>
                                <td><?= esc($g['feedback'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-muted">No grades or feedback yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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
                    // On success: add to enrolled list and disable button
                    if (jqXHR.status === 201 && data && data.status === 'success') {
                        const $row = $btn.closest('tr');
                        const title = $row.find('td').eq(0).text();
                        const code  = $row.find('td').eq(1).text();
                        const unit  = $row.find('td').eq(2).text();

                        // Append new enrolled row
                        const now = new Date();
                        const stamp = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0') + '-' + String(now.getDate()).padStart(2,'0') + ' ' + String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0') + ':' + String(now.getSeconds()).padStart(2,'0');
                        $('#enrolled-tbody').append(
                            `<tr><td>${$('<div>').text(title).html()}</td><td>${$('<div>').text(code).html()}</td><td>${$('<div>').text(unit).html()}</td><td>${stamp}</td></tr>`
                        );

                        // Remove available row
                        $('#course-row-' + courseId).remove();

                        // Show alert
                        $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                          'Enrolled in ' + $('<div>').text(title).html() + ' successfully.' +
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
