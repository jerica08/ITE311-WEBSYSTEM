<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Search Results</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            min-height: 100vh;
            background: #e6e3dc;
            font-family: 'Times New Roman', serif;
        }
        .topbar {
            background: #000;
            color: #fff;
            padding: 0.6rem 1rem;
            font-size: 1rem;
            font-weight: 600;
        }
        .subbar {
            background: #DAA520;
            color: #000;
            padding: 0.6rem 1rem;
            font-weight: 600;
        }
        .section-title {
            background: #D1A11F;
            color: #000;
            padding: 0.5rem 0.75rem;
            border-radius: 12px;
            font-weight: 700;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
        }
        .page-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem 4rem;
        }
        .course-card {
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.1);
        }
        .course-card .card-body {
            display: flex;
            flex-direction: column;
            min-height: 250px;
        }
        .course-card .btn-primary {
            background: #DAA520;
            border: none;
            color: #000;
            font-weight: 600;
        }
        .course-card .btn-primary:hover {
            background: #b4801a;
        }
        .search-box .form-control {
            border-radius: 10px 0 0 10px;
            border: 2px solid #DAA520;
        }
        .search-box .btn {
            border-radius: 0 10px 10px 0;
            border: 2px solid #DAA520;
            border-left: none;
            background: #DAA520;
            color: #000;
        }
        @media (max-width: 768px) {
            .search-box .input-group {
                flex-direction: column;
            }
            .search-box .form-control,
            .search-box .btn {
                border-radius: 10px;
                width: 100%;
            }
            .search-box .btn {
                margin-top: 0.5rem;
                border-left: 2px solid #DAA520;
            }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="container-fluid">Kawas National High School</div>
    </div>
    <div class="subbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div>Learning Management System</div>
            <div class="text-end">
                <span class="fw-bold me-2">Courses</span>
            </div>
        </div>
    </div>
    <div class="page-container">
        <div class="section-title mb-4">Results for <?= esc($searchTerm ?: 'all courses') ?></div>
        <div class="search-box mb-4">
            <form id="searchForm" class="input-group" method="get" action="<?= site_url('course') ?>">
                <?= csrf_field() ?>
                <input type="text" id="searchInput" name="search_term" class="form-control" placeholder="Search courses..." value="<?= esc($searchTerm) ?>">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search me-1"></i> Search
                </button>
            </form>
        </div>

        <div id="coursesContainer" class="row g-4">
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $course): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card course-card h-100">
                            <div class="card-body">
                                <h5 class="card-title fs-5 mb-3"><?= esc($course['title'] ?? 'Untitled course') ?></h5>
                                <p class="card-text text-muted flex-grow-1"><?= esc($course['description'] ?? 'No description available.') ?></p>
                                <p class="text-muted small mb-1">Instructor ID: <?= esc($course['instructor_id'] ?? '-') ?></p>
                                <p class="text-muted small mb-3">Created at: <?= esc($course['created_at'] ?? '-') ?></p>
                                <a href="<?= site_url('admin/course/' . (int) ($course['id'] ?? 0) . '/upload') ?>" class="btn btn-primary mt-auto shadow-sm">
                                    View course
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">No courses matched your search.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function () {
            const $searchInput = $('#searchInput');
            const $coursesContainer = $('#coursesContainer');
            let debounceTimer;

            function renderCourses(courses) {
                $coursesContainer.empty();
                if (courses.length === 0) {
                    $coursesContainer.html('<div class="col-12"><div class="alert alert-info">No courses found matching your search.</div></div>');
                    return;
                }
                $.each(courses, function (index, course) {
                    const description = (course.description ?? '').substring(0, 160);
                    const card = `
                        <div class="col-md-4">
                            <div class="card course-card h-100">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">${course.title ?? 'Untitled course'}</h5>
                                    <p class="card-text text-muted flex-grow-1">${description}${(course.description && course.description.length > 160 ? '...' : '')}</p>
                                    <p class="text-muted small mb-1">Instructor ID: ${course.instructor_id ?? '-'}</p>
                                    <p class="text-muted small">Created at: ${course.created_at ?? '-'}</p>
                                    <a class="btn btn-sm btn-primary mt-auto" href="<?= site_url('admin/course') ?>/${course.id ?? 0}/upload">View course</a>
                                </div>
                            </div>
                        </div>
                    `;
                    $coursesContainer.append(card);
                });
            }

            function performSearch(term) {
                $.get('<?= site_url('course') ?>', { search_term: term }, function (data) {
                    const courses = data.courses ?? [];
                    renderCourses(courses);
                }, 'json');
            }

            $searchInput.on('keyup', function () {
                const term = $(this).val().trim();
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    performSearch(term);
                }, 300);
            });

            $('#searchForm').on('submit', function (e) {
                e.preventDefault();
                clearTimeout(debounceTimer);
                performSearch($searchInput.val().trim());
            });
        });
    </script>
</body>
</html>
