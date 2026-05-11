<?php
require_once __DIR__ . '/includes/functions.php';

$id = intval($_GET['id'] ?? 0);
$post = get_post($id);
$comments = $post ? get_comments($id) : [];
$current_user = get_current_user();
$message = '';

if (!$post) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $current_user) {
    $content = trim($_POST['content'] ?? '');
    if (!empty($content)) {
        $result = create_comment($id, $current_user['id'], $content);
        if ($result['success']) {
            header('Location: detail.php?id=' . $id);
            exit;
        } else {
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            padding-bottom: 80px;
        }
        
        .header {
            background: white;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .back-btn {
            font-size: 24px;
            color: #374151;
            text-decoration: none;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background 0.3s;
        }
        
        .back-btn:hover {
            background: #f3f4f6;
        }
        
        .header-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            flex: 1;
        }
        
        .share-btn {
            font-size: 24px;
            color: #374151;
            text-decoration: none;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background 0.3s;
        }
        
        .share-btn:hover {
            background: #f3f4f6;
        }
        
        .post-detail {
            background: white;
            padding: 20px;
            margin-bottom: 12px;
        }
        
        .post-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        
        .post-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: 700;
        }
        
        .post-author {
            flex: 1;
        }
        
        .post-username {
            font-weight: 600;
            color: #1f2937;
            font-size: 16px;
            margin-bottom: 4px;
        }
        
        .post-time {
            font-size: 13px;
            color: #9ca3af;
        }
        
        .post-topic {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 16px;
            font-size: 13px;
            color: white;
            margin-bottom: 16px;
        }
        
        .post-content {
            color: #374151;
            line-height: 1.8;
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .post-image {
            width: 100%;
            border-radius: 12px;
            margin-bottom: 16px;
        }
        
        .post-stats {
            display: flex;
            gap: 24px;
            padding-top: 16px;
            border-top: 1px solid #f3f4f6;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6b7280;
            font-size: 14px;
        }
        
        .stat-icon {
            font-size: 20px;
        }
        
        .comments-section {
            background: white;
            padding: 20px;
        }
        
        .comments-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        
        .comments-title {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
        }
        
        .comments-count {
            color: #6b7280;
            font-size: 14px;
        }
        
        .comment-item {
            padding: 16px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .comment-item:last-child {
            border-bottom: none;
        }
        
        .comment-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }
        
        .comment-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            font-weight: 600;
        }
        
        .comment-author {
            flex: 1;
        }
        
        .comment-username {
            font-weight: 600;
            color: #1f2937;
            font-size: 14px;
        }
        
        .comment-time {
            font-size: 12px;
            color: #9ca3af;
        }
        
        .comment-content {
            color: #374151;
            line-height: 1.6;
            font-size: 14px;
            margin-left: 46px;
        }
        
        .comment-form {
            background: white;
            padding: 16px 20px;
            border-top: 1px solid #e5e7eb;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }
        
        .comment-input-wrapper {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }
        
        .comment-input {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 24px;
            font-size: 15px;
            resize: none;
            font-family: inherit;
            transition: border-color 0.3s;
        }
        
        .comment-input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .comment-input::placeholder {
            color: #9ca3af;
        }
        
        .comment-submit {
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 24px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .comment-submit:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .comment-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .message {
            padding: 12px 16px;
            border-radius: 10px;
            margin: 0 20px 16px;
            font-size: 14px;
        }
        
        .message.error {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .login-prompt {
            background: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 12px;
        }
        
        .login-prompt-text {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 12px;
        }
        
        .login-btn {
            display: inline-block;
            padding: 12px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 24px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .login-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php" class="back-btn">←</a>
        <div class="header-title">动态详情</div>
        <a href="#" class="share-btn">↗️</a>
    </div>
    
    <div class="post-detail">
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
            <?= nl2br(e($post['content'])) ?>
        </div>
        
        <?php if ($post['images']): ?>
        <img src="<?= e($post['images']) ?>" class="post-image" alt="动态图片">
        <?php endif; ?>
        
        <div class="post-stats">
            <div class="stat-item">
                <span class="stat-icon">❤️</span>
                <span><?= $post['likes'] ?> 点赞</span>
            </div>
            <div class="stat-item">
                <span class="stat-icon">💬</span>
                <span><?= count($comments) ?> 评论</span>
            </div>
        </div>
    </div>
    
    <?php if ($message): ?>
    <div class="message error"><?= e($message) ?></div>
    <?php endif; ?>
    
    <div class="comments-section">
        <div class="comments-header">
            <div class="comments-title">💬 评论</div>
            <div class="comments-count"><?= count($comments) ?> 条评论</div>
        </div>
        
        <?php if (empty($comments)): ?>
        <div style="text-align: center; padding: 40px 0; color: #9ca3af;">
            <div style="font-size: 48px; margin-bottom: 12px;">💬</div>
            <div>暂无评论，快来抢沙发！</div>
        </div>
        <?php else: ?>
        <?php foreach ($comments as $comment): ?>
        <div class="comment-item">
            <div class="comment-header">
                <div class="comment-avatar">
                    <?= mb_substr($comment['username'], 0, 1) ?>
                </div>
                <div class="comment-author">
                    <div class="comment-username"><?= e($comment['username']) ?></div>
                    <div class="comment-time"><?= format_time($comment['created_at']) ?></div>
                </div>
            </div>
            <div class="comment-content">
                <?= nl2br(e($comment['content'])) ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <?php if (!$current_user): ?>
    <div class="login-prompt">
        <div class="login-prompt-text">登录后参与评论</div>
        <a href="login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="login-btn">登录</a>
    </div>
    <?php else: ?>
    <form method="POST" class="comment-form">
        <div class="comment-input-wrapper">
            <textarea name="content" class="comment-input" placeholder="写下你的评论..." rows="1" required></textarea>
            <button type="submit" class="comment-submit">发送</button>
        </div>
    </form>
    <?php endif; ?>
</body>
</html>
