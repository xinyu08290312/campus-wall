<?php
require_once __DIR__ . '/includes/functions.php';

if (!is_user_logged_in()) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$user = get_logged_in_user();
$topics = get_topics();
$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content'] ?? '');
    $topic_id = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : null;
    
    if (empty($content)) {
        $message = '请输入内容';
    } else {
        $images = null;
        if (isset($_FILES['images']) && $_FILES['images']['error'] === UPLOAD_ERR_OK) {
            $upload_result = upload_image($_FILES['images']);
            if ($upload_result['success']) {
                $images = $upload_result['url'];
            }
        }
        
        $result = create_post($user['id'], $content, $topic_id, $images);
        if ($result['success']) {
            $success = true;
            $message = '发布成功！';
        } else {
            $message = '发布失败，请稍后重试';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>发布动态 - <?= e(SITE_NAME) ?></title>
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
        
        .submit-btn {
            padding: 10px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .submit-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .form-container {
            padding: 20px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .user-avatar {
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
        
        .user-name {
            font-weight: 600;
            color: #1f2937;
        }
        
        .topic-select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            background: white;
            margin-bottom: 16px;
            cursor: pointer;
            transition: border-color 0.3s;
        }
        
        .topic-select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        textarea {
            width: 100%;
            min-height: 200px;
            padding: 16px;
            border: none;
            font-size: 16px;
            line-height: 1.7;
            resize: vertical;
            background: transparent;
            font-family: inherit;
        }
        
        textarea:focus {
            outline: none;
        }
        
        textarea::placeholder {
            color: #9ca3af;
        }
        
        .image-upload {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 32px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 16px;
            background: white;
        }
        
        .image-upload:hover {
            border-color: #667eea;
            background: #f9fafb;
        }
        
        .image-upload-icon {
            font-size: 40px;
            margin-bottom: 8px;
        }
        
        .image-upload-text {
            color: #6b7280;
            font-size: 14px;
        }
        
        .image-upload-hint {
            color: #9ca3af;
            font-size: 12px;
            margin-top: 4px;
        }
        
        .message {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-size: 14px;
        }
        
        .message.success {
            background: #dcfce7;
            color: #166534;
        }
        
        .message.error {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .success-screen {
            text-align: center;
            padding: 80px 20px;
        }
        
        .success-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        
        .success-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
        }
        
        .success-text {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 24px;
        }
        
        .btn-back {
            display: inline-block;
            padding: 12px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 24px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php" class="back-btn">←</a>
        <div class="header-title">发布动态</div>
        <?php if (!$success): ?>
        <button type="submit" form="postForm" class="submit-btn">发布</button>
        <?php endif; ?>
    </div>
    
    <div class="form-container">
        <?php if ($message): ?>
        <div class="message <?= $success ? 'success' : 'error' ?>"><?= e($message) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="success-screen">
            <div class="success-icon">🎉</div>
            <div class="success-title">发布成功！</div>
            <div class="success-text">你的动态已经发布成功，快去看看吧</div>
            <a href="index.php" class="btn-back">返回首页</a>
        </div>
        <?php else: ?>
        <form id="postForm" method="POST" enctype="multipart/form-data">
            <div class="user-info">
                <div class="user-avatar">
                    <?= mb_substr($user['username'], 0, 1) ?>
                </div>
                <div class="user-name"><?= e($user['username']) ?></div>
            </div>
            
            <select name="topic_id" class="topic-select">
                <option value="">选择话题（可选）</option>
                <?php foreach ($topics as $topic): ?>
                <option value="<?= $topic['id'] ?>"><?= e($topic['icon'] ?? '') ?> <?= e($topic['name']) ?></option>
                <?php endforeach; ?>
            </select>
            
            <textarea name="content" placeholder="分享你的校园生活..." required></textarea>
            
            <div class="image-upload" onclick="document.getElementById('imageInput').click()">
                <input type="file" id="imageInput" name="images" accept="image/*" style="display: none;" onchange="handleImageSelect(this)">
                <div class="image-upload-icon">📷</div>
                <div class="image-upload-text">添加图片</div>
                <div class="image-upload-hint">支持 JPG、PNG 格式，最大 5MB</div>
            </div>
        </form>
        <?php endif; ?>
    </div>
    
    <script>
        function handleImageSelect(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 5 * 1024 * 1024) {
                    alert('图片大小不能超过5MB');
                    input.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const uploadDiv = document.querySelector('.image-upload');
                    uploadDiv.innerHTML = '<img src="' + e.target.result + '" style="max-width: 100%; border-radius: 8px;">';
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
