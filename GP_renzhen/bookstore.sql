-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025 年 11 月 27 日 10:45
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `bookstore`
--

-- --------------------------------------------------------

--
-- 資料表結構 `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) NOT NULL,
  `parent_category` varchar(50) DEFAULT '其他',
  `price` decimal(10,2) NOT NULL,
  `cover_image` varchar(255) DEFAULT 'default.jpg',
  `description` text DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `sales_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `publisher` varchar(100) DEFAULT 'Knowledge Press',
  `isbn` varchar(20) DEFAULT '978-0-00-000000-0',
  `category` varchar(50) DEFAULT 'General',
  `manual_tag` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `parent_category`, `price`, `cover_image`, `description`, `publish_date`, `sales_count`, `created_at`, `publisher`, `isbn`, `category`, `manual_tag`) VALUES
(1, 'Legend of the Condor Heroes', 'Jin Yong', 'Fiction', 28.00, 'sdyxz.jpg', 'The most famous martial arts novel in Chinese history.', '2018-02-28', 15000, '2025-11-26 05:08:12', 'Ming Ho House', '978-0-857-05300-8', 'Wuxia', NULL),
(2, 'Learn Spanish Fast for Adult Beginners: 3-in-1 Workbook', 'Speak Abroad Academy', 'Languages', 161.00, 'spanishl.jpg', '✦ NEW EDITION 2025 ✦', '2024-04-12', 2500, '2025-11-26 07:22:21', 'Madrid Press', '978-84-376-5678-9', 'Spanish', NULL),
(3, 'Let\'s Study Korean', 'Bridge Education', 'Languages', 22.00, 'koreanl.jpg', 'Let’s Study Korean – Complete Work Book for Grammar, Spelling, Vocabulary, and Reading Comprehension is an essential supplement for your Korean study needs as it brings a carefully designed set of questions covering Korean grammar, Hangul spelling, vocabulary drills, and reading comprehension.', '2021-03-10', 800, '2025-11-26 05:08:12', '', '978-8-9730-0876-1', 'Korean', NULL),
(4, 'Italian Grammar Made Easy', 'Lingo Mastery ', 'Languages', 90.00, 'italianl.jpg', 'Ready to unlock the secrets of Italian grammar?\r\n\r\nItalian, a language of romance and history, spoken by millions, is known for its melodic tone and rich cultural heritage. And though it is relatively beginner-friendly, Italian can have challenges that can make a new learner scratch their head multiple times. Mamma mia!\r\n\r\nHowever, we’ve now made it easier than ever to step into the world of Italian with Italian Grammar Made Easy. This comprehensive workbook is a treasure trove of knowledge, containing units and chapters meticulously crafted to guide you through the fundamental to advanced elements of Italian grammar.\r\n\r\nLet’s explore what Italian Grammar Made Easy offers:\r\n\r\nInsightful units, each focusing on key aspects of Italian grammar. From learning how to read and understand subject pronouns and verbs, to mastering the complexities of sentence structures and verb tenses, every chapter is a step towards fluency.\r\nEngaging exercises in each chapter provide practical application of the lessons, challenging and enhancing your grasp of Italian. These exercises include everything from creating sentences to understanding adverbs, adjectives, and much more.\r\nThe workbook is peppered with Italian tongue twisters, adding a fun twist to your learning experience while improving pronunciation and fluency.\r\nBONUS: Tired of reading text all the time? Worry not – we have images all throughout the book, as well as audio exercises to help you master the spoken tongue.\r\nEXTRA FEATURES: Dive into valuable sections like reading comprehension, a list of \'False Friends\' (commonly confused words in Italian and English), and handy charts for irregular verbs, reflexive verbs, and reciprocal verbs.\r\nFor every challenge you might face, an extensive answer key offers detailed explanations, ensuring a clear understanding of each topic.\r\nFrom the basics of numbers and articles to the nuances of everyday conversation and email writing, Italian Grammar Made Easy is your comprehensive guide to mastering the Italian language.\r\n\r\nStart your journey to Italian grammar mastery today. Get your hands on Italian Grammar Made Easy and begin your adventure into the beautiful world of the Italian language!\r\n\r\n', '2024-08-12', 121, '2025-11-26 07:22:21', 'Rome Learning', '978-88-544-1234-1', 'Italian', NULL),
(5, 'Learn Thai in 100 Days: The 100% Natural Method to Finally Get Results with Thai!', 'Natura Lingua ', 'Languages', 72.00, 'thail.jpg', 'Ever dreamt of speaking Thai like a native? NaturaLingua can turn that dream into reality in just 100 days. Our method is natural and stress-free, mirroring the way you naturally learned your first language. 🌱\r\n\r\nShort on time and disappointed by the resources out there? Your search ends here! Born from 7 years of in-depth research, NaturaLingua uses techniques from renowned polyglots, offering a seamless and rewarding path to learning Thai. 📚\r\n\r\nImagine a guide that not only thoroughly explores Thai but also fosters natural understanding and smooth conversations, guiding your learning every step of the way.\r\n\r\nFrustrated with traditional methods that fail to deliver? NaturaLingua is different. Say goodbye to memorizing endless conjugation tables and sifting through dense grammar books. You didn’t learn your first language that way; why start now?\r\n\r\nNaturaLingua employs a natural, science-based approach, utilizing the latest cognitive research and polyglot strategies to make learning Thai intuitive and effortless.', '2024-05-01', 88, '2025-11-26 07:22:21', 'Bangkok Books', '978-616-18-9012-3', 'Thai', NULL),
(6, 'The Three-Body Problem', 'Liu Cixin', 'Fiction', 26.00, 'allknow.png', 'A science fiction masterpiece about first contact with aliens.', '2025-11-24', 50, '2025-11-26 05:08:12', 'Chongqing Press', '978-0-7653-7706-7', 'Fantasy', NULL),
(7, 'Learn French Fast for Adult Beginners', 'Speak Abroad Academy', 'Languages', 40.00, 'frenchl.jpg', '❗With over 100,000 copies sold and over 1,000 positive reviews across platforms, the NEW 2025 Edition has been enriched with 8 New Bonuses to improve your learning experience.\r\n\r\nStruggling with French? Learning a new language can be tricky, but it doesn’t have to be a struggle. This 8-in-1 bundle makes learning easy with short lessons and interactive exercises—so you start speaking naturally, fast.\r\n\r\nWhat’s Inside?\r\n\r\n✅ 8 Full-Length Books – Everything you need, from absolute beginner to advanced.\r\n✅ Bite-Sized 15-Minute Lessons – Learn at your own pace with easy-to-follow sessions.\r\n✅ 150+ Interactive Exercises – Turn passive reading into active learning, practical drills.\r\n✅ Short Stories & Real Conversations – Speak and understand French faster.\r\n✅ Essential & Advanced Vocabulary – Master useful words and phrases for everyday life.\r\n✅ Exclusive Bonuses – Study plans, audio lessons, flashcards & more!\r\n\r\nThis 8-in-1 Value-Packed Bundle includes:\r\n\r\nBook 1 & 2 – Beginner & Intermediate 15-Minute Lessons + Interactive Exercises for fast progress.\r\nBook 3 & 4 – Essential Words & Phrases to confidently navigate everyday conversations.\r\nBook 5 & 8 – Short Stories (Beginner to Advanced) with translations to boost comprehension.\r\nBook 6 – Advanced 15-Minute Lessons + Interactive Drills to refine your skills.\r\nBook 7 – Real-World Dialogues & Conversations for fluency and confidence.\r\n\r\nElevate Your Conversation Skills for Seamless Interactions, with:\r\n\r\nReach CEFR C1 (Advanced) Fast – Short, structured lessons designed for real progress.\r\n150+ Effective Exercises – Strengthen your writing and speaking skills effortlessly.\r\nThe 10% of French You’ll Use 90% of the Time – No pointless memorization, just practical language.\r\n2,000+ Must-Know Words & Phrases – Speak naturally in everyday situations.\r\nFeel confident in any situation—whether ordering food, asking for directions, or making friends.\r\nEngaging Short Stories & Dialogues – Learn through real-world examples with translations.\r\nClear Pronunciation Guide – Sound natural with easy-to-follow tips.\r\n\r\nGet These 6 Powerful Extras to Fast-Track Your French Mastery:\r\n\r\n🎧 Full Audiobook Version – Learn anytime, anywhere, hands-free.\r\n📄 Printable Grammar Cheat Sheets – Quick-reference guides for key rules.\r\n🃏 French Flashcards – Fun, visual memory boosters for faster recall.\r\n📝 Practice Worksheets – Extra drills to reinforce every lesson.\r\n📜 Top 1,000 Most Common Words – Master the vocabulary you’ll use daily.\r\n📅 30-Day Study Plan – A step-by-step guide to keep you on track.\r\n\r\nPLUS! 2 Exclusive Bonuses to Guarantee Your Success:\r\n\r\n🎥 Learn French for Adult Beginners Video Course – 128 hours of expert-led Masterclass lessons ($297 Value – 30 Days Free Trial).\r\n🚀 Advanced Study Plan – Unlock accelerated progress and reach fluency faster ($47 Value).', '2025-04-26', 46, '2025-11-26 07:22:21', 'Paris Culinary', '978-2-08-020345-6', 'French', NULL),
(8, 'MARUGOTO Japanese Language and Culture Introduction A1 Understanding Section', 'Japan Foundation', 'Languages', 65.00, 'japanl.jpeg', 'This book is one of the \"MARUGOTO Japanese Language and Culture\" series textbooks, serving as an introductory level textbook primarily for beginners with no prior Japanese knowledge. The content is divided into nine main topics, each containing two lessons. Topic 1 covers basic Japanese knowledge, including hiragana and katakana; Topic 2 covers self-introduction and family introduction; Topic 3 introduces favorite dishes and dining locations; Topic 4 describes one\'s own house; Topic 5 introduces one\'s lifestyle habits; Topic 6 introduces one\'s hobbies; Topic 7 covers transportation and directional terms; Topic 8 covers shopping; and Topic 9 introduces one\'s holiday activities. Answers to the exercises are included at the end of the book.', '2018-11-06', 2, '2018-11-30 03:58:36', 'Japan Foundation', '978-7-5213-2024-4', 'Japanese', NULL),
(9, 'Grimms\' Fairy Tales', ' Jacob Grimm and Wilhelm Grimm', 'Children', 102.00, 'glth.png', 'Grimm\'s Fairy Tales: Grimm\'s Fairy Tales, also known as Child And Family Fairy tale Collection is a world- famous tale collection of German folklores, fairy tales, myths and biographies scientifically collected, sorted, processed and published by Jacob Grimm and Wilhelm Grimm. The Grimm brothers preserved original elements in German folklores, and refined them with simple, lively and humorous styles. The first volume was published several days before Christmas in 1812;some 200 tales was included in this edition. It got world’s attention since then.', '2025-11-26', 2, '2025-11-26 04:42:52', 'Kindle Edition', '', 'Fairy Tales', NULL),
(10, 'DP Chinese A Course Literary Terminology Handbook (Second Edition)', 'Dongning', 'Languages', 72.00, 'chinesel.png', 'This book is a new edition of the *International Baccalaureate Diploma Programme Chinese A Course Literary Terminology Handbook (Traditional Chinese Version)* (ISBN: 9789620440144). The new edition mainly revises the \"Exam Preparation Tips\" section and adds an appendix, \"Examples of Literary Terminology Used in Drama Teaching.\"', '2024-01-29', 1, '2025-11-26 04:11:48', 'Joint Publishing (Hong Kong) Limited', '9789620454066', 'Chinese', NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `feedback`
--

INSERT INTO `feedback` (`id`, `full_name`, `email`, `phone`, `message`, `submitted_at`) VALUES
(1, 'Chan Hoi Sui', 'chrspm9604@gmail.com', '52257179', 'good job', '2025-11-27 07:42:45'),
(2, 'rex', 'rex1004077179@gmail.com', '52257179', 'test', '2025-11-27 07:43:51');

-- --------------------------------------------------------

--
-- 資料表結構 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(3, 'admin', 'admin@gmail.com', '$2y$10$vDYIEvDS2Jf19GqjjYmAv.BukR1Rqm7ksU50XNBKwQqAibO3o0d4m', '2025-11-26 09:32:44'),
(4, 'rex', 'rex1004077179@gmail.com', '$2y$10$WTHzDV0gFdcKbsW8GMlOKO/aR5WfvN87QoFkoc2qUMGURi4IX6wWS', '2025-11-27 07:43:15');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
