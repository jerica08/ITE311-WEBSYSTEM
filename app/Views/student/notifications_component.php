<!-- Notification Component - Can be included in any student page -->
<div class="notification-dropdown position-relative">
    <!-- Notification Bell Icon -->
    <button class="btn btn-outline-light position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-bell"></i>
        <?php if ($unreadCount > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationBadge">
                <?= $unreadCount > 99 ? '99+' : $unreadCount ?>
            </span>
        <?php endif; ?>
    </button>
    
    <!-- Notification Dropdown -->
    <ul class="dropdown-menu dropdown-menu-end notification-menu" aria-labelledby="notificationDropdown" style="min-width: 350px; max-height: 400px; overflow-y: auto;">
        <li class="dropdown-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-bell me-2"></i>Notifications</span>
            <?php if ($unreadCount > 0): ?>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="markAllNotificationsRead()">
                    Mark all read
                </button>
            <?php endif; ?>
        </li>
        <li><hr class="dropdown-divider"></li>
        
        <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $notification): ?>
                <li class="notification-item <?= $notification['is_read'] ? 'read' : 'unread' ?>" data-notification-id="<?= $notification['id'] ?>">
                    <div class="dropdown-item notification-content" href="#" onclick="markNotificationRead(<?= $notification['id'] ?>); return false;">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 <?= $notification['is_read'] ? 'text-muted' : 'fw-bold' ?>">
                                    <?= esc($notification['title']) ?>
                                </h6>
                                <p class="mb-1 small <?= $notification['is_read'] ? 'text-muted' : '' ?>">
                                    <?= esc($notification['message']) ?>
                                </p>
                                <small class="text-muted">
                                    <?= date('M j, Y g:i A', strtotime($notification['created_at'])) ?>
                                </small>
                            </div>
                            <?php if (!$notification['is_read']): ?>
                                <div class="ms-2">
                                    <span class="badge bg-primary">New</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>
                <div class="dropdown-item text-muted text-center">
                    <i class="bi bi-bell-slash me-2"></i>No notifications
                </div>
            </li>
        <?php endif; ?>
    </ul>
</div>

<style>
.notification-dropdown .notification-menu {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border: 1px solid #dee2e6;
}

.notification-item.unread {
    background-color: #f8f9fa;
    border-left: 3px solid #007bff;
}

.notification-item.read {
    background-color: #fff;
}

.notification-content {
    cursor: pointer;
    transition: background-color 0.2s;
}

.notification-content:hover {
    background-color: #e9ecef;
}

.notification-content h6,
.notification-content p {
    margin: 0;
}

.notification-badge {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
</style>

<script>
// Notification functions
function markNotificationRead(notificationId) {
    $.post('<?= site_url('student/notifications/mark-read') ?>', {
        notification_id: notificationId,
        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
    })
    .done(function(data) {
        if (data.success) {
            // Remove unread styling
            const $item = $('.notification-item[data-notification-id="' + notificationId + '"]');
            $item.removeClass('unread').addClass('read');
            $item.find('.badge').remove();
            
            // Update badge count
            updateNotificationBadge();
        }
    })
    .fail(function() {
        console.error('Failed to mark notification as read');
    });
}

function markAllNotificationsRead() {
    $.post('<?= site_url('student/notifications/mark-all-read') ?>', {
        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
    })
    .done(function(data) {
        if (data.success) {
            // Mark all as read
            $('.notification-item').removeClass('unread').addClass('read');
            $('.notification-item .badge').remove();
            
            // Update badge
            $('#notificationBadge').remove();
        }
    })
    .fail(function() {
        console.error('Failed to mark all notifications as read');
    });
}

function updateNotificationBadge() {
    const currentCount = parseInt($('#notificationBadge').text()) || 0;
    if (currentCount <= 1) {
        $('#notificationBadge').remove();
    } else {
        $('#notificationBadge').text(currentCount - 1);
    }
}

// Auto-refresh notifications every 30 seconds
setInterval(function() {
    $.get('<?= site_url('student/notifications') ?>')
        .done(function(data) {
            if (data.success) {
                // Update badge
                if (data.unreadCount > 0) {
                    if ($('#notificationBadge').length === 0) {
                        $('#notificationDropdown').append('<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationBadge">' + (data.unreadCount > 99 ? '99+' : data.unreadCount) + '</span>');
                    } else {
                        $('#notificationBadge').text(data.unreadCount > 99 ? '99+' : data.unreadCount);
                    }
                } else {
                    $('#notificationBadge').remove();
                }
            }
        })
        .fail(function() {
            console.error('Failed to refresh notifications');
        });
}, 30000);
</script>
