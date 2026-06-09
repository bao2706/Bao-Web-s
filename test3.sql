-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th6 09, 2026 lúc 04:13 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `test3`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `income`
--

CREATE TABLE `income` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `content` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `income`
--

INSERT INTO `income` (`id`, `user_id`, `amount`, `content`) VALUES
(1, 1, 11111, 'Food & Dining ????'),
(2, 1, 2222, 'Food & Dining ????'),
(3, 1, 2233, 'Food & Dining ????'),
(4, 1, 444555, 'Food & Dining ????'),
(5, 1, 444555, 'Food & Dining ????');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `members`
--

INSERT INTO `members` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'ズオンヤティン', 'giabaolop8@gmail.com', '$2y$10$MAx10IyCleOf/QrxcTgXw.Z1VJE6DXV4c9/R56EIGPlsX3Ax/3kqm', '2026-06-06 10:13:17'),
(2, 'DHGB', 'giabaolop7@gmail.com', '$2y$10$bkpE4dUhlrNqMEBto8aHyOjnfO/pGiABbZB0OubxvcLsgn.Fhe9Em', '2026-06-06 10:47:55'),
(3, 'ズオンヤティン', 'giabaolop6@gmail.com', '$2y$10$2oK9AE/SyZwNNSyYWFmaQeUJgcSLwQ5cIPQcOMLQEb0wkfJImYwv2', '2026-06-06 10:51:13'),
(4, 'ズオンヤティン', 'giabaolop5@gmail.com', '$2y$10$pFCQEkhqCWVqtv/2UT3WU.xY6VHjjnBrX1sVLNCT2D.0HgDFnIycK', '2026-06-06 10:54:58'),
(5, 'ズオンヤティン', 'giabaolop70@gmail.com', '$2y$10$/WuRKrGdbg40JEzRsAw0S.6/4foc/k3L0JWLroZb95pmaWdAfwGYy', '2026-06-06 10:55:58');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `income`
--
ALTER TABLE `income`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `income`
--
ALTER TABLE `income`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `income`
--
ALTER TABLE `income`
  ADD CONSTRAINT `income_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `members` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
