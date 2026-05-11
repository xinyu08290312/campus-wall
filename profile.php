<?php
require_once __DIR__ . '/includes/functions.php';

if (!is_user_logged_in()) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$user = get_current_user();
$stats = get_user_stats($user['id']);
$posts = get_user_posts($user['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'logout') {
        logout_user();
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的 - <?= e(SITE_NAME) ?></title>
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
        
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 32px 20px;
            position: relative;
        }
        
        .profile-back {
            position: absolute;
            top: 20px;
            left: 20px;
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
        
        .profile-back:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .profile-settings {
            position: absolute;
            top: 20px;
            right: 20px;
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
        
        .profile-settings:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .profile-info {
            text-align: center;
            margin-top: 20px;
        }
        
        .profile-avatar {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,0.3);
            margin: 0 auto 16px;
            object-fit: cover;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }
        
        .profile-name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .profile-bio {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        
        .profile-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 24px;
        }
        
        .stat-item {
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .stat-item:hover {
            transform: scale(1.1);
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .stat-label {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .nav-tabs {
            background: white;
            display: flex;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 12px;
        }
        
        .nav-tab {
            flex: 1;
            padding: 16px;
            text-align: center;
            text-decoration: none;
            color: #6b7280;
            font-weight: 600;
            font-size: 14px;
            position: relative;
            transition: all 0.3s;
        }
        
        .nav-tab.active {
            color: #667eea;
        }
        
        .nav-tab.active::after {
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
        
        .posts-section {
            padding: 0;
        }
        
        .section-title {
            padding: 16px 20px;
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            background: white;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .post-card {
            background: white;
            padding: 16px;
            margin-bottom: 8px;
            transition: all 0.3s;
        }
        
        .post-card:hover {
            background: #f9fafb;
        }
        
        .post-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        
        .post-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
        }
        
        .post-meta {
            flex: 1;
        }
        
        .post-username {
            font-weight: 600;
            color: #1f2937;
            font-size: 14px;
            margin-bottom: 2px;
        }
        
        .post-time {
            font-size: 12px;
            color: #9ca3af;
        }
        
        .post-topic {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            margin-bottom: 8px;
            color: white;
        }
        
        .post-content {
            color: #374151;
            line-height: 1.6;
            font-size: 14px;
            margin-bottom: 12px;
        }
        
        .post-actions {
            display: flex;
            gap: 24px;
            color: #6b7280;
            font-size: 13px;
        }
        
        .action-btn {
            display: flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            transition: color 0.3s;
        }
        
        .action-btn:hover {
            color: #667eea;
        }
        
        .action-btn.liked {
            color: #ef4444;
        }
        
        .action-btn.comment:hover {
            color: #10b981;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }
        
        .empty-icon {
            font-size: 64px;
            margin-bottom: 16px;
        }
        
        .empty-text {
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .btn-create {
            display: inline-block;
            padding: 12px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 24px;
            font-weight: 600;
            transition: transform 0.3s;
        }
        
        .btn-create:hover {
            transform: scale(1.05);
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
        
        .menu-section {
            background: white;
            margin-top: 12px;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            text-decoration: none;
            color: #374151;
            transition: background 0.3s;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .menu-item:hover {
            background: #f9fafb;
        }
        
        .menu-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 20px;
        }
        
        .menu-icon.blue { background: #dbeafe'; color: #2563eb; }
        .menu-icon.green { background: #dcfce7; color: #16a34a; }
        .menu-icon.red { background: #fee2e2; color: #dc2626; }
        .menu-icon.orange { background: #ffedd5; color: #ea580c; }
        
        .menu-text {
            flex: 1;
            font-weight: 500;
        }
        
        .menu-arrow {
            color: #d1d5db;
            font-size: 18px;
        }
        
        .logout-section {
            padding: 20px;
            background: white;
            margin-top: 12px;
        }
        
        .btn-logout {
            width: 100%;
            padding: 14px;
            background: #fee2e2;
            color: #dc2626;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-logout:hover {
            background: #fecaca;
        }
        
        .content-wrapper {
            padding-bottom: 80px;
        }
    </style>
</head>
<body>
    <div class="profile-header">
        <a href="index.php" class="profile-back">←</a>
        <a href="#" class="profile-settings">⚙️</a>
        
        <div class="profile-info">
            <div class="profile-avatar">
                <?= mb_substr($user['username'], 0, 1) ?>
            </div>
            <div class="profile-name"><?= e($user['username']) ?></div>
            <div class="profile-bio"><?= e($user['bio'] ?: '这个人很懒，什么都没写~') ?></div>
        </div>
        
        <div class="profile-stats">
            <div class="stat-item">
                <div class="stat-number"><?= $stats['posts'] ?></div>
                <div class="stat-label">动态</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= $stats['comments'] ?></div>
                <div class="stat-label">评论</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= $stats['likes'] ?></div>
                <div class="stat-label">获赞</div>
            </div>
        </div>
    </div>
    
    <div class="content-wrapper">
        <div class="menu-section">
            <a href="#" class="menu-item">
                <div class="menu-icon blue">📝</div>
                <div class="menu-text">我的动态</div>
                <div class="menu-arrow">›</div>
            </a>
            <a href="#" class="menu-item">
                <div class="menu-icon green">❤️</div>
                <div class="menu-text">我的点赞</div>
                <div class="menu-arrow">›</div>
            </a>
            <a href="messages.php" class="menu-item">
                <div class="menu-icon orange">🔔</div>
                <div class="menu-text">消息通知</div>
                <div class="menu-arrow">›</div>
            </a>
        </div>
        
        <div class="logout-section">
            <form method="POST">
                <input type="hidden" name="action" value="logout">
                <button type="submit" class="btn-logout">退出登录</button>
            </form>
        </div>
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
        <a href="messages.php" class="nav-item">
            <div class="nav-item-icon">🔔</div>
            <div class="nav-item-text">消息</div>
        </a>
        <a href="profile.php" class="nav-item active">
            <div class="nav-item-icon">👤</div>
            <div class="nav-item-text">我的</div>
        </a>
    </div>
</body>
</html>
