<!DOCTYPE html>
<html>
<head>
    <title>测试页面</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .test { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>🧪 校园墙系统测试</h1>
    
    <div class="test">
        <h2>PHP 版本</h2>
        <p class="<?php echo version_compare(PHP_VERSION, '8.0.0', '>=') ? 'success' : 'error'; ?>">
            <?php echo PHP_VERSION; ?>
            <?php echo version_compare(PHP_VERSION, '8.0.0', '>=') ? '✅ 支持' : '❌ 不支持'; ?>
        </p>
    </div>
    
    <div class="test">
        <h2>必要的 PHP 扩展</h2>
        <p class="<?php echo extension_loaded('pdo') ? 'success' : 'error'; ?>">
            PDO: <?php echo extension_loaded('pdo') ? '✅ 已启用' : '❌ 未启用'; ?>
        </p>
        <p class="<?php echo extension_loaded('pdo_mysql') ? 'success' : 'error'; ?>">
            PDO MySQL: <?php echo extension_loaded('pdo_mysql') ? '✅ 已启用' : '❌ 未启用'; ?>
        </p>
        <p class="<?php echo extension_loaded('mbstring') ? 'success' : 'error'; ?>">
            MBString: <?php echo extension_loaded('mbstring') ? '✅ 已启用' : '❌ 未启用'; ?>
        </p>
    </div>
    
    <div class="test">
        <h2>数据库连接测试</h2>
        <?php
        try {
            require_once __DIR__ . '/config/database.php';
            $db = Database::getInstance();
            echo '<p class="success">✅ 数据库连接成功！</p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ 数据库连接失败：' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>
    
    <div class="test">
        <h2>下一步</h2>
        <p>⚠️ 请确保：</p>
        <ul>
            <li>数据库已创建</li>
            <li>数据库名称为：campus_wall</li>
            <li>已导入 database.sql 文件</li>
        </ul>
        <p><a href="install.php">点击这里运行安装向导</a></p>
    </div>
</body>
</html>
