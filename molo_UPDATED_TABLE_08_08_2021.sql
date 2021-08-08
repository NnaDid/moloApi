-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 08, 2021 at 07:40 PM
-- Server version: 10.4.6-MariaDB
-- PHP Version: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `molo`
--

-- --------------------------------------------------------

--
-- Table structure for table `birthday_msg`
--

CREATE TABLE `birthday_msg` (
  `msg_id` int(11) NOT NULL,
  `msg` varchar(200) NOT NULL,
  `createdAt` varchar(15) NOT NULL,
  `updatedAt` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `donation`
--

CREATE TABLE `donation` (
  `don_id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `amt_donated` decimal(6,2) NOT NULL,
  `txRef` varchar(32) NOT NULL,
  `createdAt` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `m_transactions`
--

CREATE TABLE `m_transactions` (
  `txId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `amount` varchar(6) NOT NULL,
  `txtTpe` varchar(15) NOT NULL,
  `txRef` varchar(20) DEFAULT NULL,
  `createdAt` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `m_transactions`
--

INSERT INTO `m_transactions` (`txId`, `userId`, `amount`, `txtTpe`, `txRef`, `createdAt`) VALUES
(1, 3, '200', 'funding', '66565TDF56543', '2021-08-08 '),
(2, 3, '200', 'funding', '66565TDF56543', '2021-08-08 '),
(3, 3, '200', 'funding', '66565TDF56543', '2021-08-08 '),
(4, 3, '200', 'funding', '600776TDF56543', '2021-08-08 13:39:23'),
(5, 3, '200', 'funding', '600776TDF56543', '2021-08-08 13:40:43'),
(6, 3, '200', 'funding', '600776TDF56543', '2021-08-08 13:42:35'),
(7, 3, '200', 'funding', '600776TDF56543', '2021-08-08 13:46:15'),
(8, 2, '200', 'funding', '788787868TYUTYUFFC7', '2021-08-08 16:47:28'),
(9, 2, '100', 'AIRTIME_VTU', 'nna992298', '2021-08-08 16:48:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(80) NOT NULL,
  `phone` varchar(16) NOT NULL,
  `dob` varchar(40) DEFAULT NULL,
  `church` varchar(50) DEFAULT NULL,
  `church_group` varchar(50) DEFAULT NULL,
  `church_zone` varchar(50) DEFAULT NULL,
  `partnership_arm` enum('HEALING SCHOOL','INNER CITY MISSION','RAPSODY OF REALITIES') DEFAULT NULL,
  `giving_freq` enum('WEKLY','MONTHLY','QUATERLY','YEARLY') DEFAULT NULL,
  `bible_study_plan` enum('YEAR ONE','YEAR TWO') NOT NULL DEFAULT 'YEAR ONE',
  `recurrentBilling` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `paswd` varchar(200) NOT NULL,
  `createdAt` varchar(20) NOT NULL,
  `updatedAt` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `dob`, `church`, `church_group`, `church_zone`, `partnership_arm`, `giving_freq`, `bible_study_plan`, `recurrentBilling`, `paswd`, `createdAt`, `updatedAt`) VALUES
(2, 'NnaDid', 'nnadidsuccess@gmail.com', '08139240318', NULL, NULL, NULL, NULL, NULL, NULL, 'YEAR ONE', 'NO', '$2y$10$/gEQJhfmWnLTeNgSBRSJpe8/ZtTiHjCyi4sKrBvW.FnKJe4CfagI2', '2021-08-06 17:50:22', ''),
(3, 'NnaDid Two', 'dids@gmail.com', '09066781979', NULL, NULL, NULL, NULL, NULL, NULL, 'YEAR ONE', 'NO', '$2y$10$/qN0hXlnAVk4ywrW41YFQudf.xjn4X3sgof2bMfyRq4hT4fRLFCEu', '2021-08-06 18:32:11', '');

-- --------------------------------------------------------

--
-- Table structure for table `wallet`
--

CREATE TABLE `wallet` (
  `wal_id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `wal_balance` decimal(6,2) NOT NULL,
  `updatedAt` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wallet`
--

INSERT INTO `wallet` (`wal_id`, `userId`, `wal_balance`, `updatedAt`) VALUES
(1, 3, '1600.00', '2021-08-08 13:46:15'),
(2, 2, '100.00', '2021-08-08 16:48:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `donation`
--
ALTER TABLE `donation`
  ADD PRIMARY KEY (`don_id`);

--
-- Indexes for table `m_transactions`
--
ALTER TABLE `m_transactions`
  ADD PRIMARY KEY (`txId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wallet`
--
ALTER TABLE `wallet`
  ADD PRIMARY KEY (`wal_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `donation`
--
ALTER TABLE `donation`
  MODIFY `don_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `m_transactions`
--
ALTER TABLE `m_transactions`
  MODIFY `txId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wallet`
--
ALTER TABLE `wallet`
  MODIFY `wal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
