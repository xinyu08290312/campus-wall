<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>服务器测试</title>
    <style>
        body { font-family: Arial; padding: 40px; background: #f0f0f0; }
        .container { background: white; padding: 30px; border-radius: 10px; max-width: 600px; margin: 0 auto; }
        h1 { color: #333; }
        .test { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 校园墙系统 - 服务器测试</h1>
        
        <div class="test info">
            <strong>PHP 版本:</strong> <?php echo PHP_VERSION; ?>
        </div>
        
        <div class="test <?php echo extension_loaded('pdo') ? 'success' : 'error'; ?>">
            <strong>PDO 扩展:</strong> <?php echo extension_loaded('pdo') ? '✅ 已启用' : '❌ 未启用'; ?>
        </div>
        
        <div class="test <?php echo extension_loaded('pdo_mysql') ? 'success' : 'error'; ?>">
            <strong>PDO MySQL 扩展:</strong> <?php echo extension_loaded('pdo_mysql') ? '✅ 已启用' : '❌ 未启用'; ?>
        </div>
        
        <div class="test <?php echo extension_loaded('mbstring') ? 'success' : 'error'; ?>">
            <strong>MBString 扩展:</strong> <?php echo extension_loaded('mbstring') ? '✅ 已启用' : '❌ 未启用'; ?>
        </div>
        
        <h2 style="margin-top: 30px;">数据库连接测试</h2>
        <?php
        try {
            require_once __DIR__ . '/config/database.php';
            $db = Database::getInstance();
            echo '<div class="test success">✅ 数据库连接成功！</div>';
        } catch (Exception $e) {
            echo '<div class="test error">❌ 数据库连接失败:<br>' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
        
        <div class="test info" style="margin-top: 30px;">
            <strong>📝 说明:</strong>
            <p>页面全白可能是因为：</p>
            <ol style="text-align: left;">
                <li>数据库未创建或配置错误</li>
                <li>database.sql 文件未导入</li>
                <li>config/config.php 中的数据库信息不正确</li>
            </ol>
            <p><strong>解决方案：</strong></p>
            <p>请访问 <a href="install.php">install.php</a> 运行安装向导</p>
        </div>
    </div>
</body>
</html>