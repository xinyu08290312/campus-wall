-- 校园墙系统数据库结构
-- 创建时间: 2026-05-10

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

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
  `sort` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 插入默认话题
INSERT INTO `topics` (`name`, `color`, `sort`) VALUES
('#校园日常#', '#3B82F6', 1),
('#求助问答#', '#10B981', 2),
('#失物招领#', '#F59E0B', 3),
('#二手交易#', '#EF4444', 4),
('#表白分享#', '#EC4899', 5);

-- 动态表
DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `likes` int(11) NOT NULL DEFAULT '0',
  `is_top` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `topic_id` (`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 评论表
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`)
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
('欢迎来到校园墙！', NULL, NULL, 1, 1),
('新学期开始了', NULL, NULL, 2, 1);

-- 插入示例动态
INSERT INTO `posts` (`topic_id`, `content`, `likes`, `is_top`, `status`, `created_at`) VALUES
(1, '今天的食堂饭菜真不错！推荐大家去尝尝~', 12, 1, 1, '2026-05-10 10:00:00'),
(2, '有没有人知道图书馆明天开门吗？', 5, 0, 1, '2026-05-10 09:30:00'),
(3, '在操场捡到一个黑色钱包，请到失物招领处认领', 8, 0, 1, '2026-05-10 09:00:00'),
(5, '今天天气真好，有人一起去打球吗？', 15, 0, 1, '2026-05-10 08:00:00');

-- 插入示例评论
INSERT INTO `comments` (`post_id`, `content`, `status`, `created_at`) VALUES
(1, '真的吗？哪个窗口呀', 1, '2026-05-10 10:30:00'),
(1, '明天去试试！', 1, '2026-05-10 11:00:00'),
(2, '图书馆周末好像不开门', 1, '2026-05-10 10:00:00');

SET FOREIGN_KEY_CHECKS = 1;
