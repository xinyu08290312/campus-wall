<?php
require_once __DIR__ . '/includes/functions.php';

if (is_user_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // 验证
    if (empty($username) || empty($email) || empty($password)) {
        $error = '请填写所有字段';
    } elseif (strlen($username) < 2 || strlen($username) > 20) {
        $error = '用户名长度必须在2-20个字符之间';
    } elseif (!preg_match('/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]+$/u', $username)) {
        $error = '用户名只能包含字母、数字、下划线和中文';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '请输入有效的邮箱地址';
    } elseif (strlen($password) < 6) {
        $error = '密码长度至少6位';
    } elseif ($password !== $confirm_password) {
        $error = '两次密码不一致';
    } else {
        $result = register_user($username, $email, $password);
        if ($result['success']) {
            header('Location: login.php?registered=1');
            exit;
        } else {
            $error = '注册失败，用户名或邮箱可能已存在';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注册 - <?= e(SITE_NAME) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .register-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            padding: 48px 40px;
            animation: fadeInUp 0.6s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .logo-icon {
            font-size: 56px;
            margin-bottom: 12px;
            display: inline-block;
        }
        
        .logo-title {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        .logo-subtitle {
            font-size: 14px;
            color: #6b7280;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f9fafb;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #f5576c;
            background: white;
            box-shadow: 0 0 0 4px rgba(245, 87, 108, 0.1);
        }
        
        .form-input::placeholder {
            color: #9ca3af;
        }
        
        .form-hint {
            margin-top: 6px;
            font-size: 12px;
            color: #9ca3af;
        }
        
        .btn-register {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 87, 108, 0.5);
        }
        
        .btn-register:active {
            transform: translateY(0);
        }
        
        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .agreement {
            margin: 20px 0;
            font-size: 13px;
            color: #6b7280;
            text-align: center;
            line-height: 1.6;
        }
        
        .agreement a {
            color: #f5576c;
            text-decoration: none;
            font-weight: 500;
        }
        
        .agreement a:hover {
            text-decoration: underline;
        }
        
        .login-link {
            text-align: center;
            margin-top: 24px;
            color: #6b7280;
            font-size: 14px;
        }
        
        .login-link a {
            color: #f5576c;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .login-link a:hover {
            color: #e11d48;
            text-decoration: underline;
        }
        
        .back-home {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #9ca3af;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }
        
        .back-home:hover {
            color: #f5576c;
        }
        
        .password-strength {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .strength-weak {
            width: 33%;
            background: #ef4444;
        }
        
        .strength-medium {
            width: 66%;
            background: #f59e0b;
        }
        
        .strength-strong {
            width: 100%;
            background: #10b981;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <div class="logo-icon">🎓</div>
            <div class="logo-title">加入校园墙</div>
            <div class="logo-subtitle">开启你的校园社交之旅</div>
        </div>
        
        <?php if ($error): ?>
        <div class="error-message">
            <span>⚠️</span>
            <?= e($error) ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label">用户名</label>
                <input 
                    type="text" 
                    name="username" 
                    class="form-input" 
                    placeholder="请输入用户名（2-20个字符）"
                    required
                    autofocus
                    minlength="2"
                    maxlength="20"
                    value="<?= e($_POST['username'] ?? '') ?>"
                >
                <div class="form-hint">支持中文、字母、数字和下划线</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">邮箱</label>
                <input 
                    type="email" 
                    name="email" 
                    class="form-input" 
                    placeholder="请输入邮箱地址"
                    required
                    value="<?= e($_POST['email'] ?? '') ?>"
                >
            </div>
            
            <div class="form-group">
                <label class="form-label">密码</label>
                <input 
                    type="password" 
                    name="password" 
                    id="password"
                    class="form-input" 
                    placeholder="请输入密码（至少6位）"
                    required
                    minlength="6"
                >
                <div class="password-strength">
                    <div class="password-strength-bar" id="strengthBar"></div>
                </div>
                <div class="form-hint" id="strengthText">请设置一个强密码</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">确认密码</label>
                <input 
                    type="password" 
                    name="confirm_password" 
                    class="form-input" 
                    placeholder="请再次输入密码"
                    required
                >
            </div>
            
            <div class="agreement">
                注册即表示同意
                <a href="#">《用户协议》</a>
                和
                <a href="#">《隐私政策》</a>
            </div>
            
            <button type="submit" class="btn-register">
                注 册
            </button>
        </form>
        
        <div class="login-link">
            已有账号？ <a href="login.php">立即登录</a>
        </div>
        
        <a href="index.php" class="back-home">
            ← 返回首页
        </a>
    </div>
    
    <script>
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            strengthBar.className = 'password-strength-bar';
            
            if (strength <= 2) {
                strengthBar.classList.add('strength-weak');
                strengthText.textContent = '密码强度：弱';
                strengthText.style.color = '#ef4444';
            } else if (strength <= 3) {
                strengthBar.classList.add('strength-medium');
                strengthText.textContent = '密码强度：中等';
                strengthText.style.color = '#f59e0b';
            } else {
                strengthBar.classList.add('strength-strong');
                strengthText.textContent = '密码强度：强';
                strengthText.style.color = '#10b981';
            }
        });
    </script>
</body>
</html>
