<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Assignment - <?= esc($submission->assignment_title ?? '') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #e6e3dc;
            font-family: 'Times New Roman', serif;
        }
        .grade-header {
            background: linear-gradient(135deg, #D1A11F 0%, #DAA520 100%);
            color: #000;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
        }
        .assignment-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .student-info {
            font-size: 1.1rem;
            font-weight: 500;
        }
        .submission-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            overflow: hidden;
        }
        .submission-header {
            background: #f8f9fa;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e9ecef;
        }
        .submission-body {
            padding: 1.25rem;
        }
        .answer-text {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            line-height: 1.6;
            white-space: pre-wrap;
            min-height: 200px;
        }
        .attachment-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #DAA520;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border: 1px solid #DAA520;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .attachment-link:hover {
            background: #DAA520;
            color: #000;
        }
        .grade-form {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1.5rem;
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .grade-input {
            font-size: 1.2rem;
            font-weight: 600;
            text-align: center;
        }
        .back-btn {
            background: #D1A11F;
            color: #000;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .back-btn:hover {
            background: #DAA520;
            color: #000;
        }
        .grade-btn {
            background: #DAA520;
            color: #000;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.2s;
        }
        .grade-btn:hover {
            background: #D1A11F;
            color: #000;
        }
        .meta-info {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            font-size: 0.9rem;
            color: #666;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .max-score-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.9rem;
            color: #856404;
        }
    </style>
</head>
<body>
    <?= view('templates/header') ?>

    <div class="container my-4">
        <!-- Assignment Header -->
        <div class="card mb-4">
            <div class="grade-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="assignment-title">
                            <i class="bi bi-pencil-square me-2"></i>
                            Grade Assignment: <?= esc($submission->assignment_title ?? '') ?>
                        </div>
                        <div class="student-info">
                            <i class="bi bi-person-circle me-2"></i>
                            <?= esc($submission->student_name ?? '') ?> (<?= esc($submission->student_email ?? '') ?>)
                        </div>
                        <div class="meta-info">
                            <div class="meta-item">
                                <i class="bi bi-book"></i>
                                <span><?= esc($submission->course_title ?? '') ?> (<?= esc($submission->course_code ?? '') ?>)</span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-calendar-check"></i>
                                <span><?= $submission->submission_date ? date('M j, Y g:i A', strtotime($submission->submission_date)) : 'No date' ?></span>
                            </div>
                        </div>
                    </div>
                    <a href="<?= site_url('teacher/assignments/view/' . ($submission->assignment_id ?? '')) ?>" class="back-btn">
                        <i class="bi bi-arrow-left me-1"></i>Back to Submissions
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Student Submission -->
            <div class="col-lg-6 mb-4">
                <div class="submission-card">
                    <div class="submission-header">
                        <h5 class="mb-0">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            Student's Submission
                        </h5>
                    </div>
                    <div class="submission-body">
                        <?php if (!empty($submission->answer_text)): ?>
                            <div class="answer-text">
                                <?= nl2br(esc($submission->answer_text)) ?>
                            </div>
                        <?php else: ?>
                            <div class="text-muted text-center py-3">
                                <i class="bi bi-file-earmark-x" style="font-size: 2rem;"></i>
                                <p class="mt-2">No text answer provided</p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($submission->attachment)): ?>
                            <div class="mb-3">
                                <a href="<?= base_url('uploads/' . $submission->attachment) ?>" 
                                   class="attachment-link" target="_blank">
                                    <i class="bi bi-file-earmark"></i>
                                    View Attachment
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Grade Form -->
            <div class="col-lg-6 mb-4">
                <div class="grade-form">
                    <h5 class="mb-4">
                        <i class="bi bi-award me-2"></i>
                        Grade Assignment
                    </h5>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?= esc(session()->getFlashdata('error')) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= site_url('teacher/grade/' . ($submission->id ?? '')) ?>">
                        <div class="mb-4">
                            <label for="grade" class="form-label">
                                <i class="bi bi-star me-1"></i>
                                Grade
                            </label>
                            <div class="max-score-info mb-2">
                                Maximum Score: <?= esc($submission->max_score ?? '100') ?> points
                            </div>
                            <input type="number" 
                                   class="form-control grade-input" 
                                   id="grade" 
                                   name="grade" 
                                   min="0" 
                                   max="<?= esc($submission->max_score ?? '100') ?>"
                                   step="0.01"
                                   value="<?= esc($submission->grade ?? '') ?>"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label for="feedback" class="form-label">
                                <i class="bi bi-chat-text me-1"></i>
                                Feedback (Optional)
                            </label>
                            <textarea class="form-control" 
                                      id="feedback" 
                                      name="feedback" 
                                      rows="6" 
                                      placeholder="Provide feedback to the student..."><?= esc($submission->feedback ?? '') ?></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn grade-btn flex-fill">
                                <i class="bi bi-check-circle me-2"></i>
                                Submit Grade
                            </button>
                            <a href="<?= site_url('teacher/assignments/view/' . ($submission->assignment_id ?? '')) ?>" 
                               class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
