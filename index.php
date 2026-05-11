<?php
require_once __DIR__ . '/includes/functions.php';

$current_user = get_current_user();
$topic_id = isset($_GET['topic']) ? intval($_GET['topic']) : null;
$posts = get_posts($topic_id);
$topics = get_topics();
$banners = get_banners();
$unread_count = $current_user ? get_unread_notification_count($current_user['id']) : 0;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(SITE_NAME) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            padding-bottom: 80px;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .header-title {
            font-size: 22px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .header-actions {
            display: flex;
            gap: 12px;
        }
        
        .header-btn {
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
            transition: all 0.3s;
            position: relative;
        }
        
        .header-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: scale(1.1);
        }
        
        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: 700;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .user-menu {
            position: relative;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .user-avatar:hover {
            background: rgba(255,255,255,0.4);
            transform: scale(1.1);
        }
        
        .banner-section {
            background: white;
            padding: 16px;
        }
        
        .banner-swiper {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        
        .banner-swiper::-webkit-scrollbar {
            display: none;
        }
        
        .banner-item {
            flex-shrink: 0;
            width: 100%;
            height: 160px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 20px;
            scroll-snap-align: start;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            min-width: 100%;
        }
        
        .banner-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .banner-desc {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .topics-section {
            padding: 16px;
            background: white;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .topics-title {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 12px;
        }
        
        .topics-grid {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 8px;
            scrollbar-width: none;
        }
        
        .topics-grid::-webkit-scrollbar {
            display: none;
        }
        
        .topic-tag {
            padding: 8px 16px;
            border-radius: 20px;
            background: white;
            border: 2px solid #e5e7eb;
            font-size: 14px;
            font-weight: 500;
            color: #6b7280;
            white-space: nowrap;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .topic-tag:hover {
            border-color: #667eea;
            color: #667eea;
        }
        
        .topic-tag.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .posts-section {
            padding: 16px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }
        
        .post-card {
            background: white;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .post-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .post-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        
        .post-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            font-weight: 700;
            flex-shrink: 0;
        }
        
        .post-author {
            flex: 1;
        }
        
        .post-username {
            font-weight: 600;
            color: #1f2937;
            font-size: 15px;
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
            color: white;
            margin-bottom: 8px;
        }
        
        .post-content {
            color: #374151;
            line-height: 1.7;
            font-size: 15px;
            margin-bottom: 12px;
        }
        
        .post-actions {
            display: flex;
            justify-content: space-around;
            padding-top: 12px;
            border-top: 1px solid #f3f4f6;
        }
        
        .action-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #6b7280;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            padding: 8px 16px;
            border-radius: 8px;
        }
        
        .action-btn:hover {
            background: #f3f4f6;
        }
        
        .action-btn.like:hover,
        .action-btn.liked {
            color: #ef4444;
            background: #fee2e2;
        }
        
        .action-btn-icon {
            font-size: 20px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
        }
        
        .empty-icon {
            font-size: 64px;
            margin-bottom: 16px;
        }
        
        .empty-title {
            font-size: 18px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 8px;
        }
        
        .empty-text {
            font-size: 14px;
            color: #9ca3af;
            margin-bottom: 20px;
        }
        
        .fab {
            position: fixed;
            bottom: 88px;
            right: 20px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
            z-index: 999;
        }
        
        .fab:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.5);
        }
        
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .nav-item {
            text-align: center;
            padding: 6px 16px;
            color: #9ca3af;
            text-decoration: none;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }
        
        .nav-item.active {
            color: #667eea;
        }
        
        .nav-item-icon {
            font-size: 24px;
        }
        
        .nav-item-text {
            font-size: 11px;
            font-weight: 500;
        }
        
        .nav-item.active .nav-item-icon {
            transform: scale(1.1);
        }
        
        .auth-buttons {
            display: flex;
            gap: 10px;
        }
        
        .auth-btn {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .auth-btn-login {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        
        .auth-btn-register {
            background: white;
            color: #667eea;
        }
        
        .auth-btn:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-title">
            🎓 校园墙
        </div>
        <div class="header-actions">
            <?php if ($current_user): ?>
            <a href="messages.php" class="header-btn">
                🔔
                <?php if ($unread_count > 0): ?>
                <span class="notification-badge"><?= $unread_count > 9 ? '9+' : $unread_count ?></span>
                <?php endif; ?>
            </a>
            <div class="user-menu">
                <a href="profile.php" class="user-avatar">
                    <?= mb_substr($current_user['username'], 0, 1) ?>
                </a>
            </div>
            <?php else: ?>
            <div class="auth-buttons">
                <a href="login.php" class="auth-btn auth-btn-login">登录</a>
                <a href="register.php" class="auth-btn auth-btn-register">注册</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($banners)): ?>
    <div class="banner-section">
        <div class="banner-swiper">
            <?php foreach ($banners as $banner): ?>
            <div class="banner-item" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="banner-title"><?= e($banner['title']) ?></div>
                <div class="banner-desc">发现校园精彩，分享生活点滴</div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="topics-section">
        <div class="topics-title">🏷️ 热门话题</div>
        <div class="topics-grid">
            <a href="index.php" class="topic-tag <?= $topic_id === null ? 'active' : '' ?>">全部</a>
            <?php foreach ($topics as $topic): ?>
            <a href="index.php?topic=<?= $topic['id'] ?>" 
               class="topic-tag <?= $topic_id === $topic['id'] ? 'active' : '' ?>"
               style="<?= $topic_id === $topic['id'] ? 'background: ' . $topic['color'] . '; border-color: ' . $topic['color'] . ';' : '' ?>">
                <?= e($topic['icon'] ?? '') ?> <?= e($topic['name']) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="posts-section">
        <div class="section-header">
            <div class="section-title">📝 最新动态</div>
        </div>
        
        <?php if (empty($posts)): ?>
        <div class="empty-state">
            <div class="empty-icon">📭</div>
            <div class="empty-title">暂无动态</div>
            <div class="empty-text">还没有人发布动态，成为第一个吧！</div>
            <?php if ($current_user): ?>
            <a href="post.php" class="fab" style="position: static; transform: none;">+</a>
            <?php else: ?>
            <a href="login.php" class="fab" style="position: static; transform: none;">+</a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <?php foreach ($posts as $post): ?>
        <div class="post-card" onclick="location.href='detail.php?id=<?= $post['id'] ?>'">
            <div class="post-header">
                <div class="post-avatar">
                    <?= mb_substr($post['username'], 0, 1) ?>
                </div>
                <div class="post-author">
                    <div class="post-username"><?= e($post['username']) ?></div>
                    <div class="post-time"><?= format_time($post['created_at']) ?></div>
                </div>
            </div>
            
            <?php if ($post['topic_name']): ?>
            <span class="post-topic" style="background: <?= e($post['topic_color']) ?>;">
                <?= e($post['topic_name']) ?>
            </span>
            <?php endif; ?>
            
            <div class="post-content">
                <?= e($post['content']) ?>
            </div>
            
            <div class="post-actions">
                <div class="action-btn like" onclick="event.stopPropagation();">
                    <span class="action-btn-icon">❤️</span>
                    <span><?= $post['likes'] ?></span>
                </div>
                <div class="action-btn">
                    <span class="action-btn-icon">💬</span>
                    <span><?= $post['comments_count'] ?></span>
                </div>
                <div class="action-btn">
                    <span class="action-btn-icon">↗️</span>
                    <span>分享</span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <?php if ($current_user): ?>
    <a href="post.php" class="fab">+</a>
    <?php else: ?>
    <a href="login.php" class="fab">+</a>
    <?php endif; ?>
    
    <div class="bottom-nav">
        <a href="index.php" class="nav-item active">
            <div class="nav-item-icon">🏠</div>
            <div class="nav-item-text">首页</div>
        </a>
        <?php if ($current_user): ?>
        <a href="post.php" class="nav-item">
            <div class="nav-item-icon">✏️</div>
            <div class="nav-item-text">发布</div>
        </a>
        <a href="messages.php" class="nav-item">
            <div class="nav-item-icon">🔔</div>
            <div class="nav-item-text">消息</div>
        </a>
        <a href="profile.php" class="nav-item">
            <div class="nav-item-icon">👤</div>
            <div class="nav-item-text">我的</div>
        </a>
        <?php else: ?>
        <a href="login.php" class="nav-item">
            <div class="nav-item-icon">✏️</div>
            <div class="nav-item-text">发布</div>
        </a>
        <a href="login.php" class="nav-item">
            <div class="nav-item-icon">🔔</div>
            <div class="nav-item-text">消息</div>
        </a>
        <a href="login.php" class="nav-item">
            <div class="nav-item-icon">👤</div>
            <div class="nav-item-text">我的</div>
        </a>
        <?php endif; ?>
    </div>
</body>
</html>
