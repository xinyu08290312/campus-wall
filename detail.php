<?php
require_once __DIR__ . '/includes/functions.php';

$id = intval($_GET['id'] ?? 0);
$post = get_post($id);
$comments = $post ? get_comments($id) : [];

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $post) {
    $content = trim($_POST['content'] ?? '');
    if (!empty($content)) {
        try {
            db()->insert('comments', [
                'post_id' => $id,
                'content' => $content
            ]);
            $success = true;
            $message = '评论成功！';
            $comments = get_comments($id);
        } catch (Exception $e) {
            $message = '评论失败';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>动态详情 - <?= e(SITE_NAME) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; min-height: 100vh; }
        .header { background: white; padding: 16px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid #e5e7eb; }
        .back-btn { font-size: 20px; text-decoration: none; color: #374151; }
        .header-title { font-size: 18px; font-weight: 600; }
        .container { padding: 16px; }
        .post-detail { background: white; border-radius: 12px; padding: 16px; margin-bottom: 12px; }
        .post-topic { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 12px; margin-bottom: 8px; color: white; }
        .post-content { line-height: 1.6; color: #374151; margin-bottom: 12px; font-size: 16px; }
        .post-time { color: #9ca3af; font-size: 12px; margin-bottom: 12px; }
        .post-stats { display: flex; gap: 16px; color: #6b7280; font-size: 14px; padding-top: 12px; border-top: 1px solid #e5e7eb; }
        .comments-section { background: white; border-radius: 12px; padding: 16px; }
        .comment-title { font-size: 16px; font-weight: 600; margin-bottom: 16px; }
        .comment-item { padding: 12px 0; border-bottom: 1px solid #f3f4f6; }
        .comment-item:last-child { border-bottom: none; }
        .comment-content { color: #374151; line-height: 1.5; margin-bottom: 8px; }
        .comment-time { color: #9ca3af; font-size: 12px; }
        .comment-form { margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb; }
        textarea { width: 100%; min-height: 80px; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; margin-bottom: 12px; resize: vertical; }
        .submit-btn { width: 100%; padding: 12px; background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .message { padding: 12px; border-radius: 8px; margin-bottom: 12px; }
        .message.success { background: #dcfce7; color: #166534; }
        .message.error { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php" class="back-btn">←</a>
        <div class="header-title">动态详情</div>
    </div>
    
    <div class="container">
        <?php if (!$post): ?>
        <div style="text-align: center; padding: 40px; color: #9ca3af;">动态不存在</div>
        <a href="index.php" style="display: block; text-align: center; padding: 12px; background: white; border-radius: 8px; text-decoration: none; color: #3B82F6;">返回首页</a>
        <?php else: ?>
        
        <?php if ($message): ?>
        <div class="message <?= $success ? 'success' : 'error' ?>"><?= e($message) ?></div>
        <?php endif; ?>
        
        <div class="post-detail">
            <?php if ($post['topic_name']): ?>
            <span class="post-topic" style="background: <?= e($post['topic_color']) ?>;"><?= e($post['topic_name']) ?></span>
            <?php endif; ?>
            <div class="post-content"><?= e($post['content']) ?></div>
            <div class="post-time"><?= format_time($post['created_at']) ?></div>
            <div class="post-stats">
                <span>❤️ <?= $post['likes'] ?> 点赞</span>
                <span>💬 <?= count($comments) ?> 评论</span>
            </div>
        </div>
        
        <div class="comments-section">
            <div class="comment-title">💬 评论 (<?= count($comments) ?>)</div>
            
            <?php if (empty($comments)): ?>
            <div style="color: #9ca3af; text-align: center; padding: 20px;">暂无评论</div>
            <?php else: ?>
            <?php foreach ($comments as $comment): ?>
            <div class="comment-item">
                <div class="comment-content"><?= e($comment['content']) ?></div>
                <div class="comment-time"><?= format_time($comment['created_at']) ?></div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
            
            <form method="POST" class="comment-form">
                <textarea name="content" placeholder="写下你的评论..." required></textarea>
                <button type="submit" class="submit-btn">发表评论</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
