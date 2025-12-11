<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #e6e3dc; font-family: 'Times New Roman', serif; }
        .section-title { background:#D1A11F; color:#000; padding:.5rem .75rem; border-radius:8px 8px 0 0; font-weight:600; }
        .table-wrap { border-radius:10px; overflow:hidden; box-shadow:0 8px 16px rgba(0,0,0,.08); background:#fff; }
        .table thead th { background:#f8f9fa; }
    </style>
</head>
<body>
    <?= view('templates/header', ['title' => 'My Courses']) ?>

    <div class="container my-4">
        <!-- Pending Enrollments Section -->
        <?php if (!empty($pendingEnrollments ?? [])): ?>
            <div class="mb-4">
                <div class="mb-2 section-title"><i class="bi bi-person-check me-2"></i>Pending Student Enrollments</div>
                <div class="table-wrap mb-4">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Enrollment Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingEnrollments as $i => $enrollment): ?>
                                <tr>
                                    <td><?= esc($enrollment['student_name'] ?? '') ?></td>
                                    <td><?= esc($enrollment['student_email'] ?? '') ?></td>
                                    <td><?= esc($enrollment['course_title'] ?? '') ?> (<?= esc($enrollment['course_code'] ?? '') ?>)</td>
                                    <td><?= esc($enrollment['created_at'] ?? '') ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-success me-1" href="<?= site_url('teacher/approve-enrollment/' . ($enrollment['id'] ?? 0)) ?>">Approve</a>
                                        <a class="btn btn-sm btn-danger" href="<?= site_url('teacher/reject-enrollment/' . ($enrollment['id'] ?? 0)) ?>">Reject</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- My Courses Section -->
        <div class="mb-2 section-title d-flex justify-content-between align-items-center flex-wrap">
            <div><i class="bi bi-journal-text me-2"></i>My Courses</div>
            <form class="d-flex align-items-center gap-2 mt-2 mt-sm-0" method="get" action="<?= current_url() ?>">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text"
                           name="q"
                           class="form-control"
                           placeholder="Search title or code"
                           value="<?= esc($filters['q'] ?? '') ?>">
                </div>
                <select name="level" class="form-select form-select-sm" style="min-width: 140px;">
                    <option value="all">All Levels</option>
                    <?php if (!empty($courseLevels ?? [])): ?>
                        <?php foreach ($courseLevels as $level): ?>
                            <option value="<?= esc($level) ?>" <?= (($filters['level'] ?? '') === $level) ? 'selected' : '' ?>>
                                <?= esc($level) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if (!empty($filters['q'] ?? '') || (!empty($filters['level']) && ($filters['level'] ?? '') !== 'all')): ?>
                    <a href="<?= current_url() ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
                <?php endif; ?>
                <button type="submit" class="btn btn-sm btn-dark">Apply</button>
            </form>
        </div>
        <div class="table-wrap mb-4">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Course Title</th>
                        <th style="width:120px;">Code</th>
                        <th style="width:80px;">Unit</th>
                        <th style="width:120px;">Level</th>
                        <th style="width:140px;">Department</th>
                        <th style="width:100px;">Status</th>
                        <th style="width:120px;">View Students</th>
                        <th style="width:140px;">Upload Materials</th>
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
                                <td><?= esc($c['unit'] ?? '-') ?></td>
                                <td><?= esc($c['course_level'] ?? '-') ?></td>
                                <td><?= esc($c['department'] ?? '-') ?></td>
                                <td>
                                    <?php 
                                    $status = $c['status'] ?? 'draft';
                                    $badgeClass = $status === 'published' ? 'bg-success' : 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= esc(ucfirst($status)) ?></span>
                                </td>
                                <td>
                                    <?php if ($cid > 0): ?>
                                        <a class="btn btn-sm btn-outline-success w-100" href="<?= site_url('teacher/courses/' . $cid . '/students') ?>">
                                            <i class="bi bi-people me-1"></i> View Students
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($cid > 0): ?>
                                        <a class="btn btn-sm btn-outline-warning w-100" href="<?= site_url('materials/upload/' . $cid) ?>">
                                            <i class="bi bi-upload me-1"></i> Upload Materials
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-3">
                                <i class="bi bi-journal-x text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">You are not assigned to any courses yet.</p>
                                <small class="text-muted">Create a new course from your dashboard to get started.</small>
                                <br><br>
                                <small class="text-danger">DEBUG: Courses variable is <?php echo isset($courses) ? (is_array($courses) ? 'array with ' . count($courses) . ' items' : 'not array') : 'not set'; ?></small>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
