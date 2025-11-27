-- 1. 如果表已存在，先删除（慎用！会清空现有数据）
DROP TABLE IF EXISTS books;

-- 2. 创建 books 表
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    parent_category VARCHAR(50) DEFAULT 'Other',
    category VARCHAR(50) DEFAULT 'General',
    price DECIMAL(10, 2) NOT NULL,
    cover_image VARCHAR(255) DEFAULT 'allknow.png',
    description TEXT,
    publish_date DATE,
    sales_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    publisher VARCHAR(100),
    isbn VARCHAR(20),
    manual_tag VARCHAR(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. 插入所有书籍数据
INSERT INTO books (id, title, author, parent_category, category, price, cover_image, description, publish_date, sales_count, publisher, isbn, manual_tag) VALUES
-- 原有数据 (ID 1-10, 参考截图)
(1, 'Legend of the Condor Heroes', 'Jin Yong', 'Fiction', 'Wuxia', 28.00, 'sdyxz.jpg', 'The most famous martial arts novel in Chinese history.', '2018-02-28', 15000, 'Ming Ho House', '978-0-857-05300-8', NULL),
(2, 'Learn Spanish Fast for Adult Beginners', 'Speak Abroad Academy', 'Languages', 'Spanish', 161.00, 'spanishl.jpg', 'NEW EDITION 2025 - 3-in-1 Workbook.', '2024-04-12', 2500, 'Madrid Press', '978-84-376-5678-9', NULL),
(3, 'Let\'s Study Korean', 'Bridge Education', 'Languages', 'Korean', 22.00, 'koreanl.jpg', 'Complete Work Book for Grammar, Spelling, Vocabulary.', '2021-03-10', 800, NULL, '978-8-9730-0876-1', NULL),
(4, 'Italian Grammar Made Easy', 'Lingo Mastery', 'Languages', 'Italian', 90.00, 'italianl.jpg', 'Ready to unlock the secrets of Italian grammar?', '2024-08-12', 121, 'Rome Learning', '978-88-544-1234-1', NULL),
(5, 'Learn Thai in 100 Days', 'Natura Lingua', 'Languages', 'Thai', 72.00, 'thail.jpg', 'Ever dreamt of speaking Thai like a native?', '2024-05-01', 88, 'Bangkok Books', '978-616-18-9012-3', NULL),
(6, 'The Three-Body Problem', 'Liu Cixin', 'Fiction', 'Fantasy', 26.00, 'allknow.png', 'A science fiction masterpiece about first contact with aliens.', '2025-11-24', 50, 'Chongqing Press', '978-0-7653-7706-7', NULL),
(7, 'Learn French Fast for Adult Beginners', 'Speak Abroad Academy', 'Languages', 'French', 40.00, 'frenchl.jpg', 'With over 100,000 copies sold and over 1,000 positive reviews.', '2025-04-26', 46, 'Paris Culinary', '978-2-08-020345-6', NULL),
(8, 'MARUGOTO Japanese Language and Culture', 'Japan Foundation', 'Languages', 'Japanese', 65.00, 'japanl.jpeg', 'This book is one of the MARUGOTO Japanese Language series.', '2018-11-06', 2, 'Japan Foundation', '978-7-5213-2024-4', NULL),
(9, 'Grimms\' Fairy Tales', 'Jacob Grimm and Wilhelm Grimm', 'Children', 'Fairy Tales', 102.00, 'glth.png', 'Grimm\'s Fairy Tales: classic stories of magic and adventure.', '2025-11-26', 2, 'Kindle Edition', NULL, NULL),
(10, 'DP Chinese A Course Literary Terminology', 'Dongning', 'Languages', 'Chinese', 72.00, 'chinesel.png', 'This book is a new edition of the International Baccalaureate.', '2024-01-29', 1, 'Joint Publishing (Hong Kong)', '9789620454066', NULL),

-- 补充数据 (ID 19-33, 基于之前的生成请求)
(19, 'Standard Chinese Level 1', 'Beijing Language Press', 'Languages', 'Chinese', 29.99, 'allknow.png', 'The best textbook for beginners learning Mandarin Chinese.', '2023-05-15', 150, 'Beijing Press', '978-7-5619-3709-9', NULL),
(20, 'Business English Communication', 'Cambridge University', 'Languages', 'English', 45.00, 'allknow.png', 'Essential guide for international business negotiations.', '2022-08-20', 300, 'Cambridge Press', '978-1-107-68322-0', NULL),
(21, 'Japanese Hiragana Guide', 'Tokyo Talk', 'Languages', 'Japanese', 15.50, 'allknow.png', 'Master the basics of Japanese writing in 2 weeks.', '2025-11-22', 20, 'Tokyo Press', '978-4-88319-603-6', NULL),
(22, 'Korean Dramas & Culture', 'Kim Soo-hyun', 'Languages', 'Korean', 22.00, 'allknow.png', 'Learn Korean through popular K-Dramas and pop culture.', '2021-03-10', 800, 'Seoul Media', '978-8-9730-0876-1', NULL),
(23, 'Arabic Script Practice', 'Al-Jazeera Education', 'Languages', 'Arabic', 18.99, 'allknow.png', 'A workbook to practice beautiful Arabic calligraphy.', '2020-11-05', 100, 'Cairo Books', '978-9-7741-6699-5', NULL),
(24, 'Andersen\'s Fairy Tales', 'Hans Christian Andersen', 'Children', 'Fairy Tales', 12.50, 'allknow.png', 'Timeless classics including The Little Mermaid and The Ugly Duckling.', '2019-12-25', 5000, 'Penguin Kids', '978-0-141-32901-7', NULL),
(25, 'The Very Hungry Caterpillar', 'Eric Carle', 'Children', 'Picture Books', 16.00, 'allknow.png', 'A classic picture book that teaches counting and days of the week.', '2018-06-01', 12000, 'World of Eric Carle', '978-0-399-22690-8', NULL),
(26, 'Legend of the Condor Heroes', 'Jin Yong', 'Fiction', 'Wuxia', 28.00, 'allknow.png', 'The most famous martial arts novel in Chinese history.', '2018-02-28', 15000, 'Ming Ho House', '978-0-857-05300-8', NULL),
(27, 'The Three-Body Problem', 'Liu Cixin', 'Fiction', 'Fantasy', 26.00, 'allknow.png', 'A science fiction masterpiece about first contact with aliens.', '2025-11-24', 50, 'Chongqing Press', '978-0-7653-7706-7', NULL),
(28, 'Romeo and Juliet', 'William Shakespeare', 'Fiction', 'Romance', 9.99, 'allknow.png', 'The tragic tale of star-crossed lovers in Verona.', '2015-06-15', 400, 'Classic Reads', '978-0-7434-7711-6', NULL),
(29, 'The Shining', 'Stephen King', 'Fiction', 'Horror', 19.50, 'allknow.png', 'A terrifying story of a haunted hotel and a family in isolation.', '2020-10-31', 3500, 'Doubleday', '978-0-385-12167-5', NULL),
(30, 'Sapiens: A Brief History', 'Yuval Noah Harari', 'Fiction', 'History', 28.00, 'allknow.png', 'A groundbreaking narrative of humanity\'s creation and evolution.', '2021-09-10', 8000, 'Harper', '978-0-06-231609-7', NULL),
(31, 'Romance of the Three Kingdoms', 'Luo Guanzhong', 'Classics', 'Eastern', 32.00, 'allknow.png', 'An epic saga of war, loyalty, and strategy in ancient China.', '2010-01-01', 450, 'Tuttle Publishing', '978-0-8048-4393-5', NULL),
(32, 'Great Expectations', 'Charles Dickens', 'Classics', 'Western', 11.50, 'allknow.png', 'The coming-of-age story of an orphan named Pip.', '2012-04-12', 320, 'Penguin Classics', '978-0-141-43956-3', NULL),
(33, 'Harry Potter 2025 Edition', 'J.K. Rowling', 'Fiction', 'Fantasy', 39.99, 'allknow.png', 'Special anniversary edition with new illustrations.', '2025-11-01', 5, 'Bloomsbury', '978-1-4088-5565-2', 'HOT'),

-- 补充数据 (ID 34-37, 新增语言)
(34, 'Italian for Beginners', 'Luigi Rossi', 'Languages', 'Italian', 24.50, 'allknow.png', 'A comprehensive guide to learning the Italian language and culture.', '2023-09-15', 120, 'Rome Learning', '978-88-544-1234-1', NULL),
(35, 'Spanish Grammar in Use', 'Maria Garcia', 'Languages', 'Spanish', 30.00, 'allknow.png', 'The ultimate reference book for Spanish grammar mastery.', '2021-06-20', 2500, 'Madrid Press', '978-84-376-5678-9', NULL),
(36, 'Read & Write Thai', 'Somchai Jai', 'Languages', 'Thai', 19.99, 'allknow.png', 'Learn the Thai script and basic conversation skills.', '2024-01-10', 85, 'Bangkok Books', '978-616-18-9012-3', NULL),

(37, 'French Patisserie', 'Pierre Hermé', 'Languages', 'French', 40.00, 'allknow.png', 'Discover the art of French baking with delicious recipes.', '2025-11-20', 45, 'Paris Culinary', '978-2-08-020345-6', NULL);
