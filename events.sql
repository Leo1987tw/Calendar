-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2026-07-19 14:02:45
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
-- 資料庫： `calendar`
--

-- --------------------------------------------------------

--
-- 資料表結構 `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `event_date` date DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `during_time` time GENERATED ALWAYS AS (timediff(`end_time`,`start_time`)) VIRTUAL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `bg_color` varchar(7) NOT NULL,
  `border_color` varchar(7) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `events`
--

INSERT INTO `events` (`id`, `event_date`, `start_time`, `end_time`, `title`, `description`, `bg_color`, `border_color`, `created_at`, `deleted_at`) VALUES
(1, '2026-07-14', '00:00:00', '23:59:59', 'test', '', '#00FFFF', '', '2026-07-12 05:20:28', NULL),
(2, '2026-07-15', '00:05:57', '07:05:57', 'asdf', '', '#115678', '', '2026-07-19 01:06:39', NULL),
(3, '2026-07-15', '08:05:57', '09:05:57', 'asdf', '', '#115678', '', '2026-07-19 01:06:42', NULL),
(4, '2026-07-15', '11:07:17', '14:11:17', 'fasdf', '', '#154865', '', '2026-07-19 01:07:46', NULL),
(5, '2026-07-15', '15:07:17', '23:59:59', 'fasdf', '', '#154865', '', '2026-07-19 01:07:49', NULL);

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
