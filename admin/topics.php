<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin_login();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $color = $_POST['color'] ?? '#3B82F6';
        if ($name) {
            try {
                db()->insert('topics', ['name' => $name, 'color' => $color]);
                $message = '添加成功';
            } catch (Exception $e) {
                $message = '添加失败';
            }
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($id) {
            try {
                db()->delete('topics', 'id = ?', [$id]);
                db()->update('posts', ['topic_id' => null], 'topic_id = ?', [$id]);
                $message = '删除成功';
            } catch (Exception $e) {
                $message = '删除失败';
            }
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
    <title>话题管理 - <?= e(SITE_NAME) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; min-height: 100vh; }
        .header { background: white; padding: 16px 20px; display: flex; align-items: center; gap: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .back-btn { font-size: 20px; text-decoration: none; color: #374151; }
        .header-title { font-size: 18px; font-weight: 600; }
        .container { padding: 20px; }
        .message { padding: 12px; border-radius: 8px; margin-bottom: 16px; background: #dcfce7; color: #166534; }
        .add-form { background: white; border-radius: 12px; padding: 16px; margin-bottom: 16px; }
        .form-row { display: flex; gap: 12px; margin-bottom: 12px; }
        .form-row > *:first-child { flex: 1; }
        input[type="text"] { width: 100%; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; }
        input[type="color"] { width: 60px; height: 44px; border: 1px solid #e5e7eb; border-radius: 8px; cursor: pointer; }
        .btn { padding: 12px 20px; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; font-weight: 500; }
        .btn-primary { background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%); color: white; }
        .btn-danger { background: #fee2e2; color: #991b1b; padding: 8px 16px; font-size: 13px; }
        .topic-list { display: flex; flex-direction: column; gap: 12px; }
        .topic-card { background: white; border-radius: 12px; padding: 16px; display: flex; align-items: center; justify-content: space-between; }
        .topic-info { display: flex; align-items: center; gap: 12px; }
        .topic-color { width: 32px; height: 32px; border-radius: 8px; }
        .topic-name { font-size: 16px; font-weight: 500; color: #374151; }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php" class="back-btn">←</a>
        <div class="header-title">话题管理</div>
    </div>
    
    <div class="container">
        <?php if ($message): ?>
        <div class="message"><?= e($message) ?></div>
        <?php endif; ?>
        
        <div class="add-form">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-row">
                    <input type="text" name="name" placeholder="话题名称（如：#校园日常#）" required>
                    <input type="color" name="color" value="#3B82F6">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">添加话题</button>
            </form>
        </div>
        
        <?php if (empty($topics)): ?>
        <div style="text-align: center; padding: 40px; color: #9ca3af;">暂无话题</div>
        <?php else: ?>
        <div class="topic-list">
            <?php foreach ($topics as $topic): ?>
            <div class="topic-card">
                <div class="topic-info">
                    <div class="topic-color" style="background: <?= e($topic['color']) ?>;"></div>
                    <div class="topic-name"><?= e($topic['name']) ?></div>
                </div>
                <form method="POST" onsubmit="return confirm('确定要删除吗？');">
                    <input type="hidden" name="id" value="<?= $topic['id'] ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="btn btn-danger">删除</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
