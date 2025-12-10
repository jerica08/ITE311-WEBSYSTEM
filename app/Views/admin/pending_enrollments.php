<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Enrollments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?= view('templates/header', ['title' => 'Pending Enrollments']) ?>

<div class="container my-4">
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Pending Enrollments</h4>
        <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-sm btn-secondary">Back to Dashboard</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($pendingEnrollments)): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Code</th>
                                <th>Enrollment Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingEnrollments as $enrollment): ?>
                                <tr>
                                    <td><?= esc($enrollment['student_name'] ?? '') ?></td>
                                    <td><?= esc($enrollment['student_email'] ?? '') ?></td>
                                    <td><?= esc($enrollment['course_title'] ?? '') ?></td>
                                    <td><?= esc($enrollment['course_code'] ?? '') ?></td>
                                    <td><?= esc($enrollment['enrollment_date'] ?? '') ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= site_url('admin/approve-enrollment/' . (int)($enrollment['id'] ?? 0)) ?>" 
                                               class="btn btn-sm btn-success" 
                                               onclick="return confirm('Approve this enrollment?')">
                                                <i class="bi bi-check-circle"></i> Approve
                                            </a>
                                            <a href="<?= site_url('admin/reject-enrollment/' . (int)($enrollment['id'] ?? 0)) ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Reject this enrollment?')">
                                                <i class="bi bi-x-circle"></i> Reject
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">No pending enrollments.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
