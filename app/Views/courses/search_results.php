<?php helper('url'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Search Results</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
    <div class="container py-5">
        <h2 class="mb-4">Results for <?= esc($searchTerm ?: 'all courses') ?></h2>
        <div class="row mb-4">
            <div class="col-md-6">
                <form id="searchForm" class="d-flex" method="get" action="<?= site_url('course/search') ?>">
                    <?= csrf_field() ?>
                    <div class="input-group">
                        <input type="text" id="searchInput" name="search_term" class="form-control" placeholder="Search courses..." value="<?= esc($searchTerm) ?>">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="coursesContainer" class="row g-4">
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $course): ?>
                    <div class="col-md-4">
                        <div class="card course-card h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= esc($course['title'] ?? 'Untitled course') ?></h5>
                                <p class="card-text text-muted flex-grow-1">
                                    <?= esc($course['description'] ?? 'No description available.') ?>
                                </p>
                                <p class="text-muted small mb-1">Instructor ID: <?= esc($course['instructor_id'] ?? '-') ?></p>
                                <p class="text-muted small">Created at: <?= esc($course['created_at'] ?? '-') ?></p>
                                <a href="<?= site_url('admin/course/' . (int) ($course['id'] ?? 0) . '/upload') ?>" class="mt-auto btn btn-sm btn-primary">
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

            function filterCards(value) {
                const term = value.toLowerCase();
                $('.course-card').each(function () {
                    const text = $(this).text().toLowerCase();
                    $(this).closest('.col-md-4').toggle(text.indexOf(term) > -1);
                });
            }

            $searchInput.on('keyup', function () {
                filterCards($(this).val());
            });

            $('#searchForm').on('submit', function (e) {
                e.preventDefault();
                const searchTerm = $searchInput.val();
                $.get('<?= site_url('course/search') ?>', { search_term: searchTerm }, function (data) {
                    $coursesContainer.empty();

                    const courses = data.courses ?? [];
                    if (courses.length > 0) {
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
                    } else {
                        $coursesContainer.html('<div class="col-12"><div class="alert alert-info">No courses found matching your search.</div></div>');
                    }
                    filterCards(searchTerm);
                }, 'json');
            });
        });
    </script>
</body>
</html>
