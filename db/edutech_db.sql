-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 14, 2025 at 02:11 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `edutech_db`
--
CREATE DATABASE IF NOT EXISTS `edutech_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `edutech_db`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('student','admin') NOT NULL DEFAULT 'student',
  `status` enum('pending','active','inactive','banned') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `role`, `status`) VALUES
(4, 'andinh', 'andinh151104@gmail.com', '$2y$10$wX9s5B2I6jkG83mXtb8IEOAsbGOsIJ6uRIwR1XGZ1X4jmvkvlHPQa', 'admin', 'active'),
(6, 'abc', 'abc@gmail.vn', '$2y$10$Qqxw8PJSdME1VDZmTOc.Duiv83RNvMZAyYAow.Z8Ef/lKWmtsMq.6', 'student', 'pending'),
(7, 'trieu', 'toilatrieu2004@gmail.com', '$2y$10$q.5n/YtiC3NyjBWLbiE.DOB5tbEelRrXXQiBhIzji/O0snoA1.i3a', 'student', 'active'),
(8, 'Trieu Tran', 'tranthientrieu2004@gmail.com', '$2y$10$iAhRcrBZnAB8TzWtNJFQiuwcdJ3RC8k3V/5oXjeVEfKbIhOxlw8nu', 'student', 'active'),
(9, 'dh', 'dh52201647@student.stu.edu.vn', '$2y$10$pKKmY5Afzqd7hW4pZ4ZTZ.K6y6r93lnIJ68rsfzX2WDzrr2JWGFCK', 'student', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

DROP TABLE IF EXISTS `user_tokens`;
CREATE TABLE IF NOT EXISTS `user_tokens` (
  `token_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `token_type` enum('email_verification','password_reset') NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`token_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_tokens`
--

INSERT INTO `user_tokens` (`token_id`, `user_id`, `token_hash`, `token_type`, `expires_at`) VALUES
(6, 6, 'fbe260cc06d012c6cc22972583e3d5bc57ee619eebd439e363c232c371de2fc4', 'email_verification', '2025-11-11 17:05:42'),
(8, 4, '80c7d106f8f02e0d91106f0f90abd796801612393c77833362df4e44acee808f', 'password_reset', '2025-11-12 00:00:09');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
