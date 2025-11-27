-- 1. 选中数据库
USE bookstore;

-- 2. 如果已存在则删除旧表 (慎用，会清空所有用户数据)
DROP TABLE IF EXISTS `users`;

-- 3. 创建 users 表
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL, -- 存储哈希加密后的密码
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`), -- 确保用户名唯一
  UNIQUE KEY `email` (`email`)       -- 确保邮箱唯一
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. (可选) 插入一个测试用户
-- 用户名: admin
-- 邮箱: admin@example.com
-- 密码: password123 (这是加密后的哈希值，直接登录可用)
INSERT INTO `users` (`username`, `email`, `password`) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');