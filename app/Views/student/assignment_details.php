<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($assignment['title'] ?? '') ?> - Assignment Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #e6e3dc;
            font-family: 'Times New Roman', serif;
        }
        .assignment-header {
            background: linear-gradient(135deg, #D1A11F 0%, #DAA520 100%);
            color: #000;
            padding: 2rem;
            border-radius: 12px 12px 0 0;
        }
        .assignment-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .assignment-meta {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .meta-item i {
            font-size: 1.1rem;
        }
        .due-date {
            font-weight: 500;
        }
        .due-date.overdue {
            color: #dc3545;
            font-weight: 600;
        }
        .assignment-content {
            background: white;
            padding: 2rem;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .section-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #DAA520;
        }
        .description-text {
            line-height: 1.8;
            color: #555;
            margin-bottom: 2rem;
        }
        .attachment-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .attachment-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            background: white;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            border: 1px solid #e9ecef;
            transition: all 0.2s;
        }
        .attachment-item:hover {
            background: #f0f0f0;
            border-color: #DAA520;
        }
        .attachment-icon {
            font-size: 1.5rem;
            color: #DAA520;
        }
        .attachment-info {
            flex: 1;
        }
        .attachment-name {
            font-weight: 500;
            color: #333;
        }
        .attachment-size {
            font-size: 0.85rem;
            color: #666;
        }
        .submission-section {
            background: #e8f5e8;
            border: 1px solid #d4edda;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .submission-section.no-submission {
            background: #fff3cd;
            border-color: #ffeaa7;
        }
        .submission-section.overdue {
            background: #f8d7da;
            border-color: #f5c6cb;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
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
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
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
    </style>
</head>
<body>
    <?= view('templates/header') ?>

    <div class="container my-4">
        <!-- Assignment Header -->
        <div class="card mb-4">
            <div class="assignment-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="assignment-title">
                            <i class="bi bi-clipboard-check me-2"></i>
                            <?= esc($assignment['title'] ?? '') ?>
                        </div>
                        <div class="assignment-meta">
                            <div class="meta-item">
                                <i class="bi bi-book"></i>
                                <span><?= esc($course['title'] ?? '') ?> (<?= esc($course['code'] ?? '') ?>)</span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-calendar-event"></i>
                                <span class="due-date <?= $isOverdue ? 'overdue' : '' ?>">
                                    Due: <?= $dueDate ? date('M j, Y g:i A', strtotime($dueDate)) : 'No due date' ?>
                                </span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-trophy"></i>
                                <span><?= esc($assignment['max_score'] ?? '100') ?> points</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="status-badge status-<?= $status ?>">
                                <?= ucfirst($status) ?>
                            </span>
                            <span class="small opacity-75">
                                Posted: <?= $assignment['created_at'] ? date('M j, Y', strtotime($assignment['created_at'])) : 'Unknown' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignment Content -->
        <div class="assignment-content">
            <!-- Description Section -->
            <div class="mb-4">
                <h5 class="section-title">
                    <i class="bi bi-file-text me-2"></i>Assignment Description
                </h5>
                <div class="description-text">
                    <?= nl2br(esc($assignment['description'] ?? 'No description provided.')) ?>
                </div>
            </div>

            <!-- Attachments Section -->
            <?php if (!empty($assignment['attachment'])): ?>
            <div class="attachment-section">
                <h5 class="section-title mb-3">
                    <i class="bi bi-paperclip me-2"></i>Attachments
                </h5>
                <div class="attachment-item">
                    <div class="attachment-icon">
                        <i class="bi bi-file-earmark"></i>
                    </div>
                    <div class="attachment-info">
                        <div class="attachment-name"><?= esc($assignment['attachment']) ?></div>
                        <div class="attachment-size">File attachment</div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download me-1"></i>Download
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <!-- Submission Status Section -->
            <div class="submission-section <?= $status === 'pending' ? 'no-submission' : ($isOverdue ? 'overdue' : '') ?>">
                <h5 class="section-title mb-3">
                    <i class="bi bi-send me-2"></i>Submission Status
                </h5>
                <?php if ($status === 'pending'): ?>
                    <p class="mb-3">You haven't submitted this assignment yet.</p>
                    <?php if ($isOverdue): ?>
                        <div class="alert alert-warning mb-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            This assignment is overdue! Submit as soon as possible.
                        </div>
                    <?php endif; ?>
                <?php elseif ($status === 'submitted'): ?>
                    <p class="mb-3">Your assignment has been submitted and is awaiting grading.</p>
                    <?php if ($submission && $submission->submission_date): ?>
                        <p class="small text-muted mb-0">
                            <i class="bi bi-clock me-1"></i>
                            Submitted on: <?= date('M j, Y g:i A', strtotime($submission->submission_date)) ?>
                        </p>
                    <?php endif; ?>
                <?php elseif ($status === 'graded'): ?>
                    <div class="mb-3">
                        <p class="mb-2">Your assignment has been graded.</p>
                        <div class="alert alert-success">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <i class="bi bi-trophy me-2"></i>
                                    <strong>Your Grade:</strong> 
                                    <span class="fs-5"><?= esc($submission->grade ?? 'N/A') ?></span> / 
                                    <span><?= esc($assignment['max_score'] ?? '100') ?></span>
                                </div>
                                <div class="text-end">
                                    <?php 
                                    $percentage = $submission->grade && $assignment['max_score'] 
                                        ? round(($submission->grade / $assignment['max_score']) * 100, 1) 
                                        : 0; 
                                    ?>
                                    <span class="badge bg-success"><?= $percentage ?>%</span>
                                </div>
                            </div>
                        </div>
                        <?php if ($submission && $submission->graded_at): ?>
                            <p class="small text-muted mb-2">
                                <i class="bi bi-check-circle me-1"></i>
                                Graded on: <?= date('M j, Y g:i A', strtotime($submission->graded_at)) ?>
                            </p>
                        <?php endif; ?>
                        <?php if ($submission && !empty($submission->feedback)): ?>
                            <div class="mt-3">
                                <h6 class="text-muted mb-2">
                                    <i class="bi bi-chat-left-text me-1"></i>Feedback:
                                </h6>
                                <div class="bg-light p-3 rounded">
                                    <?= nl2br(esc($submission->feedback)) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($submission && $submission->submission_date): ?>
                        <p class="small text-muted mb-0">
                            <i class="bi bi-clock me-1"></i>
                            Submitted on: <?= date('M j, Y g:i A', strtotime($submission->submission_date)) ?>
                        </p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="<?= site_url('student/course/' . ($course['id'] ?? '') . '/assignments') ?>" class="btn btn-outline-custom">
                    <i class="bi bi-arrow-left me-1"></i>Back to Assignments
                </a>
                <?php if ($status === 'pending'): ?>
                    <a href="<?= site_url('student/course/' . ($course['id'] ?? '') . '/answer/' . ($assignment['id'] ?? '')) ?>" class="btn btn-primary-custom">
                        <i class="bi bi-pencil me-1"></i>Answer Assignment
                    </a>
                <?php elseif ($status === 'submitted'): ?>
                    <button class="btn btn-outline-custom" disabled>
                        <i class="bi bi-clock me-1"></i>Awaiting Grading
                    </button>
                <?php elseif ($status === 'graded'): ?>
                    <button class="btn btn-outline-custom">
                        <i class="bi bi-eye me-1"></i>View Grade
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
