<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin_login();

try {
    $posts_count = db()->fetchOne('SELECT COUNT(*) as count FROM posts')['count'] ?? 0;
    $comments_count = db()->fetchOne('SELECT COUNT(*) as count FROM comments')['count'] ?? 0;
    $topics_count = db()->fetchOne('SELECT COUNT(*) as count FROM topics')['count'] ?? 0;
    $recent_posts = db()->fetchAll('SELECT * FROM posts ORDER BY created_at DESC LIMIT 5');
} catch (Exception $e) {
    $posts_count = 0;
    $comments_count = 0;
    $topics_count = 0;
    $recent_posts = [];
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台首页 - <?= e(SITE_NAME) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; min-height: 100vh; }
        .header { background: white; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header-title { font-size: 18px; font-weight: 600; color: #1f2937; }
        .header-actions { display: flex; gap: 12px; align-items: center; }
        .logout-btn { padding: 8px 16px; background: #fee2e2; color: #991b1b; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 14px; }
        .container { padding: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: white; border-radius: 12px; padding: 20px; text-align: center; }
        .stat-icon { font-size: 32px; margin-bottom: 8px; }
        .stat-number { font-size: 28px; font-weight: 700; color: #1f2937; margin-bottom: 4px; }
        .stat-label { font-size: 14px; color: #6b7280; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 24px; }
        .menu-item { background: white; border-radius: 12px; padding: 20px; text-align: center; text-decoration: none; color: #374151; display: block; transition: transform 0.2s; }
        .menu-item:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .menu-icon { font-size: 28px; margin-bottom: 8px; }
        .menu-title { font-size: 14px; font-weight: 500; }
        .section { background: white; border-radius: 12px; padding: 20px; margin-bottom: 16px; }
        .section-title { font-size: 16px; font-weight: 600; margin-bottom: 16px; color: #1f2937; }
        .post-item { padding: 12px 0; border-bottom: 1px solid #f3f4f6; }
        .post-item:last-child { border-bottom: none; }
        .post-item-title { color: #374151; margin-bottom: 4px; font-size: 14px; }
        .post-item-meta { color: #9ca3af; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-title">🎓 校园墙管理后台</div>
        <div class="header-actions">
            <a href="../index.php" style="color: #3B82F6; text-decoration: none; font-size: 14px;">前台</a>
            <a href="logout.php" class="logout-btn">退出</a>
        </div>
    </div>
    
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📝</div>
                <div class="stat-number"><?= $posts_count ?></div>
                <div class="stat-label">动态数</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💬</div>
                <div class="stat-number"><?= $comments_count ?></div>
                <div class="stat-label">评论数</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🏷️</div>
                <div class="stat-number"><?= $topics_count ?></div>
                <div class="stat-label">话题数</div>
            </div>
        </div>
        
        <div class="menu-grid">
            <a href="posts.php" class="menu-item">
                <div class="menu-icon">📝</div>
                <div class="menu-title">动态管理</div>
            </a>
            <a href="comments.php" class="menu-item">
                <div class="menu-icon">💬</div>
                <div class="menu-title">评论管理</div>
            </a>
            <a href="topics.php" class="menu-item">
                <div class="menu-icon">🏷️</div>
                <div class="menu-title">话题管理</div>
            </a>
            <a href="settings.php" class="menu-item">
                <div class="menu-icon">⚙️</div>
                <div class="menu-title">系统设置</div>
            </a>
        </div>
        
        <?php if (!empty($recent_posts)): ?>
        <div class="section">
            <div class="section-title">最新动态</div>
            <?php foreach ($recent_posts as $post): ?>
            <div class="post-item">
                <div class="post-item-title"><?= e(mb_substr($post['content'], 0, 50)) ?><?= mb_strlen($post['content']) > 50 ? '...' : '' ?></div>
                <div class="post-item-meta"><?= format_time($post['created_at']) ?> · <?= $post['likes'] ?> 点赞</div>
            </div>
            <?php endforeach; ?>
            <a href="posts.php" style="display: block; text-align: center; padding-top: 12px; color: #3B82F6; text-decoration: none; font-size: 14px;">查看全部 →</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
