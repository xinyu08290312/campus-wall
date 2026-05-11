
#!/bin/bash
# 校园墙系统 - 快速启动脚本
# 使用 PHP 内置服务器预览项目

echo "🎓 校园墙系统启动脚本"
echo "======================"

# 检查 PHP 是否安装
if ! command -v php &amp;&gt; /dev/null; then
    echo "❌ 错误: 未找到 PHP，请先安装 PHP 8.0 或更高版本"
    exit 1
fi

# 显示 PHP 版本
PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2)
echo "✓ PHP 版本: $PHP_VERSION"

# 检查 PHP 版本
if php -r "exit(version_compare(PHP_VERSION, '8.0.0', '&lt;') ? 1 : 0);"; then
    echo "✓ PHP 版本符合要求 (&gt;= 8.0)"
else
    echo "⚠️ 警告: PHP 版本低于 8.0，可能存在兼容性问题"
fi

# 创建 uploads 目录（如果不存在）
mkdir -p uploads
chmod 755 uploads
echo "✓ uploads 目录已准备好"

# 启动服务器
echo ""
echo "🚀 启动 PHP 内置服务器..."
echo "📱 前台地址: http://localhost:8000/"
echo "🔧 后台地址: http://localhost:8000/admin/"
echo "📦 安装向导: http://localhost:8000/install.php"
echo ""
echo "按 Ctrl+C 停止服务器"
echo "======================"
echo ""

php -S localhost:8000
