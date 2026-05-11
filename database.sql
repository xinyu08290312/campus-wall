-- 校园墙系统数据库结构 v2.0
-- 更新时间: 2026-05-11

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 用户表
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 管理员表
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 插入默认管理员 (用户名: admin, 密码: admin123)
INSERT INTO `admin` (`username`, `password`) VALUES
('admin', '$2y$10$TQ5S3pXWqkQ9vV1zV2yWou5qC5u6q5u6q5u6q5u6q5u6q5u6q5u6');

-- 话题表
DROP TABLE IF EXISTS `topics`;
CREATE TABLE `topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `color` varchar(20) NOT NULL DEFAULT '#3B82F6',
  `icon` varchar(50) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 插入默认话题
INSERT INTO `topics` (`name`, `color`, `icon`, `sort`) VALUES
('#校园日常#', '#3B82F6', '📝', 1),
('#求助问答#', '#10B981', '❓', 2),
('#失物招领#', '#F59E0B', '🔍', 3),
('#二手交易#', '#EF4444', '💰', 4),
('#表白分享#', '#EC4899', '💕', 5),
('#校园新闻#', '#8B5CF6', '📰', 6),
('#活动预告#', '#06B6D4', '🎉', 7);

-- 动态表
DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `images` text,
  `likes` int(11) NOT NULL DEFAULT '0',
  `comments_count` int(11) NOT NULL DEFAULT '0',
  `is_top` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `topic_id` (`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 评论表
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `likes` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 点赞表
DROP TABLE IF EXISTS `likes`;
CREATE TABLE `likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_post` (`user_id`, `post_id`),
  UNIQUE KEY `user_comment` (`user_id`, `comment_id`),
  KEY `post_id` (`post_id`),
  KEY `comment_id` (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 轮播图表
DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 插入示例轮播图
INSERT INTO `banners` (`title`, `image`, `link`, `sort`, `status`) VALUES
('🎓 欢迎来到校园墙！', '/assets/images/banner1.jpg', NULL, 1, 1),
('📢 新学期开始了，快来分享你的故事！', '/assets/images/banner2.jpg', NULL, 2, 1),
('🎉 校园活动进行中...', '/assets/images/banner3.jpg', NULL, 3, 1);

-- 消息表
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` enum('like','comment','follow','system') NOT NULL,
  `content` text NOT NULL,
  `from_user_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 插入示例用户
INSERT INTO `users` (`username`, `email`, `password`, `avatar`, `bio`, `status`) VALUES
('小明', 'xiaoming@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '/assets/images/avatar1.jpg', '热爱学习的校园达人 📚', 1),
('小红', 'xiaohong@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '/assets/images/avatar2.jpg', '喜欢拍照记录生活 📸', 1),
('校园小助手', 'helper@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '/assets/images/avatar3.jpg', '校园墙官方账号 🎓', 1);

-- 插入示例动态
INSERT INTO `posts` (`user_id`, `topic_id`, `content`, `likes`, `comments_count`, `is_top`, `status`, `created_at`) VALUES
(3, 1, '🎓 欢迎各位同学来到校园墙！这里是你分享校园生活、结交朋友的好地方。快来发布你的第一条动态吧！', 128, 45, 1, 1, '2026-05-11 08:00:00'),
(1, 1, '今天的食堂饭菜真不错！推荐大家去尝尝三楼的麻辣香锅 🍲', 56, 12, 0, 1, '2026-05-11 10:30:00'),
(2, 2, '有没有人知道图书馆明天开门吗？周末想去看书 📚', 23, 8, 0, 1, '2026-05-11 09:15:00'),
(1, 3, '在操场捡到一个黑色钱包，请到失物招领处认领 🔍', 89, 15, 0, 1, '2026-05-11 07:45:00'),
(2, 5, '今天天气真好，有人一起去打球吗？🏀', 67, 20, 0, 1, '2026-05-11 08:30:00'),
(3, 6, '📢 关于举办校园歌手大赛的通知...', 234, 56, 1, 1, '2026-05-11 06:00:00');

-- 插入示例评论
INSERT INTO `comments` (`post_id`, `user_id`, `content`, `likes`, `created_at`) VALUES
(2, 2, '真的吗？哪个窗口呀', 5, '2026-05-11 10:45:00'),
(2, 3, '明天去试试！', 3, '2026-05-11 11:00:00'),
(3, 1, '图书馆周末好像不开门', 2, '2026-05-11 09:30:00'),
(4, 3, '已经交到失物招领处了 👍', 8, '2026-05-11 08:00:00'),
(5, 1, '算我一个！', 4, '2026-05-11 09:00:00');

SET FOREIGN_KEY_CHECKS = 1;
