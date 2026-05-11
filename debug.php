<?php
echo "<h1>PHP 测试</h1>";
echo "<p>PHP 版本: " . PHP_VERSION . "</p>";

echo "<h2>检查必需扩展</h2>";
echo "<p>pdo: " . (extension_loaded('pdo') ? '✅' : '❌') . "</p>";
echo "<p>pdo_mysql: " . (extension_loaded('pdo_mysql') ? '✅' : '❌') . "</p>";
echo "<p>mbstring: " . (extension_loaded('mbstring') ? '✅' : '❌') . "</p>";

echo "<h2>测试数据库连接</h2>";
try {
    require_once __DIR__ . '/config/database.php';
    $db = Database::getInstance();
    echo "<p style='color:green'>✅ 数据库连接成功!</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>❌ 数据库连接失败: " . $e->getMessage() . "</p>";
}
?>