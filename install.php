<?php
$step = isset($_GET['step']) ? intval($_GET['step']) : 1;
$message = '';
$db_config = [];

function check_env() {
    return [
        'php_version' => version_compare(PHP_VERSION, '8.0.0', '>='),
        'pdo' => extension_loaded('pdo'),
        'pdo_mysql' => extension_loaded('pdo_mysql'),
        'config_writeable' => is_writable(__DIR__ . '/config'),
        'upload_writeable' => is_writable(__DIR__ . '/uploads'),
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['install_db'])) {
        $db_host = trim($_POST['db_host'] ?? 'localhost');
        $db_user = trim($_POST['db_user'] ?? 'root');
        $db_pass = $_POST['db_pass'] ?? '';
        $db_name = trim($_POST['db_name'] ?? 'campus_wall');
        $admin_pass = trim($_POST['admin_password'] ?? 'admin123');
        
        try {
            $pdo = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `$db_name`");
            
            $sql = file_get_contents(__DIR__ . '/database.sql');
            if ($sql) {
                $pdo->exec($sql);
            }
            
            if (!empty($admin_pass) && $admin_pass !== 'admin123') {
                $hashed_pass = password_hash($admin_pass, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE admin SET password = ? WHERE username = 'admin'")->execute([$hashed_pass]);
            }
            
            $config_content = "<?php\ndefine('DB_HOST', '$db_host');\ndefine('DB_USER', '$db_user');\ndefine('DB_PASS', '$db_pass');\ndefine('DB_NAME', '$db_name');\ndefine('DB_CHARSET', 'utf8mb4');\n\ndefine('SITE_NAME', '校园墙');\ndefine('SITE_URL', 'http://' . (\$_SERVER['HTTP_HOST'] ?? 'localhost'));\n\ndefine('UPLOAD_DIR', __DIR__ . '/../uploads/');\ndefine('UPLOAD_URL', '/uploads/');\ndefine('ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif']);\ndefine('MAX_FILE_SIZE', 5 * 1024 * 1024);\n\ndate_default_timezone_set('Asia/Shanghai');\n\nerror_reporting(E_ALL);\nini_set('display_errors', 1);\n\nsession_start();\n";
            file_put_contents(__DIR__ . '/config/config.php', $config_content);
            
            header('Location: install.php?step=3');
            exit;
        } catch (PDOException $e) {
            $message = '数据库连接失败: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>校园墙系统 - 安装向导</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 500px; margin: 0 auto; background: white; border-radius: 16px; padding: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
        h1 { color: #1f2937; margin-bottom: 8px; font-size: 24px; text-align: center; }
        .subtitle { color: #6b7280; margin-bottom: 32px; font-size: 14px; text-align: center; }
        .step-indicator { display: flex; justify-content: center; gap: 12px; margin-bottom: 32px; }
        .step-dot { width: 40px; height: 40px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-weight: 700; font-size: 14px; transition: all 0.2s; }
        .step-dot.active { background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%); color: white; transform: scale(1.1); }
        .step { display: none; }
        .step.active { display: block; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #374151; font-weight: 600; font-size: 14px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 15px; transition: border-color 0.2s; }
        input:focus { outline: none; border-color: #3B82F6; }
        .btn { background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%); color: white; border: none; padding: 14px 32px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: transform 0.2s; width: 100%; }
        .btn:hover { transform: translateY(-2px); }
        .btn-secondary { background: #f3f4f6; color: #374151; }
        .check-list { list-style: none; margin-bottom: 24px; }
        .check-list li { padding: 14px; background: #f9fafb; border-radius: 10px; margin-bottom: 10px; display: flex; align-items: center; gap: 14px; }
        .check-icon { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: bold; flex-shrink: 0; }
        .pass { background: #dcfce7; color: #166534; }
        .fail { background: #fee2e2; color: #991b1b; }
        .check-label { font-size: 14px; color: #374151; }
        .alert { padding: 14px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .success-box { text-align: center; padding: 20px; }
        .success-icon { width: 88px; height: 88px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; font-size: 44px; }
        .credentials { background: #f9fafb; padding: 24px; border-radius: 12px; margin-top: 24px; text-align: left; }
        .credentials h3 { font-size: 15px; margin-bottom: 16px; color: #1f2937; }
        .credentials p { margin: 10px 0; color: #374151; font-size: 14px; }
        .credentials strong { color: #1f2937; font-weight: 600; }
        .btn-group { display: flex; gap: 12px; margin-top: 24px; }
        .hint { color: #6b7280; font-size: 13px; margin-top: 6px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎓 校园墙系统</h1>
        <p class="subtitle">安装向导 - 快速部署您的校园墙应用</p>
        
        <div class="step-indicator">
            <div class="step-dot <?= $step >= 1 ? 'active' : '' ?>">1</div>
            <div class="step-dot <?= $step >= 2 ? 'active' : '' ?>">2</div>
            <div class="step-dot <?= $step >= 3 ? 'active' : '' ?>">3</div>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-error"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="step <?= $step == 1 ? 'active' : '' ?>">
            <h2 style="font-size: 18px; margin-bottom: 24px; text-align: center;">环境检测</h2>
            <?php $checks = check_env(); ?>
            <ul class="check-list">
                <li>
                    <span class="check-icon <?= $checks['php_version'] ? 'pass' : 'fail' ?>">
                        <?= $checks['php_version'] ? '✓' : '✗' ?>
                    </span>
                    <span class="check-label">PHP 8.0+ (当前: <?= PHP_VERSION ?>)</span>
                </li>
                <li>
                    <span class="check-icon <?= $checks['pdo'] ? 'pass' : 'fail' ?>">
                        <?= $checks['pdo'] ? '✓' : '✗' ?>
                    </span>
                    <span class="check-label">PDO 扩展</span>
                </li>
                <li>
                    <span class="check-icon <?= $checks['pdo_mysql'] ? 'pass' : 'fail' ?>">
                        <?= $checks['pdo_mysql'] ? '✓' : '✗' ?>
                    </span>
                    <span class="check-label">PDO MySQL 扩展</span>
                </li>
                <li>
                    <span class="check-icon <?= $checks['config_writeable'] ? 'pass' : 'fail' ?>">
                        <?= $checks['config_writeable'] ? '✓' : '✗' ?>
                    </span>
                    <span class="check-label">config/ 目录可写</span>
                </li>
                <li>
                    <span class="check-icon <?= $checks['upload_writeable'] ? 'pass' : 'fail' ?>">
                        <?= $checks['upload_writeable'] ? '✓' : '✗' ?>
                    </span>
                    <span class="check-label">uploads/ 目录可写</span>
                </li>
            </ul>
            <?php if (in_array(false, $checks)): ?>
            <p style="color: #ef4444; margin-bottom: 20px; font-size: 14px;">请先解决环境问题后再继续安装</p>
            <?php else: ?>
            <form method="GET">
                <input type="hidden" name="step" value="2">
                <button type="submit" class="btn">下一步：配置数据库</button>
            </form>
            <?php endif; ?>
        </div>

        <div class="step <?= $step == 2 ? 'active' : '' ?>">
            <h2 style="font-size: 18px; margin-bottom: 24px; text-align: center;">数据库配置</h2>
            <form method="POST">
                <div class="form-group">
                    <label>数据库主机</label>
                    <input type="text" name="db_host" value="localhost" placeholder="localhost">
                </div>
                <div class="form-group">
                    <label>数据库用户名</label>
                    <input type="text" name="db_user" value="root" placeholder="root">
                </div>
                <div class="form-group">
                    <label>数据库密码</label>
                    <input type="password" name="db_pass" placeholder="数据库密码">
                </div>
                <div class="form-group">
                    <label>数据库名称</label>
                    <input type="text" name="db_name" value="campus_wall" placeholder="campus_wall">
                    <p class="hint">如果不存在将自动创建</p>
                </div>
                <div class="form-group">
                    <label>管理员密码</label>
                    <input type="password" name="admin_password" placeholder="admin123（留空则使用默认）">
                    <p class="hint">默认: admin123</p>
                </div>
                <input type="hidden" name="install_db" value="1">
                <div class="btn-group">
                    <a href="install.php?step=1" class="btn btn-secondary" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">上一步</a>
                    <button type="submit" class="btn">开始安装</button>
                </div>
            </form>
        </div>

        <div class="step <?= $step == 3 ? 'active' : '' ?>">
            <div class="success-box">
                <div class="success-icon">🎉</div>
                <h2 style="font-size: 22px; margin-bottom: 12px;">安装成功！</h2>
                <p style="color: #6b7280; margin-bottom: 24px; font-size: 14px;">校园墙系统已成功部署</p>
                
                <div class="credentials">
                    <h3>后台登录信息</h3>
                    <p><strong>用户名:</strong> admin</p>
                    <p><strong>密码:</strong> 您设置的密码 (默认: admin123)</p>
                    <p style="color: #ef4444; margin-top: 16px; font-size: 13px;">⚠️ 请登录后立即修改默认密码！</p>
                </div>
                
                <div class="btn-group">
                    <a href="index.php" class="btn btn-secondary" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">访问前台</a>
                    <a href="admin/index.php" class="btn" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">访问后台</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
