-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 13, 2025 at 10:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `xie_alumni`
--

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_text` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `sender_id`, `receiver_id`, `message_text`, `timestamp`) VALUES
(1, 1, 2, 'Hello, how are you?', '2025-04-20 09:08:22'),
(2, 4, 1, 'hiii bro', '2025-04-20 09:09:12'),
(3, 1, 4, 'hello how are you brother', '2025-04-20 09:17:57'),
(4, 1, 2, 'namaskar aniruddha', '2025-04-20 09:19:40'),
(5, 2, 1, 'hello sirr i m good boy', '2025-04-25 03:44:11'),
(6, 1, 2, 'good', '2025-04-25 03:45:29'),
(7, 2, 1, 'hi\r\n', '2025-04-25 03:58:11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `batch` varchar(50) DEFAULT NULL,
  `job` varchar(100) DEFAULT NULL,
  `role` enum('alumni','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `batch`, `job`, `role`) VALUES
(1, 'Admin User', 'admin@example.com', 'admin123', NULL, NULL, 'admin'),
(2, 'Aniruddha kale', 'aniruddha@gmail.com', 'ansh123', '2024', '0', 'alumni'),
(3, 'Ankita Mehta', 'ankita.mehta@xiealumni.com', 'pass123', '2018', 'Software Engineer at TCS', 'alumni'),
(4, 'Rahul Sharma', 'rahul.sharma@xiealumni.com', 'pass123', '2017', 'Backend Developer at Infosys', 'alumni'),
(5, 'Priya Nair', 'priya.nair@xiealumni.com', 'pass123', '2019', 'UI/UX Designer at Accenture', 'alumni'),
(6, 'Aditya Kulkarni', 'aditya.kulkarni@xiealumni.com', 'pass123', '2020', 'Cloud Engineer at Wipro', 'alumni'),
(7, 'Sneha Iyer', 'sneha.iyer@xiealumni.com', 'pass123', '2021', 'Mobile App Developer at Tech Mahindra', 'alumni'),
(8, 'Vikram Patil', 'vikram.patil@xiealumni.com', 'pass123', '2016', 'DevOps Engineer at Capgemini', 'alumni'),
(9, 'Ruchi Desai', 'ruchi.desai@xiealumni.com', 'pass123', '2015', 'Project Manager at L&T Infotech', 'alumni'),
(10, 'Karan Dsouza', 'karan.dsouza@xiealumni.com', 'pass123', '2018', 'Data Scientist at Fractal Analytics', 'alumni'),
(11, 'Neha Agarwal', 'neha.agarwal@xiealumni.com', 'pass123', '2019', 'Full Stack Developer at Zoho', 'alumni'),
(12, 'Siddharth Rao', 'siddharth.rao@xiealumni.com', 'pass123', '2022', 'Machine Learning Intern at CitiusTech', 'alumni'),
(14, 'rajeev dudhasagare', 'udgduagduagd@gmail.com', 'asasas', '2024', 'Software Engineer', 'alumni');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
