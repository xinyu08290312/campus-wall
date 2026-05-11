
# 校园墙系统

一个基于 PHP + MySQL 的校园墙应用，包含前台用户界面和后台管理系统。

## 功能特性

### 前台功能
- 📢 轮播公告展示
- 🏷️ 话题分类筛选
- 📝 动态浏览与详情查看
- ✍️ 发布新动态（支持图片上传）
- ❤️ 点赞功能
- 💬 评论功能

### 后台管理
- 🔐 管理员登录
- 📊 数据统计仪表盘
- 📝 动态管理（发布、编辑、删除、置顶、隐藏）
- 💬 评论管理
- 🏷️ 话题管理
- ⚙️ 系统设置（修改密码）

## 技术栈

- 后端：PHP 8.0+
- 数据库：MySQL 5.7+
- 前端：HTML5 + CSS3 + JavaScript
- 数据库抽象：PDO

## 安装说明

### 方式一：安装向导（推荐）

1. 下载并解压项目文件到 Web 服务器目录
2. 访问 `http://your-domain/install.php`
3. 按照向导提示完成数据库配置和安装
4. 安装完成后，删除 `install.php` 文件（安全建议）

### 方式二：手动安装

#### 1. 环境要求
- PHP 8.0 或更高版本
- MySQL 5.7 或更高版本
- Apache/Nginx Web 服务器
- 开启 PDO 扩展

#### 2. 上传文件
将所有文件上传到 Web 服务器目录。

#### 3. 创建数据库
1. 创建一个 MySQL 数据库
2. 导入 `database.sql` 文件

#### 4. 修改配置
编辑 `config/config.php` 文件，修改数据库连接信息：

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'your_database');
```

#### 5. 设置目录权限
确保 `uploads` 目录可写：
```bash
chmod 755 uploads
```

#### 6. 访问应用
- 前台地址：http://your-domain/
- 后台地址：http://your-domain/admin/

## 本地预览

在本地开发时，可以使用 PHP 内置服务器快速预览项目：

### Linux/Mac:
```bash
./start_server.sh
```

### Windows 或手动启动:
```bash
php -S localhost:8000
```

然后访问：
- 前台：http://localhost:8000/
- 后台：http://localhost:8000/admin/
- 安装向导：http://localhost:8000/install.php

## 默认账号

- 用户名：admin
- 密码：admin123

**⚠️ 请登录后立即修改密码！**

## 目录结构

```
campus-wall/
├── admin/              # 后台管理目录
│   ├── index.php       # 后台首页
│   ├── login.php       # 登录页面
│   ├── posts.php       # 动态管理
│   ├── comments.php    # 评论管理
│   ├── topics.php      # 话题管理
│   ├── settings.php    # 系统设置
│   └── logout.php      # 退出登录
├── api/                # API 接口
│   └── like.php        # 点赞接口
├── config/             # 配置文件
│   ├── config.php      # 系统配置
│   └── database.php    # 数据库连接
├── includes/           # 公共文件
│   └── functions.php   # 公共函数
├── assets/             # 静态资源
│   ├── css/
│   │   ├── style.css    # 前台样式
│   │   └── admin.css    # 后台样式
│   └── js/
├── uploads/            # 上传文件目录
├── index.php           # 前台首页
├── post.php            # 发布页面
├── detail.php          # 详情页面
├── install.php         # 安装向导
├── start_server.sh     # 快速启动脚本
├── .htaccess           # Apache 配置
├── database.sql        # 数据库文件
└── README.md          # 说明文档
```

## 数据库表说明

- `admin`：管理员表
- `posts`：动态表
- `comments`：评论表
- `topics`：话题表
- `banners`：轮播图表

## 安全建议

1. 修改默认密码
2. 删除或保护 `database.sql` 文件
3. 配置适当的文件权限
4. 使用 HTTPS
5. 定期备份数据库

## 开发说明

本项目采用原生 PHP 开发，代码结构清晰，方便二次开发。

## 许可证

MIT License
