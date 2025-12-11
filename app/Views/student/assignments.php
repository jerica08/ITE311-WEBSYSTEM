<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments - Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #e6e3dc;
            font-family: 'Times New Roman', serif;
        }
        .welcome-card { 
            background:#D1A11F; 
            color:#000; 
            border:none; 
            border-radius:16px; 
        }
        .course-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }
        .course-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .course-header {
            background: linear-gradient(135deg, #D1A11F 0%, #DAA520 100%);
            color: #000; 
            padding: 1rem 1.25rem;
            font-weight: 600;
            border-bottom: none;
        }
        .course-body {
            padding: 1.25rem;
        }
        .course-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #D1A11F;
        }
        .stat-label {
            font-size: 0.85rem;
            color: #666;
            text-transform: uppercase;
        }
        .assignment-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .assignment-item {
            padding: 0.75rem;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.2s;
        }
        .assignment-item:hover {
            background-color: #f8f9fa;
        }
        .assignment-item:last-child {
            border-bottom: none;
        }
        .assignment-info {
            flex: 1;
        }
        .assignment-title {
            font-weight: 500;
            color: #333;
            margin-bottom: 0.25rem;
            font-size: 0.95rem;
        }
        .assignment-meta {
            font-size: 0.8rem;
            color: #666;
        }
        .due-date {
            color: #D1A11F;
            font-weight: 500;
        }
        .due-date.overdue {
            color: #dc3545;
            font-weight: 600;
        }
        .assignment-badge {
            background: #D1A11F;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .view-all-btn {
            background: #D1A11F;
            color: #000;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
            width: 100%;
            margin-top: 1rem;
        }
        .view-all-btn:hover {
            background: #DAA520;
            color: #000;
        }
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
        .empty-state i {
            font-size: 2.5rem;
            color: #DAA520;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <?= view('templates/header') ?>

    <div class="container my-4">

        <!-- Assignments by Course -->
        <?php if (!empty($assignmentsByCourse)): ?>
            <?php foreach ($assignmentsByCourse as $courseId => $courseData): ?>
                <div class="course-card">
                    <div class="course-header">
                        <i class="bi bi-book me-2"></i>
                        <?= esc($courseData['course_title']) ?>
                    </div>
                    
                    <div class="course-body">
                        <div class="course-stats">
                            <div class="stat-item">
                                <div class="stat-number"><?= count($courseData['assignments']) ?></div>
                                <div class="stat-label">Assignments</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">
                                    <?php 
                                    $pendingCount = 0;
                                    foreach ($courseData['assignments'] as $assignment) {
                                        $dueDate = $assignment['due_date'] ?? '';
                                        if (!$dueDate || strtotime($dueDate) >= strtotime('now')) {
                                            $pendingCount++;
                                        }
                                    }
                                    echo $pendingCount;
                                    ?>
                                </div>
                                <div class="stat-label">Pending</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?= esc($courseData['course_code']) ?></div>
                                <div class="stat-label">Course Code</div>
                            </div>
                        </div>

                        <?php if (!empty($courseData['assignments'])): ?>
                            <ul class="assignment-list">
                                <?php 
                                $assignmentsToShow = array_slice($courseData['assignments'], 0, 3);
                                foreach ($assignmentsToShow as $assignment): 
                                ?>
                                    <?php 
                                    $dueDate = $assignment['due_date'] ?? '';
                                    $isOverdue = $dueDate && strtotime($dueDate) < strtotime('now');
                                    ?>
                                    <li class="assignment-item">
                                        <div class="assignment-info">
                                            <div class="assignment-title">
                                                <?= esc($assignment['title'] ?? '') ?>
                                            </div>
                                            <div class="assignment-meta">
                                                <span class="due-date <?= $isOverdue ? 'overdue' : '' ?>">
                                                    <?= $dueDate ? date('M j', strtotime($dueDate)) : 'No due date' ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="assignment-badge">
                                            <?= esc($assignment['max_score'] ?? '100') ?> pts
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                                
                                <?php if (count($courseData['assignments']) > 3): ?>
                                    <li class="assignment-item text-center">
                                        <div class="text-muted">
                                            +<?= count($courseData['assignments']) - 3 ?> more assignments
                                        </div>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p class="mb-0">No assignments posted yet</p>
                            </div>
                        <?php endif; ?>

                        <a href="<?= site_url('student/course/' . $courseId . '/assignments') ?>" class="btn view-all-btn">
                            <i class="bi bi-arrow-right me-1"></i>View All Assignments
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h5>No Assignments Available</h5>
                <p>You haven't enrolled in any courses yet, or no assignments have been posted for your enrolled courses.</p>
                <a href="<?= site_url('dashboard') ?>" class="btn btn-primary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
