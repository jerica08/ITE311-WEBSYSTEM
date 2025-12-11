<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($course['title'] ?? '') ?> - Course Overview</title>
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
            padding: 2rem;
            border-radius: 12px 12px 0 0;
        }
        .course-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .course-code {
            font-size: 1.1rem;
            opacity: 0.8;
        }
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #D1A11F;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .assignments-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #DAA520;
        }
        .assignment-item {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            background: #f8f9fa;
            transition: background 0.2s;
        }
        .assignment-item:hover {
            background: #e9ecef;
        }
        .assignment-title {
            font-weight: 500;
            color: #333;
        }
        .assignment-due {
            color: #666;
            font-size: 0.9rem;
        }
        .assignment-points {
            background: #D1A11F;
            color: #000;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .view-all-btn {
            background: #D1A11F;
            color: #000;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .view-all-btn:hover {
            background: #DAA520;
            color: #000;
        }
    </style>
</head>
<body>
    <?= view('templates/header') ?>

    <div class="container my-4">
        <!-- Course Header -->
        <div class="card mb-4">
            <div class="course-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="course-title">
                            <i class="bi bi-book me-2"></i>
                            <?= esc($course['title'] ?? '') ?>
                        </div>
                        <div class="course-code">
                            <?= esc($course['code'] ?? '') ?> COURSE CODE
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-number"><?= $totalAssignments ?? 0 ?></div>
                <div class="stat-label">Assignments</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $pendingAssignments ?? 0 ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= esc($course['code'] ?? '') ?></div>
                <div class="stat-label">Course Code</div>
            </div>
        </div>

        <!-- Upcoming Assignments -->
        <div class="assignments-section">
            <h5 class="section-title">
                <i class="bi bi-calendar-check me-2"></i>Upcoming Assignments
            </h5>
            <?php if (!empty($upcomingAssignments)): ?>
                <?php foreach ($upcomingAssignments as $assignment): ?>
                    <div class="assignment-item">
                        <div class="flex-grow-1">
                            <div class="assignment-title"><?= esc($assignment['title']) ?></div>
                            <div class="assignment-due">
                                <i class="bi bi-calendar-event me-1"></i>
                                <?= $assignment['due_date'] ? date('M j', strtotime($assignment['due_date'])) : 'No due date' ?>
                            </div>
                        </div>
                        <span class="assignment-points"><?= esc($assignment['max_score'] ?? '100') ?> pts</span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center text-muted py-3">
                    <i class="bi bi-calendar-x" style="font-size: 2rem;"></i>
                    <p class="mt-2">No upcoming assignments</p>
                </div>
            <?php endif; ?>
            
            <div class="text-center mt-4">
                <a href="<?= site_url('student/course/' . ($course['id'] ?? '') . '/assignments') ?>" class="view-all-btn">
                    <i class="bi bi-arrow-right me-1"></i>View All Assignments
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
