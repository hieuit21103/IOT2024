-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th9 03, 2024 lúc 10:39 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `iot`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `auto_conf`
--

CREATE TABLE `auto_conf` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `speed` int(11) NOT NULL,
  `time` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `auto_conf`
--

INSERT INTO `auto_conf` (`id`, `name`, `speed`, `time`, `status`) VALUES
(1, 'amnis', 127, '6 8 10', 1),
(2, 'brass', 133, '6 8 10', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL,
  `mode` text NOT NULL,
  `station` varchar(50) NOT NULL,
  `speed` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `logs`
--

INSERT INTO `logs` (`id`, `timestamp`, `mode`, `station`, `speed`) VALUES
(1, '2024-09-01 12:00:00', 'auto', 'Station1', 150),
(2, '2024-09-01 12:00:00', 'auto', 'Station1', 150),
(3, '0000-00-00 00:00:00', 'manual', 'off', 127),
(4, '0000-00-00 00:00:00', 'auto', 'on', 127),
(5, '0000-00-00 00:00:00', 'auto', 'on', 127),
(6, '0000-00-00 00:00:00', 'auto', 'on', 127),
(7, '0000-00-00 00:00:00', 'auto', 'on', 127),
(8, '0000-00-00 00:00:00', 'auto', 'on', 127),
(9, '0000-00-00 00:00:00', 'auto', 'on', 127),
(10, '0000-00-00 00:00:00', 'auto', 'on', 127),
(11, '0000-00-00 00:00:00', 'auto', 'on', 127);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `auto_conf`
--
ALTER TABLE `auto_conf`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`) USING HASH;

--
-- Chỉ mục cho bảng `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `auto_conf`
--
ALTER TABLE `auto_conf`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
