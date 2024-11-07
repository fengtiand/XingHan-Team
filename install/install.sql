SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 创建管理员用户表
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建网站设置表
CREATE TABLE IF NOT EXISTS `site_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建团队成员表
CREATE TABLE IF NOT EXISTS `team_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `bio` text,
  `image_url` varchar(255) DEFAULT NULL,
  `qq` varchar(20) DEFAULT NULL,
  `wechat` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('normal','paused') DEFAULT 'normal',
  `review_status` enum('approved','pending','rejected') DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建项目展示表
CREATE TABLE IF NOT EXISTS `portfolio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `image_url` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `tag` varchar(50) DEFAULT '6年老品牌',
  `status` enum('show','hide') DEFAULT 'show',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建内容管理表
CREATE TABLE IF NOT EXISTS `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建轮播图表
CREATE TABLE IF NOT EXISTS `carousel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text,
  `image_url` varchar(255) NOT NULL,
  `order_num` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 创建验证码表
CREATE TABLE IF NOT EXISTS `verification_codes` (
  `email` varchar(100) NOT NULL,
  `code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `last_sent_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 插入默认网站设置
INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('site_title', '星涵网络工作室 - 梦之理想 与你共筑'),
('site_keywords', '星涵网络工作室,星涵工作室,星涵网络'),
('site_description', '这是一个描述。'),
('footer_text', 'Copyright © 2019 - 2024 星涵网络工作室官网 All Rights Reserved.'),
('logo_type', 'text'),
('logo_text', '星涵网络工作室'),
('logo_image', '/assets/img/logo.png'),
('captcha_enabled', '0'),
('smtp_host', 'smtp.qq.com'),
('smtp_port', '465'),
('smtp_secure', 'ssl'),
('smtp_user', ''),
('smtp_pass', ''),
('from_email', ''),
('from_name', '');

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO site_settings (setting_key, setting_value) VALUES ('site_favicon', 'favicon.ico')
ON DUPLICATE KEY UPDATE setting_value = setting_value;