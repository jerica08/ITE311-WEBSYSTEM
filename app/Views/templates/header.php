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
                    <a href="<?= site_url('admin/dashboard') ?>">Dashboard</a>
                    <a href="<?= site_url('admin/users') ?>">User Management</a>
                    <a href="<?= site_url('admin/courses') ?>">Course Management</a>
                <?php elseif ($role === 'teacher' || $role === 'instructor'): ?>
                    <a href="<?= site_url('teacher/dashboard') ?>">Dashboard</a>
                    <a href="#">My Courses</a>
                    <a href="#">Assignments</a>
                <?php elseif ($role === 'student'): ?>
                    <a href="<?= site_url('student/dashboard') ?>">Dashboard</a>
                    <a href="#">My Classes</a>
                    <a href="#">Grades</a>
                <?php else: ?>
                    <a href="<?= site_url('/') ?>">Home</a>
                <?php endif; ?>
                <div class="dropdown">
                    <a href="#" id="notifDropdown" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        Notifications <span id="notifBadge" class="badge bg-danger d-none">0</span>
                    </a>
                    <div id="notifMenu" class="dropdown-menu dropdown-menu-end" aria-labelledby="notifDropdown" style="min-width:320px; max-height:360px; overflow:auto;"></div>
                </div>
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

<script>
(function(){
  // Wait for jQuery to be available before initializing

  var __csrfName = '<?= csrf_token() ?>';
  var __csrfHash = '<?= csrf_hash() ?>';

  function updateBadge(count){
    var $badge = $('#notifBadge');
    count = parseInt(count || 0, 10);
    if(count > 0){
      $badge.text(count).removeClass('d-none');
    } else {
      $badge.text('0').addClass('d-none');
    }
  }

  function renderList(items){
    var $menu = $('#notifMenu');
    $menu.empty();
    if(!items || !items.length){
      $menu.append('<div class="dropdown-item text-muted">No notifications</div>');
      return;
    }
    items.forEach(function(n){
      var $item = $('<div class="dropdown-item p-0"></div>');
      var $alert = $('<div class="alert alert-info m-2 mb-0 d-flex justify-content-between align-items-start"></div>');
      var $text = $('<div class="me-2"></div>').text(n.message);
      var $btn = $('<button type="button" class="btn btn-sm btn-outline-secondary">Mark as Read</button>');
      $btn.on('click', function(){
        var data = {}; data[__csrfName] = __csrfHash;
        $.post('<?= site_url('notifications/mark_read') ?>' + '/' + n.id, data, function(r){
          if(r && r.success){
            // Optimistic UI update
            $item.remove();
            var current = parseInt($('#notifBadge').text() || '0', 10) || 0;
            updateBadge(Math.max(0, current - 1));
            // Refresh from server to ensure perfect sync (handles any duplicates or server-side filters)
            fetchNotifications();
            if(r.csrf_token && r.csrf_hash){
              __csrfName = r.csrf_token; __csrfHash = r.csrf_hash;
            }
          }
        }).fail(function(){ /* optionally show error */ });
      });
      $alert.append($text).append($btn);
      $item.append($alert);
      $menu.append($item);
    });
  }

  function fetchNotifications(){
    $.get('<?= site_url('notifications') ?>', function(res){
      if(!res || res.success !== true) return;
      updateBadge(res.unread_count || 0);
      renderList(res.notifications || []);
      if(res.csrf_token && res.csrf_hash){
        __csrfName = res.csrf_token; __csrfHash = res.csrf_hash;
      }
    });
  }

  function initNotif(){
    var $ = window.jQuery;
    if(!$) return false;
    $(function(){
      fetchNotifications();
      $('#notifDropdown').on('show.bs.dropdown', fetchNotifications);
      setInterval(fetchNotifications, 60000);
    });
    return true;
  }

  if(!initNotif()){
    var __notifTries = 0;
    var __notifTimer = setInterval(function(){
      __notifTries++;
      if(initNotif()){
        clearInterval(__notifTimer);
      } else if(__notifTries > 60){ // ~30s max
        clearInterval(__notifTimer);
      }
    }, 500);
  }
})();
</script>
