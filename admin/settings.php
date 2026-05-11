<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin_login();

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($current_password) || empty($new_password)) {
        $message = '请填写完整';
    } elseif ($new_password !== $confirm_password) {
        $message = '两次密码不一致';
    } elseif (strlen($new_password) < 6) {
        $message = '新密码至少6位';
    } else {
        try {
            $admin = db()->fetchOne('SELECT * FROM admin WHERE id = ?', [$_SESSION['admin_id']]);
            if (password_verify($current_password, $admin['password'])) {
                db()->update('admin', ['password' => password_hash($new_password, PASSWORD_DEFAULT)], 'id = ?', [$_SESSION['admin_id']]);
                $success = true;
                $message = '密码修改成功';
            } else {
                $message = '当前密码错误';
            }
        } catch (Exception $e) {
            $message = '修改失败，请稍后重试';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统设置 - <?= e(SITE_NAME) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; min-height: 100vh; }
        .header { background: white; padding: 16px 20px; display: flex; align-items: center; gap: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .back-btn { font-size: 20px; text-decoration: none; color: #374151; }
        .header-title { font-size: 18px; font-weight: 600; }
        .container { padding: 20px; }
        .message { padding: 12px; border-radius: 8px; margin-bottom: 16px; }
        .message.success { background: #dcfce7; color: #166534; }
        .message.error { background: #fee2e2; color: #991b1b; }
        .card { background: white; border-radius: 12px; padding: 20px; }
        .card-title { font-size: 16px; font-weight: 600; margin-bottom: 20px; color: #1f2937; }
        .form-group { margin-bottom: 16px; }
        label { display: block; margin-bottom: 8px; color: #374151; font-weight: 500; font-size: 14px; }
        input[type="password"] { width: 100%; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; }
        .btn { padding: 12px 20px; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; font-weight: 500; width: 100%; }
        .btn-primary { background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%); color: white; }
        .info-box { background: #f9fafb; padding: 16px; border-radius: 8px; margin-top: 20px; }
        .info-box p { color: #6b7280; font-size: 13px; margin-bottom: 8px; }
        .info-box p:last-child { margin-bottom: 0; }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php" class="back-btn">←</a>
        <div class="header-title">系统设置</div>
    </div>
    
    <div class="container">
        <?php if ($message): ?>
        <div class="message <?= $success ? 'success' : 'error' ?>"><?= e($message) ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-title">修改密码</div>
            <form method="POST">
                <div class="form-group">
                    <label>当前密码</label>
                    <input type="password" name="current_password" placeholder="请输入当前密码" required>
                </div>
                <div class="form-group">
                    <label>新密码</label>
                    <input type="password" name="new_password" placeholder="请输入新密码（至少6位）" required>
                </div>
                <div class="form-group">
                    <label>确认新密码</label>
                    <input type="password" name="confirm_password" placeholder="请再次输入新密码" required>
                </div>
                <button type="submit" class="btn btn-primary">修改密码</button>
            </form>
            
            <div class="info-box">
                <p>📌 当前管理员：<?= e($_SESSION['admin_username'] ?? '') ?></p>
                <p>💡 提示：请定期修改密码以确保安全</p>
            </div>
        </div>
    </div>
</body>
</html>
