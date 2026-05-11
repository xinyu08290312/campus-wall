<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin_login();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    
    if ($action === 'delete' && $id) {
        try {
            db()->delete('posts', 'id = ?', [$id]);
            db()->delete('comments', 'post_id = ?', [$id]);
            $message = '删除成功';
        } catch (Exception $e) {
            $message = '删除失败';
        }
    } elseif ($action === 'toggle' && $id) {
        try {
            $post = db()->fetchOne('SELECT status FROM posts WHERE id = ?', [$id]);
            $new_status = $post['status'] == 1 ? 0 : 1;
            db()->update('posts', ['status' => $new_status], 'id = ?', [$id]);
            $message = '状态更新成功';
        } catch (Exception $e) {
            $message = '更新失败';
        }
    } elseif ($action === 'top' && $id) {
        try {
            $post = db()->fetchOne('SELECT is_top FROM posts WHERE id = ?', [$id]);
            $new_top = $post['is_top'] == 1 ? 0 : 1;
            db()->update('posts', ['is_top' => $new_top], 'id = ?', [$id]);
            $message = '置顶状态更新成功';
        } catch (Exception $e) {
            $message = '更新失败';
        }
    }
}

try {
    $posts = db()->fetchAll('SELECT p.*, t.name as topic_name FROM posts p LEFT JOIN topics t ON p.topic_id = t.id ORDER BY p.is_top DESC, p.created_at DESC');
} catch (Exception $e) {
    $posts = [];
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>动态管理 - <?= e(SITE_NAME) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; min-height: 100vh; }
        .header { background: white; padding: 16px 20px; display: flex; align-items: center; gap: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .back-btn { font-size: 20px; text-decoration: none; color: #374151; }
        .header-title { font-size: 18px; font-weight: 600; }
        .container { padding: 20px; }
        .message { padding: 12px; border-radius: 8px; margin-bottom: 16px; background: #dcfce7; color: #166534; }
        .post-list { display: flex; flex-direction: column; gap: 12px; }
        .post-card { background: white; border-radius: 12px; padding: 16px; }
        .post-topic { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 12px; margin-bottom: 8px; background: #3B82F6; color: white; }
        .post-content { line-height: 1.5; color: #374151; margin-bottom: 12px; font-size: 14px; }
        .post-meta { color: #9ca3af; font-size: 12px; margin-bottom: 12px; display: flex; gap: 12px; }
        .post-badges { display: flex; gap: 8px; margin-bottom: 8px; }
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 11px; }
        .badge-top { background: #fef3c7; color: #92400e; }
        .badge-hidden { background: #fee2e2; color: #991b1b; }
        .post-actions { display: flex; gap: 8px; flex-wrap: wrap; border-top: 1px solid #f3f4f6; padding-top: 12px; }
        .btn { padding: 8px 16px; border: none; border-radius: 6px; font-size: 13px; cursor: pointer; }
        .btn-secondary { background: #f3f4f6; color: #374151; }
        .btn-danger { background: #fee2e2; color: #991b1b; }
        .btn-primary { background: #dbeafe; color: #1e40af; }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php" class="back-btn">←</a>
        <div class="header-title">动态管理</div>
    </div>
    
    <div class="container">
        <?php if ($message): ?>
        <div class="message"><?= e($message) ?></div>
        <?php endif; ?>
        
        <?php if (empty($posts)): ?>
        <div style="text-align: center; padding: 40px; color: #9ca3af;">暂无动态</div>
        <?php else: ?>
        <div class="post-list">
            <?php foreach ($posts as $post): ?>
            <div class="post-card">
                <div class="post-badges">
                    <?php if ($post['is_top']): ?>
                    <span class="badge badge-top">⭐ 置顶</span>
                    <?php endif; ?>
                    <?php if (!$post['status']): ?>
                    <span class="badge badge-hidden">👁️ 已隐藏</span>
                    <?php endif; ?>
                </div>
                <?php if ($post['topic_name']): ?>
                <span class="post-topic"><?= e($post['topic_name']) ?></span>
                <?php endif; ?>
                <div class="post-content"><?= e(mb_substr($post['content'], 0, 150)) ?><?= mb_strlen($post['content']) > 150 ? '...' : '' ?></div>
                <div class="post-meta">
                    <span>❤️ <?= $post['likes'] ?></span>
                    <span>💬 <?= db()->fetchOne('SELECT COUNT(*) as count FROM comments WHERE post_id = ?', [$post['id']])['count'] ?? 0 ?></span>
                    <span><?= format_time($post['created_at']) ?></span>
                </div>
                <div class="post-actions">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                        <input type="hidden" name="action" value="toggle">
                        <button type="submit" class="btn btn-secondary"><?= $post['status'] ? '隐藏' : '显示' ?></button>
                    </form>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                        <input type="hidden" name="action" value="top">
                        <button type="submit" class="btn btn-primary"><?= $post['is_top'] ? '取消置顶' : '置顶' ?></button>
                    </form>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('确定要删除吗？');">
                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" class="btn btn-danger">删除</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
