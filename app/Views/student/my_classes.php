<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Classes - Student Dashboard</title>
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
        .class-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s ease-in-out;
        }
        .class-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .schedule-badge {
            background-color: #f8f9fa;
            border-left: 4px solid #D1A11F;
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 0 8px 8px 0;
        }
        .class-header {
            background: linear-gradient(135deg, #D1A11F 0%, #DAA520 100%);
            color: #000;
            border-radius: 8px 8px 0 0;
            padding: 15px;
        }
        .class-info {
            background-color: #fff;
            border-radius: 0 0 8px 8px;
            padding: 20px;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <?= view('templates/header') ?>

    <div class="container my-4">
        <!-- Search Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="<?= site_url('student/my-classes') ?>" id="filterForm">
                    <div class="row g-3 align-items-center">
                        <!-- Search Input -->
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" 
                                       class="form-control border-start-0" 
                                       id="search" 
                                       name="search" 
                                       placeholder="Search title or code"
                                       value="<?= esc($search ?? '') ?>">
                            </div>
                        </div>
                        
                        <!-- Apply Button -->
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-dark w-100">
                                Apply
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- My Classes Header -->
        <div class="section-title mb-4">
            <i class="bi bi-calendar-week me-2"></i>My Classes
        </div>

        <!-- Classes Grid -->
        <?php if (!empty($classes ?? [])): ?>
            <div class="row" id="classesGrid">
                <?php foreach ($classes as $class): ?>
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="card class-card h-100">
                            <div class="class-header">
                                <h5 class="mb-1"><?= esc($class['title']) ?></h5>
                                <small class="opacity-75"><?= esc($class['code']) ?></small>
                            </div>
                            <div class="class-info">
                                <div class="mb-3">
                                    <span class="status-badge bg-success text-white">
                                        <i class="bi bi-check-circle me-1"></i>Enrolled
                                    </span>
                                    <small class="text-muted ms-2">
                                        Since <?= date('M j, Y', strtotime($class['enrolled_at'])) ?>
                                    </small>
                                </div>

                                <!-- Class Schedule -->
                                <?php if (!empty($class['class_schedule'])): ?>
                                    <div class="schedule-badge">
                                        <h6 class="mb-2">
                                            <i class="bi bi-clock me-2"></i>Class Schedule
                                        </h6>
                                        <p class="mb-0 small"><?= nl2br(esc($class['class_schedule'])) ?></p>
                                    </div>
                                <?php endif; ?>

                                <!-- Course Details -->
                                <div class="row g-2 mb-3">
                                    <?php if (!empty($class['unit'])): ?>
                                        <div class="col-6">
                                            <small class="text-muted">Units</small>
                                            <div class="fw-bold"><?= esc($class['unit']) ?></div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($class['course_level'])): ?>
                                        <div class="col-6">
                                            <small class="text-muted">Level</small>
                                            <div class="fw-bold"><?= esc(ucfirst($class['course_level'])) ?></div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($class['department'])): ?>
                                        <div class="col-6">
                                            <small class="text-muted">Department</small>
                                            <div class="fw-bold"><?= esc($class['department']) ?></div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($class['program'])): ?>
                                        <div class="col-6">
                                            <small class="text-muted">Program</small>
                                            <div class="fw-bold"><?= esc($class['program']) ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Academic Year -->
                                <?php if (!empty($class['academic_year'])): ?>
                                    <div class="mb-3">
                                        <small class="text-muted">Academic Year</small>
                                        <div class="fw-bold"><?= esc($class['academic_year']) ?></div>
                                    </div>
                                <?php endif; ?>

                                <!-- Course Period -->
                                <?php if (!empty($class['course_start_date']) && !empty($class['course_end_date'])): ?>
                                    <div class="mb-3">
                                        <small class="text-muted">Course Period</small>
                                        <div class="fw-bold">
                                            <?= date('M j, Y', strtotime($class['course_start_date'])) ?> - 
                                            <?= date('M j, Y', strtotime($class['course_end_date'])) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Action Buttons -->
                                <div class="d-flex gap-2">
                                    <a href="<?= site_url('student/course/' . $class['id'] . '/assignments') ?>" 
                                       class="btn btn-sm btn-primary flex-fill">
                                        <i class="bi bi-clipboard-check me-1"></i>Assignments
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <?php if (!empty($search ?? '')): ?>
                <!-- No Results Found After Filtering -->
                <div class="empty-state">
                    <div class="card border-0 bg-white shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-search"></i>
                            <h4 class="text-muted">No Classes Found</h4>
                            <p class="text-muted">
                                No classes match your search criteria. Try adjusting your filters or search terms.
                            </p>
                            <a href="<?= site_url('student/my-classes') ?>" class="btn btn-primary">
                                <i class="bi bi-x-circle me-2"></i>Clear Filters
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="card border-0 bg-white shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-inbox"></i>
                            <h4 class="text-muted">No Classes Enrolled</h4>
                            <p class="text-muted">
                                You haven't enrolled in any classes yet. 
                                Visit the course catalog to find and enroll in courses.
                            </p>
                            <a href="<?= site_url('student/dashboard') ?>" 
                               class="btn btn-primary">
                                <i class="bi bi-search me-2"></i>Browse Courses
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Manual Search -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const filterForm = document.getElementById('filterForm');
            
            // Allow Enter key to submit the search form
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    filterForm.submit();
                }
            });
        });
    </script>
</body>
</html>
