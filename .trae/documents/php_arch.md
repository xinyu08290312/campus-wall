
# 校园墙系统 - 技术架构文档 (PHP版)

## 1. 架构设计
采用经典的 MVC 分层架构，前后台分离，后台使用 PHP 模板引擎，数据库使用 MySQL 5.7。

## 2. 技术栈
- **后端**：PHP 8.0+
- **数据库**：MySQL 5.7
- **前端**：HTML5 + CSS3 + JavaScript
- **样式框架**：Tailwind CSS
- **图标**：Font Awesome 或简单图标
- **服务器**：Apache/Nginx

## 3. 数据库设计

### 3.1 数据表结构

#### 管理员表 (admin)
```sql
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### 话题表 (topics)
```sql
CREATE TABLE `topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `color` varchar(20) DEFAULT '#3B82F6',
  `sort` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### 动态表 (posts)
```sql
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL,
  `user_avatar` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `images` text,
  `topic_id` int(11) DEFAULT NULL,
  `likes` int(11) DEFAULT 0,
  `comments` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `is_top` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `topic_id` (`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### 评论表 (comments)
```sql
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `user_avatar` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### 轮播图表 (banners)
```sql
CREATE TABLE `banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## 4. 项目目录结构

```
/workspace/
├── config/
│   ├── config.php          # 配置文件
│   └── database.php        # 数据库连接
├── includes/
│   ├── functions.php       # 公共函数
│   └── header.php        # 公共头部
├── admin/
│   ├── index.php         # 后台入口
│   ├── login.php         # 登录页
│   ├── dashboard.php     # 仪表盘
│   ├── posts.php         # 动态管理
│   ├── comments.php      # 评论管理
│   ├── topics.php       # 话题管理
│   ├── banners.php      # 轮播图管理
│   └── settings.php     # 设置页
│   └── logout.php        # 退出登录
├── uploads/                # 上传文件目录
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── index.php              # 首页
├── post.php             # 发布页
├── detail.php           # 详情页
└── api/
    ├── like.php          # 点赞接口
    ├── comment.php      # 评论接口
    └── upload.php       # 上传接口
```

## 5. 核心功能实现
- **会话管理**：使用 PHP Session
- **文件上传**：图片上传处理
- **数据验证**：输入过滤和验证
- **分页查询**：列表分页显示
- **安全防护**：SQL注入防护、XSS防护
