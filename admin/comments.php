<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin_login();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    
    if ($action === 'delete' && $id) {
        try {
            db()->delete('comments', 'id = ?', [$id]);
            $message = '删除成功';
        } catch (Exception $e) {
            $message = '删除失败';
        }
    } elseif ($action === 'toggle' && $id) {
        try {
            $comment = db()->fetchOne('SELECT status FROM comments WHERE id = ?', [$id]);
            $new_status = $comment['status'] == 1 ? 0 : 1;
            db()->update('comments', ['status' => $new_status], 'id = ?', [$id]);
            $message = '状态更新成功';
        } catch (Exception $e) {
            $message = '更新失败';
        }
    }
}

try {
    $comments = db()->fetchAll('SELECT c.*, p.content as post_content FROM comments c LEFT JOIN posts p ON c.post_id = p.id ORDER BY c.created_at DESC');
} catch (Exception $e) {
    $comments = [];
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>评论管理 - <?= e(SITE_NAME) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; min-height: 100vh; }
        .header { background: white; padding: 16px 20px; display: flex; align-items: center; gap: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .back-btn { font-size: 20px; text-decoration: none; color: #374151; }
        .header-title { font-size: 18px; font-weight: 600; }
        .container { padding: 20px; }
        .message { padding: 12px; border-radius: 8px; margin-bottom: 16px; background: #dcfce7; color: #166534; }
        .comment-list { display: flex; flex-direction: column; gap: 12px; }
        .comment-card { background: white; border-radius: 12px; padding: 16px; }
        .comment-content { color: #374151; line-height: 1.5; margin-bottom: 8px; font-size: 14px; }
        .comment-post { color: #6b7280; font-size: 12px; padding: 8px; background: #f9fafb; border-radius: 6px; margin-bottom: 12px; }
        .comment-meta { color: #9ca3af; font-size: 12px; margin-bottom: 12px; }
        .comment-actions { display: flex; gap: 8px; border-top: 1px solid #f3f4f6; padding-top: 12px; }
        .btn { padding: 8px 16px; border: none; border-radius: 6px; font-size: 13px; cursor: pointer; }
        .btn-secondary { background: #f3f4f6; color: #374151; }
        .btn-danger { background: #fee2e2; color: #991b1b; }
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 11px; margin-bottom: 8px; display: inline-block; background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php" class="back-btn">←</a>
        <div class="header-title">评论管理</div>
    </div>
    
    <div class="container">
        <?php if ($message): ?>
        <div class="message"><?= e($message) ?></div>
        <?php endif; ?>
        
        <?php if (empty($comments)): ?>
        <div style="text-align: center; padding: 40px; color: #9ca3af;">暂无评论</div>
        <?php else: ?>
        <div class="comment-list">
            <?php foreach ($comments as $comment): ?>
            <div class="comment-card">
                <?php if (!$comment['status']): ?>
                <span class="badge">👁️ 已隐藏</span>
                <?php endif; ?>
                <div class="comment-content"><?= e($comment['content']) ?></div>
                <div class="comment-post">原动态: <?= e(mb_substr($comment['post_content'] ?? '', 0, 50)) ?>...</div>
                <div class="comment-meta"><?= format_time($comment['created_at']) ?></div>
                <div class="comment-actions">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?= $comment['id'] ?>">
                        <input type="hidden" name="action" value="toggle">
                        <button type="submit" class="btn btn-secondary"><?= $comment['status'] ? '隐藏' : '显示' ?></button>
                    </form>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('确定要删除吗？');">
                        <input type="hidden" name="id" value="<?= $comment['id'] ?>">
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
