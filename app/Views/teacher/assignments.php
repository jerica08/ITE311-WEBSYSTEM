<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments - Teacher Dashboard</title>
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
        .btn-dark-gold {
            background-color: #000;
            border-color: #000;
            color: #fff;
        }
        .btn-dark-gold:hover {
            background-color: #333;
            border-color: #333;
            color: #fff;
        }
        .modal-header {
            background-color: #D1A11F;
            color: #000;
            border-bottom: 2px solid #DAA520;
        }
        .modal-title {
            color: #000;
        }
        .btn-close {
            background-color: transparent;
            border: none;
            color: #000;
        }
    </style>
</head>
<body>
    <?= view('templates/header') ?>

        <!-- Assignment Management Header -->
        <div class="section-title">
            <i class="bi bi-clipboard-check me-2"></i>Assignment Management
            <button class="btn btn-dark-gold btn-sm float-end" type="button" data-bs-toggle="modal" data-bs-target="#createAssignmentModal">
                <i class="bi bi-plus-circle me-2"></i>Create New Assignment
            </button>
        </div>

        <!-- Assignments Table -->
        <div class="table-wrap mb-4">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th style="width:200px;">Course</th>
                        <th style="width:150px;">Due Date</th>
                        <th style="width:100px;">Max Score</th>
                        <th style="width:100px;">Status</th>
                        <th style="width:120px;">Attachment</th>
                        <th style="width:120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($assignments ?? [])): ?>
                        <?php foreach ($assignments as $assignment): ?>
                            <tr>
                                <td><?= esc($assignment['title'] ?? '-') ?></td>
                                <td><?= esc($assignment['course_title'] ?? '-') ?> (<?= esc($assignment['course_code'] ?? '-') ?>)</td>
                                <td><?= esc($assignment['due_date'] ?? '-') ?></td>
                                <td><?= esc($assignment['max_score'] ?? '-') ?></td>
                                <td>
                                    <?php 
                                    $status = $assignment['status'] ?? 'active';
                                    if ($status === 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= esc(ucfirst($status)) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($assignment['attachment_file'])): ?>
                                        <a href="<?= site_url('teacher/assignments/download/' . $assignment['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Download <?= esc($assignment['attachment_file']) ?>">
                                            <i class="bi bi-download me-1"></i><?= esc($assignment['attachment_file']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No attachment</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= site_url('teacher/assignments/view/' . ($assignment['id'] ?? '')) ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i></i>View Submissions
                                        </a>
                                        <a href="<?= site_url('teacher/assignments/edit/' . ($assignment['id'] ?? '')) ?>" 
                                           class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-pencil me-1"></i>Edit
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteAssignment(<?= $assignment['id'] ?? '' ?>, '<?= esc($assignment['title'] ?? '') ?>')">
                                            <i class="bi bi-trash me-1"></i>Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-3">
                                <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">No assignments created yet.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Create Assignment Modal -->
        <div class="modal fade" id="createAssignmentModal" tabindex="-1" aria-labelledby="createAssignmentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createAssignmentModalLabel">Create New Assignment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="<?= site_url('teacher/createAssignment') ?>" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Course<span class="text-danger">*</span></label>
                                    <select name="course_id" class="form-select" required>
                                        <option value="">Select course</option>
                                        <?php foreach ($courses ?? [] as $course): ?>
                                            <option value="<?= $course['id'] ?>"><?= esc($course['title']) ?> (<?= esc($course['code']) ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Assignment Title<span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Description<span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control" rows="4" required></textarea>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Due Date<span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="due_date" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Max Score</label>
                                    <input type="number" name="max_score" class="form-control" min="1" max="1000" value="100">
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Attachment File</label>
                                    <input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                                    <small class="text-muted">Supported formats: PDF, DOC, DOCX, TXT, JPG, JPEG, PNG (Max size: 10MB)</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" style="background-color:#D1A11F;border:none;color:#000">Create Assignment</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Delete Assignment JavaScript -->
    <script>
        function deleteAssignment(assignmentId, assignmentTitle) {
            if (confirm('Are you sure you want to delete the assignment "' + assignmentTitle + '"? This action cannot be undone.')) {
                var csrfToken = '<?= csrf_hash() ?>';
                var csrfName = '<?= csrf_token() ?>';
                
                var formData = new FormData();
                formData.append(csrfName, csrfToken);
                
                fetch('<?= site_url('teacher/assignments/delete/') ?>' + assignmentId, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the assignment.');
                });
            }
        }
    </script>
</body>
</html>
