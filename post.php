<?php
require_once __DIR__ . '/includes/functions.php';

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content'] ?? '');
    $topic_id = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : null;
    
    if (empty($content)) {
        $message = '请输入内容';
    } else {
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_result = upload_image($_FILES['image']);
            if ($upload_result['success']) {
                $image = $upload_result['url'];
            }
        }
        
        try {
            $post_id = db()->insert('posts', [
                'topic_id' => $topic_id,
                'content' => $content,
                'image' => $image
            ]);
            $success = true;
            $message = '发布成功！';
        } catch (Exception $e) {
            $message = '发布失败，请稍后重试';
        }
    }
}

$topics = get_topics();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>发布动态 - <?= e(SITE_NAME) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; min-height: 100vh; }
        .header { background: white; padding: 16px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid #e5e7eb; }
        .back-btn { font-size: 20px; text-decoration: none; color: #374151; }
        .header-title { font-size: 18px; font-weight: 600; flex: 1; }
        .form-container { padding: 16px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; margin-bottom: 8px; color: #374151; font-weight: 500; }
        textarea { width: 100%; min-height: 150px; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 16px; resize: vertical; }
        select { width: 100%; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 16px; background: white; }
        .file-upload { border: 2px dashed #e5e7eb; padding: 24px; text-align: center; border-radius: 8px; cursor: pointer; }
        .submit-btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; }
        .message { padding: 12px; border-radius: 8px; margin-bottom: 16px; }
        .message.success { background: #dcfce7; color: #166534; }
        .message.error { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php" class="back-btn">←</a>
        <div class="header-title">发布动态</div>
    </div>
    
    <div class="form-container">
        <?php if ($message): ?>
        <div class="message <?= $success ? 'success' : 'error' ?>"><?= e($message) ?></div>
        <?php if ($success): ?>
        <a href="index.php" style="display: block; text-align: center; padding: 12px; background: #f5f5f5; border-radius: 8px; text-decoration: none; color: #374151;">返回首页</a>
        <?php endif; ?>
        <?php endif; ?>
        
        <?php if (!$success): ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>话题</label>
                <select name="topic_id">
                    <option value="">不选择话题</option>
                    <?php foreach ($topics as $topic): ?>
                    <option value="<?= $topic['id'] ?>"><?= e($topic['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>内容</label>
                <textarea name="content" placeholder="分享你的想法..." required></textarea>
            </div>
            
            <div class="form-group">
                <label>图片（可选）</label>
                <div class="file-upload">
                    <input type="file" name="image" accept="image/*" style="margin-top: 8px;">
                </div>
            </div>
            
            <button type="submit" class="submit-btn">发布</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
