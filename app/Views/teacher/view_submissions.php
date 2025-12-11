<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Submissions - <?= esc($assignment->title ?? '') ?></title>
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
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
        }
        .assignment-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .assignment-meta {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            font-size: 0.9rem;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .submission-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .submission-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .submission-header {
            background: #f8f9fa;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e9ecef;
        }
        .student-name {
            font-weight: 600;
            color: #333;
            font-size: 1.1rem;
        }
        .submission-meta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            font-size: 0.9rem;
            color: #666;
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
        .grade-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 1rem;
            border-radius: 6px;
            margin-top: 1rem;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .status-submitted {
            background: #d1ecf1;
            color: #0c5460;
        }
        .status-graded {
            background: #d4edda;
            color: #155724;
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
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        .grade-btn:hover {
            background: #D1A11F;
            color: #000;
        }
    </style>
</head>
<body>
    <?= view('templates/header') ?>

    <div class="container my-4">
        <!-- Assignment Header -->
        <div class="card mb-4">
            <div class="assignment-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="assignment-title">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            <?= esc($assignment->title ?? '') ?> - Submissions
                        </div>
                        <div class="assignment-meta">
                            <div class="meta-item">
                                <i class="bi bi-book"></i>
                                <span><?= esc($assignment->course_title ?? '') ?> (<?= esc($assignment->course_code ?? '') ?>)</span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-calendar-event"></i>
                                <span><?= $assignment->due_date ? date('M j, Y g:i A', strtotime($assignment->due_date)) : 'No due date' ?></span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-people"></i>
                                <span><?= count($submissions ?? []) ?> Submissions</span>
                            </div>
                        </div>
                    </div>
                    <a href="<?= site_url('teacher/assignments') ?>" class="back-btn">
                        <i class="bi bi-arrow-left me-1"></i>Back to Assignments
                    </a>
                </div>
            </div>
        </div>

        <!-- Submissions Table -->
        <div class="table-wrap mb-4">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Submission Date</th>
                        <th>Answer</th>
                        <th>Attachment</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($submissions)): ?>
                        <?php foreach ($submissions as $submission): ?>
                            <tr>
                                <td><?= esc($submission['student_name'] ?? '') ?></td>
                                <td><?= esc($submission['student_email'] ?? '') ?></td>
                                <td><?= $submission['submission_date'] ? date('M j, Y g:i A', strtotime($submission['submission_date'])) : date('M j, Y g:i A') ?></td>
                                <td>
                                    <?php if (!empty($submission['answer_text'])): ?>
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#answerModal<?= $submission['id'] ?? '' ?>">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">No answer</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($submission['attachment'])): ?>
                                        <a href="<?= site_url('teacher/submissions/download/' . $submission['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">None</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="number" 
                                               class="form-control form-control-sm" 
                                               style="width: 80px;"
                                               id="grade_<?= $submission['id'] ?? '' ?>"
                                               name="grade_<?= $submission['id'] ?? '' ?>"
                                               min="0" 
                                               max="100"
                                               step="0.01"
                                               value="<?= esc($submission['grade'] ?? '') ?>"
                                               placeholder="0/100">
                                        <button class="btn btn-sm btn-success" 
                                                onclick="saveGrade(<?= $submission['id'] ?? '' ?>)">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr> 
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-3">
                                <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">No submissions yet.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Answer Modals -->
        <?php if (!empty($submissions)): ?>
            <?php foreach ($submissions as $submission): ?>
                <?php if (!empty($submission['answer_text'])): ?>
                    <div class="modal fade" id="answerModal<?= $submission['id'] ?? '' ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="bi bi-person-circle me-2"></i>
                                        <?= esc($submission['student_name'] ?? '') ?>'s Answer
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="answer-text">
                                        <?= nl2br(esc($submission['answer_text'])) ?>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function saveGrade(submissionId) {
            const gradeInput = document.getElementById('grade_' + submissionId);
            const grade = gradeInput.value;
            
            if (!grade || grade < 0 || grade > 100) {
                alert('Please enter a valid grade between 0 and 100');
                return;
            }
            
            // Create form data
            const formData = new FormData();
            formData.append('grade', grade);
            formData.append('submission_id', submissionId);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            
            // Send AJAX request
            fetch('<?= site_url('teacher/saveGrade') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                    alert.style.zIndex = '9999';
                    alert.innerHTML = `
                        <i class="bi bi-check-circle me-2"></i>
                        Grade saved successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(alert);
                    
                    // Remove alert after 3 seconds
                    setTimeout(() => {
                        alert.remove();
                    }, 3000);
                } else {
                    alert('Error saving grade: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving grade. Please try again.');
            });
        }
    </script>
</body>
</html>
