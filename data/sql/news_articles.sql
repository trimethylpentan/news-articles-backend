CREATE TABLE `news_articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) COLLATE utf8mb4_german2_ci NOT NULL,
  `text` text COLLATE utf8mb4_german2_ci NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;
