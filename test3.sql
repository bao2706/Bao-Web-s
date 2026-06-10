-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2026-06-10 15:19:58
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `test3`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `income`
--

CREATE TABLE `income` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `type` enum('income','expense') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- テーブルのデータのダンプ `income`
--

INSERT INTO `income` (`user_id`, `amount`, `category`, `type`) VALUES
(1, 1212, 'Salary 💰', 'income'),
(1, 1212322, 'Food 🍔', 'expense'),
(1, 234423, 'Investment 📈', 'income');

-- --------------------------------------------------------

--
-- テーブルの構造 `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `members`
--

INSERT INTO `members` (`id`, `name`, `email`, `password`, `created`) VALUES
(1, 'BAO', 'giabaolop8@gmail.com', '$2y$10$CZA1CPfT51V2OtYA9kGymuoXKJHRkpjZq927OKc9I/4NDLQ13UTAG', '2026-06-02 12:02:33'),
(4, 'Thinh', 'giabaolop6@gmail.com', '$2y$10$Z2mhMp4TLa5uUmEjWZOEVeXJ96W9FpjD0aq4FJWTPBexghxrd1sGW', '2026-06-05 09:59:20'),
(5, 'CHO', 'giabaolop7@gmail.com', '$2y$10$KhJKUgXkP5YuXt7LB.VQQ.WHXTCT7JWo1iB6fLODOZcEvv5AEYPeS', '2026-06-05 13:52:36'),
(8, 'MEO', 'giabaolop11@gmail.com', '$2y$10$Q0UN8.oskZY0IQVUEE/Ha.Mj2a7BTEwnDYkmZkGZ1JNB0Rk.eCEt6', '2026-06-05 13:55:36'),
(9, 'BAO DZ SIEU CAP VIP PRO', 'giabaolop10@gmail.com', '$2y$10$Eo/J8s19k/7a6baMGfsQ/uy69UMhvFrG/w0B/YhaRMBiC23SON3Vu', '2026-06-08 09:10:53'),
(10, 'chuot', 'giabaolop12@gmail.com', '$2y$10$BiTY1UiH1muTuP3LIs0hO.bgfSV8bbn.C1yppSGXQvudIqyioaV8.', '2026-06-10 10:06:41');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
