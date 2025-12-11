<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submission - <?= esc($submission->assignment_title ?? '') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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
        .content-card { 
            background:#fff; 
            border-radius:8px; 
            box-shadow:0 2px 4px rgba(0,0,0,.1); 
        }
        .submission-content {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            min-height: 200px;
            white-space: pre-wrap;
        }
        .attachment-link {
            background-color: #DAA520;
            color: #000;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .attachment-link:hover {
            background-color: #b8941f;
            color: #000;
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="topbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <i class="bi bi-mortarboard-fill me-2"></i>
            <strong>Kawas National High School - LMS</strong>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span><i class="bi bi-person-circle me-1"></i><?= esc($user['name']) ?></span>
            <a href="<?= site_url('logout') ?>" class="logout-btn">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>

    <!-- Sub Bar -->
    <div class="subbar">
        <div class="d-flex justify-content-between align-items-center">
            <div class="menu">
                <a href="<?= site_url('teacher/dashboard') ?>"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>
                <a href="<?= site_url('teacher/assignments') ?>"><i class="bi bi-journal-text me-1"></i>Assignments</a>
                <a href="<?= site_url('teacher/courses') ?>"><i class="bi bi-book me-1"></i>My Courses</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= site_url('teacher/dashboard') ?>" style="color: #DAA520;">Dashboard</a></li>
                <li class="breadcrumb-item active">View Submission</li>
            </ol>
        </nav>

        <!-- Submission Details -->
        <div class="content-card p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h3 class="mb-2"><?= esc($submission->assignment_title ?? '') ?></h3>
                    <p class="text-muted mb-1">
                        <i class="bi bi-book me-1"></i><?= esc($submission->course_title ?? '') ?> 
                        <span class="text-muted">(<?= esc($submission->course_code ?? '') ?>)</span>
                    </p>
                </div>
                <div class="text-end">
                    <a href="<?= site_url('teacher/dashboard') ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Assignment Info -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5><i class="bi bi-info-circle me-2"></i>Assignment Details</h5>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Due Date:</strong></td>
                            <td><?= $submission->assignment_due_date ? date('M j, Y g:i A', strtotime($submission->assignment_due_date)) : 'No due date' ?></td>
                        </tr>
                        <tr>
                            <td><strong>Submitted:</strong></td>
                            <td><?= $submission->submission_date ? date('M j, Y g:i A', strtotime($submission->submission_date)) : 'N/A' ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5><i class="bi bi-person me-2"></i>Student Information</h5>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td><?= esc($submission->student_name ?? '') ?></td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td><?= esc($submission->student_email ?? '') ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Assignment Description -->
            <?php if (!empty($submission->assignment_description)): ?>
            <div class="mb-4">
                <h5><i class="bi bi-file-text me-2"></i>Assignment Description</h5>
                <div class="submission-content">
                    <?= esc($submission->assignment_description) ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Student's Answer -->
            <div class="mb-4">
                <h5><i class="bi bi-pencil-square me-2"></i>Student's Answer</h5>
                <div class="submission-content">
                    <?= esc($submission->answer_text ?? 'No text answer provided.') ?>
                </div>
            </div>

            <!-- Attachment -->
            <?php if (!empty($submission->attachment)): ?>
            <div class="mb-4">
                <h5><i class="bi bi-paperclip me-2"></i>Submitted File</h5>
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= site_url('teacher/submissions/download/' . $submission->id) ?>" class="attachment-link">
                        <i class="bi bi-download"></i>
                        <?= esc($submission->original_filename ?? 'Download File') ?>
                    </a>
                    <small class="text-muted">Click to download the submitted file</small>
                </div>
            </div>
            <?php endif; ?>

            <!-- Grade Section -->
            <div class="mb-4">
                <h5><i class="bi bi-star me-2"></i>Grading</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="grade" class="form-label">Grade (if applicable):</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="grade" name="grade" 
                                       value="<?= esc($submission->grade ?? '') ?>" 
                                       min="0" max="100" step="0.5">
                                <span class="input-group-text">/ 100</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="feedback" class="form-label">Feedback:</label>
                            <textarea class="form-control" id="feedback" name="feedback" rows="4"><?= esc($submission->feedback ?? '') ?></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary" onclick="saveGrade()">
                                <i class="bi bi-save me-1"></i>Save Grade
                            </button>
                            <a href="<?= site_url('teacher/submissions/view/' . $submission->id) ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <?php if (!empty($submission->grade)): ?>
                        <div class="alert alert-success">
                            <h6><i class="bi bi-check-circle me-1"></i>Graded</h6>
                            <p class="mb-2"><strong>Grade:</strong> <?= esc($submission->grade) ?>/100</p>
                            <?php if (!empty($submission->feedback)): ?>
                                <p class="mb-0"><strong>Feedback:</strong> <?= esc($submission->feedback) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($submission->graded_at)): ?>
                                <small class="text-muted">Graded on: <?= date('M j, Y g:i A', strtotime($submission->graded_at)) ?></small>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-1"></i>
                            This submission has not been graded yet.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function saveGrade() {
            const grade = $('#grade').val();
            const feedback = $('#feedback').val();
            
            if (!grade && !feedback) {
                alert('Please enter a grade or feedback before saving.');
                return;
            }
            
            $.post('<?= site_url('teacher/saveGrade') ?>', {
                submission_id: <?= $submission->id ?? 0 ?>,
                grade: grade,
                feedback: feedback,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            })
            .done(function(data) {
                if (data.success) {
                    alert('Grade saved successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to save grade'));
                }
            })
            .fail(function() {
                alert('Error: Failed to save grade. Please try again.');
            });
        }
    </script>
</body>
</html>
