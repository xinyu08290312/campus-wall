<?php
require_once __DIR__ . '/includes/functions.php';

if (!is_user_logged_in()) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$user = get_current_user();
$notifications = get_notifications($user['id']);
$unread_count = get_unread_notification_count($user['id']);

// 标记所有通知为已读
if ($unread_count > 0) {
    mark_notifications_read($user['id']);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>消息通知 - <?= e(SITE_NAME) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .header-back {
            color: white;
            text-decoration: none;
            font-size: 24px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            transition: background 0.3s;
        }
        
        .header-back:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .header-title {
            flex: 1;
            font-size: 18px;
            font-weight: 600;
        }
        
        .header-action {
            color: white;
            text-decoration: none;
            font-size: 20px;
            padding: 8px;
        }
        
        .notification-tabs {
            background: white;
            display: flex;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .tab {
            flex: 1;
            padding: 14px;
            text-align: center;
            text-decoration: none;
            color: #6b7280;
            font-weight: 600;
            font-size: 14px;
            position: relative;
            transition: all 0.3s;
        }
        
        .tab.active {
            color: #667eea;
        }
        
        .tab.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 3px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 4px;
        }
        
        .badge-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .notification-list {
            background: white;
        }
        
        .notification-item {
            padding: 16px 20px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            gap: 12px;
            transition: background 0.3s;
            cursor: pointer;
        }
        
        .notification-item:hover {
            background: #f9fafb;
        }
        
        .notification-item.unread {
            background: #eff6ff;
        }
        
        .notification-item.unread:hover {
            background: #dbeafe;
        }
        
        .notification-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            flex-shrink: 0;
        }
        
        .notification-content {
            flex: 1;
            min-width: 0;
        }
        
        .notification-text {
            color: #374151;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 4px;
        }
        
        .notification-text strong {
            color: #1f2937;
        }
        
        .notification-time {
            color: #9ca3af;
            font-size: 12px;
        }
        
        .notification-icon {
            font-size: 24px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #9ca3af;
        }
        
        .empty-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        
        .empty-title {
            font-size: 18px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 8px;
        }
        
        .empty-text {
            font-size: 14px;
        }
        
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            display: flex;
            justify-content: space-around;
            padding: 8px 0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            z-index: 100;
        }
        
        .nav-item {
            text-align: center;
            padding: 8px 16px;
            color: #6b7280;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .nav-item.active {
            color: #667eea;
        }
        
        .nav-item-icon {
            font-size: 24px;
            margin-bottom: 4px;
        }
        
        .nav-item-text {
            font-size: 12px;
        }
        
        .content-wrapper {
            padding-bottom: 80px;
        }
        
        .system-notification {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        
        .system-icon {
            font-size: 24px;
        }
        
        .system-text {
            flex: 1;
            font-size: 14px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php" class="header-back">←</a>
        <div class="header-title">消息通知</div>
        <?php if ($unread_count > 0): ?>
        <span class="badge badge-primary"><?= $unread_count ?></span>
        <?php endif; ?>
    </div>
    
    <div class="content-wrapper">
        <div class="notification-tabs">
            <a href="#" class="tab active">
                全部
                <?php if (count($notifications) > 0): ?>
                <span class="badge badge-primary"><?= count($notifications) ?></span>
                <?php endif; ?>
            </a>
            <a href="#" class="tab">点赞</a>
            <a href="#" class="tab">评论</a>
            <a href="#" class="tab">系统</a>
        </div>
        
        <?php if (empty($notifications)): ?>
        <div class="empty-state">
            <div class="empty-icon">🔔</div>
            <div class="empty-title">暂无消息</div>
            <div class="empty-text">快去发布动态，与同学们互动吧！</div>
        </div>
        <?php else: ?>
        <div class="notification-list">
            <?php foreach ($notifications as $notification): ?>
            <div class="notification-item <?= $notification['is_read'] ? '' : 'unread' ?>">
                <div class="notification-avatar">
                    <?php 
                    $icon = '👤';
                    if ($notification['type'] === 'like') $icon = '❤️';
                    elseif ($notification['type'] === 'comment') $icon = '💬';
                    elseif ($notification['type'] === 'follow') $icon = '➕';
                    elseif ($notification['type'] === 'system') $icon = '📢';
                    echo $icon;
                    ?>
                </div>
                <div class="notification-content">
                    <div class="notification-text">
                        <strong><?= e($notification['from_username'] ?? '系统') ?></strong>
                        <?= e($notification['content']) ?>
                    </div>
                    <div class="notification-time"><?= format_time($notification['created_at']) ?></div>
                </div>
                <?php if ($notification['post_id']): ?>
                <div class="notification-icon">
                    <a href="detail.php?id=<?= $notification['post_id'] ?>" style="color: #667eea; text-decoration: none;">›</a>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="bottom-nav">
        <a href="index.php" class="nav-item">
            <div class="nav-item-icon">🏠</div>
            <div class="nav-item-text">首页</div>
        </a>
        <a href="post.php" class="nav-item">
            <div class="nav-item-icon">✏️</div>
            <div class="nav-item-text">发布</div>
        </a>
        <a href="messages.php" class="nav-item active">
            <div class="nav-item-icon">🔔</div>
            <div class="nav-item-text">消息</div>
        </a>
        <a href="profile.php" class="nav-item">
            <div class="nav-item-icon">👤</div>
            <div class="nav-item-text">我的</div>
        </a>
    </div>
</body>
</html>
