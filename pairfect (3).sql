-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 06. Nov, 2025 23:49 PM
-- Tjener-versjon: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pairfect`
--

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `conversations`
--

CREATE TABLE `conversations` (
  `convo_id` bigint(20) NOT NULL,
  `userid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `messages`
--

CREATE TABLE `messages` (
  `msg_id` bigint(20) NOT NULL,
  `convo_id` bigint(20) NOT NULL,
  `role` enum('user','model') NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `users`
--

CREATE TABLE `users` (
  `userid` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `users`
--

INSERT INTO `users` (`userid`, `username`, `email`, `password_hash`) VALUES
(7, 'test', 'test@example.com', '$2y$10$gVArot8IyhUS1rpOw8NEWOV7M17lcubNL6bE9fl/OYhwXSEalx7M2'),
(8, '', 'hei@hallo.no', '$2y$10$iBVvORulHZCP2lPidv6L8.cYzCghYELpnAtvwTo2d2QCND8Yi9dYu'),
(9, '', 'ola.nordmann@example.com', '$2y$10$7QpwqhYxMymucjawTmAKYOHFhUAvnj1qb8EEo7FZIEaDJcRuLnxz.'),
(10, 'halla', 'halla@balla.com', '$2y$10$EZIENy33vuNybeAyjBPwGOzIKjT74JcfmDduCZDPCA.8Mn9BfELSq'),
(11, 'dette', 'er@en.test', '$2y$10$cNuax8DhPYk7EcAE40.LIe/rSkvpQ.5QnRhMoNp7ktSiUvnqs9Tpy'),
(12, 'Random', 'random@example.com', '$2y$10$zV8qkqoze7bx85wNg3qToeVoSPUMD4G8GM3iXQa6qReiBX72eUKOu');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`convo_id`),
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`msg_id`),
  ADD KEY `convo_id` (`convo_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `convo_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `msg_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Begrensninger for dumpede tabeller
--

--
-- Begrensninger for tabell `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE;

--
-- Begrensninger for tabell `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`convo_id`) REFERENCES `conversations` (`convo_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
