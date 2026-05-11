<?php
require_once __DIR__ . '/includes/functions.php';

$topic_id = isset($_GET['topic']) ? intval($_GET['topic']) : null;
$posts = get_posts($topic_id);
$topics = get_topics();
$banners = get_banners();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(SITE_NAME) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; min-height: 100vh; }
        .header { background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%); color: white; padding: 16px; text-align: center; font-size: 20px; font-weight: 600; }
        .banner { background: white; padding: 12px; margin: 12px; border-radius: 12px; }
        .topic-list { display: flex; gap: 10px; padding: 12px; overflow-x: auto; }
        .topic-tag { padding: 8px 16px; border-radius: 20px; background: white; border: 1px solid #e5e7eb; cursor: pointer; white-space: nowrap; }
        .topic-tag.active { background: #3B82F6; color: white; border-color: #3B82F6; }
        .post-list { padding: 12px; }
        .post-card { background: white; border-radius: 12px; padding: 16px; margin-bottom: 12px; }
        .post-topic { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 12px; margin-bottom: 8px; color: white; }
        .post-content { line-height: 1.6; color: #374151; margin-bottom: 12px; }
        .post-time { color: #9ca3af; font-size: 12px; margin-bottom: 12px; }
        .post-actions { display: flex; gap: 16px; color: #6b7280; font-size: 14px; }
        .action-btn { cursor: pointer; display: flex; align-items: center; gap: 4px; }
        .fab { position: fixed; bottom: 80px; right: 20px; width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 28px; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4); cursor: pointer; text-decoration: none; }
        .bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: white; display: flex; justify-content: space-around; padding: 8px 0; box-shadow: 0 -2px 10px rgba(0,0,0,0.1); }
        .nav-item { text-align: center; padding: 8px; color: #6b7280; text-decoration: none; }
        .nav-item.active { color: #3B82F6; }
    </style>
</head>
<body>
    <div class="header">🎓 校园墙</div>
    
    <?php if (!empty($banners)): ?>
    <div class="banner">
        <?php foreach ($banners as $banner): ?>
        <div><?= e($banner['title']) ?></div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <div class="topic-list">
        <a href="index.php" class="topic-tag <?= $topic_id === null ? 'active' : '' ?>">全部</a>
        <?php foreach ($topics as $topic): ?>
        <a href="index.php?topic=<?= $topic['id'] ?>" class="topic-tag <?= $topic_id === $topic['id'] ? 'active' : '' ?>" style="background: <?= $topic_id === $topic['id'] ? $topic['color'] : '' ?>;">
            <?= e($topic['name']) ?>
        </a>
        <?php endforeach; ?>
    </div>
    
    <div class="post-list">
        <?php if (empty($posts)): ?>
        <div style="text-align: center; padding: 40px; color: #9ca3af;">暂无动态</div>
        <?php else: ?>
        <?php foreach ($posts as $post): ?>
        <div class="post-card" onclick="location.href='detail.php?id=<?= $post['id'] ?>'">
            <?php if ($post['topic_name']): ?>
            <span class="post-topic" style="background: <?= e($post['topic_color']) ?>;"><?= e($post['topic_name']) ?></span>
            <?php endif; ?>
            <div class="post-content"><?= e($post['content']) ?></div>
            <div class="post-time"><?= format_time($post['created_at']) ?></div>
            <div class="post-actions">
                <span class="action-btn">❤️ <?= $post['likes'] ?></span>
                <span class="action-btn">💬 <?= count(get_comments($post['id'])) ?></span>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <a href="post.php" class="fab">+</a>
    
    <div class="bottom-nav">
        <a href="index.php" class="nav-item active">
            <div>🏠</div>
            <div style="font-size: 12px;">首页</div>
        </a>
        <a href="post.php" class="nav-item">
            <div>✏️</div>
            <div style="font-size: 12px;">发布</div>
        </a>
        <a href="admin/" class="nav-item">
            <div>⚙️</div>
            <div style="font-size: 12px;">管理</div>
        </a>
    </div>
</body>
</html>
