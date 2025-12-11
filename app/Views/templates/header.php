<?php
helper('url');
$session   = session();
$isLogged  = (bool) ($session->get('isLoggedIn') ?? $session->get('logged_in') ?? false);
$role      = strtolower((string) ($session->get('role') ?? $session->get('user_role') ?? ''));
$name      = (string) ($session->get('name') ?? $session->get('user_name') ?? '');
?>
<style>
    .topbar { background:#000; color:#fff; padding:.5rem 1rem; font-family: 'Times New Roman', serif; }
    .subbar { background:#DAA520; color:#fff; padding:.5rem 1rem; font-family: 'Times New Roman', serif; }
    .menu a { color:#fff; text-decoration:none; padding:.4rem .8rem; border-radius:.3rem; }
    .menu a.active, .menu a:hover { background: rgba(0,0,0,.15); }
    .logout-btn { background:#E74C3C; color:#fff; border:none; padding:.35rem .7rem; border-radius:.3rem; }
    .notif-badge { background:#DC3545; color:#fff; border-radius:999px; padding:0 .45rem; font-size:.75rem; margin-left:.25rem; }
    .dropdown-menu.show { display:block; }
</style>

<div class="topbar">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="fw-bold">Kawas National University</div>
        <?php if ($isLogged && $name): ?>
            <div class="small">Signed in as <strong><?= esc($name) ?></strong> (<?= esc(ucfirst($role)) ?>)</div>
        <?php endif; ?>
    </div>
</div>
<div class="subbar">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="fw-bold">Learning Management System</div>
        <div class="menu d-flex align-items-center gap-2">
            <?php if (!$isLogged): ?>
                <a href="<?= site_url('/') ?>" class="<?= (uri_string() === '' ? 'active' : '') ?>">Home</a>
                <a href="<?= site_url('about') ?>">About</a>
                <a href="<?= site_url('contact') ?>">Contact</a>
                <a href="<?= site_url('login') ?>">Login</a>
                <a href="<?= site_url('register') ?>">Sign Up</a>
            <?php else: ?>
                <?php if ($role === 'admin'): ?>
                    <a href="<?= site_url('dashboard') ?>">Dashboard</a>
                    <a href="<?= site_url('admin/users') ?>">User Management</a>
                    <a href="<?= site_url('admin/courses') ?>">Course Management</a>
                <?php elseif ($role === 'teacher' || $role === 'instructor'): ?>
                    <a href="<?= site_url('teacher/dashboard') ?>" class="<?= (uri_string() === 'teacher/dashboard' ? 'active' : '') ?>">Dashboard</a>
                    <a href="<?= site_url('teacher/courses') ?>" class="<?= (strpos(uri_string(), 'teacher/courses') === 0 ? 'active' : '') ?>">My Courses</a>
                    <a href="<?= site_url('teacher/assignments') ?>" class="<?= (strpos(uri_string(), 'teacher/assignments') === 0 ? 'active' : '') ?>">Assignments</a>
                <?php elseif ($role === 'student'): ?>
                    <a href="<?= site_url('dashboard') ?>">Dashboard</a>
                    <a href="<?= site_url('student/my-classes') ?>" class="<?= (uri_string() === 'student/my-classes' ? 'active' : '') ?>">My Classes</a>
                    <a href="<?= site_url('student/assignments') ?>" class="<?= (strpos(uri_string(), 'student/assignments') === 0 ? 'active' : '') ?>">Assignments</a>
                <?php else: ?>
                    <a href="<?= site_url('/') ?>">Home</a>
                <?php endif; ?>
                <div class="dropdown position-relative">
                    <a href="#" id="notifDropdown" class="dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                        Notifications <span id="notifBadge" class="badge bg-danger d-none">0</span>
                    </a>
                    <div id="notifMenu" class="dropdown-menu dropdown-menu-end" aria-labelledby="notifDropdown" style="min-width:320px; max-height:360px; overflow:auto;"></div>
                </div>
                <a href="<?= site_url('profile') ?>" class="btn btn-sm btn-outline-light ms-2 <?= (uri_string() === 'profile' ? 'active' : '') ?>" title="Profile">
                    <i class="bi bi-person-circle"></i>
                </a>
                <a href="<?= site_url('logout') ?>" class="btn btn-sm logout-btn ms-2">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container my-3">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const notifToggleEl = document.getElementById('notifDropdown');
    const notifMenuEl   = document.getElementById('notifMenu');
    const notifBadgeEl  = document.getElementById('notifBadge');
    const notifUrl      = '<?= site_url('notifications') ?>';
    const markUrlBase   = '<?= site_url('notifications/mark_read') ?>';
    const userId        = '<?= $session->get('user_id') ?? '' ?>';
    let csrfName        = '<?= csrf_token() ?>';
    let csrfHash        = '<?= csrf_hash() ?>';
    let refreshTimer    = null;

    if (!notifToggleEl || !notifMenuEl || !userId) {
        return;
    }

    const hasBootstrapDropdown = typeof bootstrap !== 'undefined' && bootstrap.Dropdown;
    let dropdownInstance = null;

    if (hasBootstrapDropdown) {
        dropdownInstance = new bootstrap.Dropdown(notifToggleEl, { autoClose: 'outside' });
        notifToggleEl.addEventListener('show.bs.dropdown', handleToggleOpen);
    } else {
        notifToggleEl.addEventListener('click', function (e) {
            e.preventDefault();
            toggleMenu();
        });
        document.addEventListener('click', function (e) {
            if (!notifMenuEl.contains(e.target) && !notifToggleEl.contains(e.target)) {
                notifMenuEl.classList.remove('show');
                notifToggleEl.setAttribute('aria-expanded', 'false');
            }
        });
    }

    function toggleMenu() {
        const willShow = !notifMenuEl.classList.contains('show');
        notifMenuEl.classList.toggle('show');
        notifToggleEl.setAttribute('aria-expanded', willShow ? 'true' : 'false');
        if (willShow) {
            fetchNotifications();
        }
    }

    function handleToggleOpen() {
        fetchNotifications();
    }

    function fetchNotifications() {
        fetch(notifUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.ok ? response.json() : Promise.reject(response.statusText))
        .then(data => {
            if (data && data.success) {
                if (typeof data.unread_count !== 'undefined') {
                    updateBadge(data.unread_count);
                }
                if (data.csrf_token && data.csrf_hash) {
                    csrfName = data.csrf_token;
                    csrfHash = data.csrf_hash;
                }
                renderNotifications(data.notifications || []);
            } else {
                renderNotifications([]);
                updateBadge(0);
            }
        })
        .catch(() => {
            renderNotifications([]);
            updateBadge(0);
        });
    }

    function updateBadge(count) {
        if (!notifBadgeEl) return;
        const value = parseInt(count, 10) || 0;
        if (value > 0) {
            notifBadgeEl.textContent = value;
            notifBadgeEl.classList.remove('d-none');
        } else {
            notifBadgeEl.classList.add('d-none');
        }
    }

    function renderNotifications(list) {
        notifMenuEl.innerHTML = '';
        if (!list || list.length === 0) {
            const emptyState = document.createElement('div');
            emptyState.className = 'dropdown-item text-muted';
            emptyState.textContent = 'No notifications';
            notifMenuEl.appendChild(emptyState);
            return;
        }

        list.forEach(notification => {
            const item = document.createElement('div');
            item.className = 'dropdown-item py-2';

            const message = document.createElement('div');
            message.textContent = notification.message || 'Notification';
            message.className = 'fw-semibold';

            const meta = document.createElement('div');
            meta.className = 'small text-muted';
            if (notification.created_at) {
                const date = new Date(notification.created_at);
                meta.textContent = isNaN(date.getTime()) ? notification.created_at : date.toLocaleString();
            } else {
                meta.textContent = '';
            }

            const actions = document.createElement('div');
            actions.className = 'mt-2 d-flex justify-content-end';

            const markBtn = document.createElement('button');
            markBtn.type = 'button';
            markBtn.className = 'btn btn-sm btn-outline-secondary';
            markBtn.textContent = 'Mark as read';
            markBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                markAsRead(notification.id);
            });

            actions.appendChild(markBtn);
            item.appendChild(message);
            if (meta.textContent) {
                item.appendChild(meta);
            }
            item.appendChild(actions);

            notifMenuEl.appendChild(item);
        });
    }

    function markAsRead(id) {
        if (!id) return;
        const formData = new FormData();
        formData.append(csrfName, csrfHash);

        fetch(`${markUrlBase}/${id}`, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.ok ? response.json() : Promise.reject(response.statusText))
        .then(data => {
            if (data) {
                if (data.csrf_token && data.csrf_hash) {
                    csrfName = data.csrf_token;
                    csrfHash = data.csrf_hash;
                }
                fetchNotifications();
            }
        })
        .catch(() => {
            // Ignore errors silently for now
        });
    }

    fetchNotifications();
    refreshTimer = setInterval(fetchNotifications, 60000);
});
</script>
