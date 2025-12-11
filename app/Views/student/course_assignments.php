<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($course['title'] ?? '') ?> Assignments - Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #e6e3dc;
            font-family: 'Times New Roman', serif;
        }
        .course-header {
            background: linear-gradient(135deg, #D1A11F 0%, #DAA520 100%);
            color: #000;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
            font-weight: 600;
        }
        .assignment-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .assignment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .assignment-header {
            background: #f8f9fa;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e9ecef;
        }
        .assignment-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        .assignment-meta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.9rem;
            color: #666;
        }
        .me.two-column-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            align-items: start;
            min-height: 500px;
        }
        .due-date {
            color: #D1A11F;
            font-weight: 500;
        }
        .due-date.overdue {
            color: #dc3545;
            font-weight: 600;
        }
        .assignment-body {
            padding: 1.25rem;
        }
        .assignment-description {
            color: #555;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .assignment-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
        }
        .btn-primary-custom {
            background: #DAA520;
            color: #000;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        .btn-primary-custom:hover {
            background: #D1A11F;
            color: #000;
        }
        .btn-outline-custom {
            background: transparent;
            color: #DAA520;
            border: 2px solid #DAA520;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        .btn-outline-custom:hover {
            background: #DAA520;
            color: #000;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-submitted {
            background: #d1ecf1;
            color: #0c5460;
        }
        .status-graded {
            background: #d4edda;
            color: #155724;
        }
        .status-overdue {
            background: #f8d7da;
            color: #721c24;
        }
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #666;
        }
        .empty-state i {
            font-size: 3rem;
            color: #DAA520;
            margin-bottom: 1rem;
        }
        .back-btn {
            background: #D1A11F;
            color: #000;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .back-btn:hover {
            background: #DAA520;
            color: #000;
        }
    </style>
</head>
<body>
    <?= view('templates/header', ['title' => esc($course['title'] ?? '') . ' Assignments - Student Dashboard']) ?>

    <div class="container my-4">
        <!-- Success/Error Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <!-- Course Header -->
        <div class="card mb-4">
            <div class="course-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="bi bi-book me-2"></i>
                            <?= esc($course['title'] ?? '') ?>
                        </h4>
                        <div class="small opacity-75">
                            Course Code: <?= esc($course['code'] ?? '') ?> | 
                            <?= count($assignments ?? []) ?> Total Assignments
                        </div>
                    </div>
                    <a href="<?= site_url('student/assignments') ?>" class="btn back-btn">
                        <i class="bi bi-arrow-left me-1"></i>Back to All Courses
                    </a>
                </div>
            </div>
        </div>

        <!-- Assignments List -->
        <?php if (!empty($assignments)): ?>
            <?php foreach ($assignments as $assignment): ?>
                <?php 
                $dueDate = $assignment['due_date'] ?? '';
                $isOverdue = $dueDate && strtotime($dueDate) < strtotime('now');
                $status = $assignment['status'] ?? 'pending'; // Use status from controller
                ?>
                <div class="assignment-card">
                    <div class="assignment-header">
                        <div class="assignment-title">
                            <?= esc($assignment['title'] ?? '') ?>
                        </div>
                        <div class="assignment-meta">
                            <div class="meta-item">
                                <i class="bi bi-calendar-event"></i>
                                <span class="due-date <?= $isOverdue ? 'overdue' : '' ?>">
                                    <?= $dueDate ? date('M j, Y g:i A', strtotime($dueDate)) : 'No due date' ?>
                                </span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-trophy"></i>
                                <span><?= esc($assignment['max_score'] ?? '100') ?> points</span>
                            </div>
                            <div class="meta-item">
                                <span class="status-badge status-<?= $status ?>">
                                    <?= ucfirst($status) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="assignment-body">
                        <div class="assignment-description">
                            <?= nl2br(esc($assignment['description'] ?? 'No description provided.')) ?>
                        </div>
                        <?php if (!empty($assignment['attachment_file'])): ?>
                            <div class="assignment-attachment mb-3">
                                <div class="alert alert-info d-flex align-items-center" role="alert">
                                    <i class="bi bi-paperclip me-2"></i>
                                    <div class="flex-grow-1">
                                        <strong>Attachment:</strong> <?= esc($assignment['attachment_file']) ?>
                                    </div>
                                    <a href="<?= site_url('teacher/assignments/download/' . $assignment['id']) ?>" 
                                       class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="bi bi-download me-1"></i>Download
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="assignment-actions">
                            <?php if ($status === 'graded'): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-success fw-bold">
                                        <i class="bi bi-trophy me-1"></i>
                                        <?= esc($assignment['grade'] ?? 'N/A') ?>/<?= esc($assignment['max_score'] ?? '100') ?>
                                    </span>
                                </div>
                            <?php elseif ($status === 'submitted'): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-info">
                                        <i class="bi bi-clock me-1"></i>Awaiting Grading
                                    </span>
                                    <a href="<?= site_url('student/course/' . ($course['id'] ?? '') . '/assignment/' . ($assignment['id'] ?? '')) ?>" class="btn btn-outline-custom btn-sm">
                                        <i class="bi bi-eye me-1"></i>View Details
                                    </a>
                                </div>
                            <?php else: ?>
                                <a href="<?= site_url('student/course/' . ($course['id'] ?? '') . '/answer/' . ($assignment['id'] ?? '')) ?>" class="btn btn-primary-custom">
                                    <i class="bi bi-pencil me-1"></i>Answer
                                </a>
                                <a href="<?= site_url('student/course/' . ($course['id'] ?? '') . '/assignment/' . ($assignment['id'] ?? '')) ?>" class="btn btn-outline-custom btn-sm">
                                    <i class="bi bi-info-circle me-1"></i>Details
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h5>No Assignments Available</h5>
                <p>No assignments have been posted for this course yet.</p>
                <a href="<?= site_url('student/assignments') ?>" class="btn btn-primary">
                    <i class="bi bi-arrow-left me-1"></i>Back to All Courses
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
