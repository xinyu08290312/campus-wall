<?php
require_once __DIR__ . '/../includes/functions.php';

$message = '';
$success = false;

if (is_admin_logged_in()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $message = '请输入用户名和密码';
    } else {
        try {
            $admin = db()->fetchOne('SELECT * FROM admin WHERE username = ?', [$username]);
            if ($admin) {
                if (password_verify($password, $admin['password'])) {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    header('Location: index.php');
                    exit;
                } else {
                    $message = '密码错误';
                }
            } else {
                $message = '用户不存在';
            }
        } catch (Exception $e) {
            $message = '登录失败，请稍后重试';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台登录 - <?= e(SITE_NAME) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .login-box { background: white; border-radius: 16px; padding: 40px; width: 100%; max-width: 400px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
        .logo { text-align: center; margin-bottom: 32px; }
        .logo-icon { font-size: 48px; margin-bottom: 8px; }
        .logo-title { font-size: 24px; font-weight: 700; color: #1f2937; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #374151; font-weight: 500; font-size: 14px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 16px; transition: border-color 0.2s; }
        input:focus { outline: none; border-color: #3B82F6; }
        .submit-btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; }
        .message { padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .message.error { background: #fee2e2; color: #991b1b; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #6b7280; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">
            <div class="logo-icon">🎓</div>
            <div class="logo-title">校园墙管理后台</div>
        </div>
        
        <?php if ($message): ?>
        <div class="message error"><?= e($message) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>用户名</label>
                <input type="text" name="username" placeholder="请输入用户名" required autofocus>
            </div>
            
            <div class="form-group">
                <label>密码</label>
                <input type="password" name="password" placeholder="请输入密码" required>
            </div>
            
            <button type="submit" class="submit-btn">登录</button>
        </form>
        
        <a href="../index.php" class="back-link">← 返回前台</a>
    </div>
</body>
</html>
