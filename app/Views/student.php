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
                <div class="col-auto">
                    <div class="initials">
                        <?php 
                        $nm = (string)($user['name'] ?? '');
                        $parts = preg_split('/\s+/', trim($nm));
                        $fi = strtoupper(substr($parts[0] ?? '', 0, 1));
                        $li = strtoupper(substr($parts[count($parts)-1] ?? '', 0, 1));
                        echo esc($fi . $li);
                        ?>
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
                        <th style="width:120px;">Term</th>
                        <th>Course</th>
                        <th style="width:160px;">Subject Code</th>
                        <th style="width:100px;">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($enrollments ?? [])): ?>
                        <?php foreach ($enrollments as $e): ?>
                            <tr>
                                <td><?= esc($e['term'] ?? '-') ?></td>
                                <td><?= esc($e['course'] ?? '-') ?></td>
                                <td><?= esc($e['code'] ?? '-') ?></td>
                                <td><?= esc($e['unit'] ?? '-') ?></td>
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
</body>
</html>
