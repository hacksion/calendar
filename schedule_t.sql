-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2023-07-19 14:54:40
-- サーバのバージョン： 10.4.28-MariaDB
-- PHP のバージョン: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `test_db`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `schedule_t`
--

CREATE TABLE `schedule_t` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) NOT NULL,
  `schedule_datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `title` varchar(100) NOT NULL,
  `content` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `schedule_t`
--

INSERT INTO `schedule_t` (`id`, `created_at`, `modified_at`, `user_id`, `schedule_datetime`, `title`, `content`, `status`) VALUES
(1, '2023-07-11 09:28:00', '2023-07-18 17:07:59', 1, '2023-07-20 09:00:00', 'ランチGo!!!', 'ああああ', 1),
(2, '2023-07-11 09:28:00', '2023-07-14 11:46:44', 1, '2023-07-26 09:27:00', 'ディナーGo!', NULL, 1),
(3, '2023-07-11 09:42:18', '2023-07-19 12:01:49', 1, '2023-07-12 09:00:00', '出勤', '通勤ラッシュ\r\n電車', 1),
(6, '2023-07-18 11:31:18', '2023-07-18 11:46:24', 1, '2023-07-18 11:31:00', 'テスト', '', 1),
(7, '2023-07-18 11:33:26', '2023-07-19 10:57:12', 1, '2023-07-21 09:00:00', 'テスト２', '', 1);

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `schedule_t`
--
ALTER TABLE `schedule_t`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedule_datetime` (`schedule_datetime`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `schedule_t`
--
ALTER TABLE `schedule_t`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
