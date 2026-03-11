-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2026 at 03:56 PM
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
-- Database: `road_side_companion`
--

-- --------------------------------------------------------

--
-- Table structure for table `users_details`
--

CREATE TABLE `users_details` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(10) NOT NULL,
  `password` varchar(16) NOT NULL,
  `accounttype` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_details`
--

INSERT INTO `users_details` (`id`, `name`, `email`, `phone_number`, `password`, `accounttype`) VALUES
(1, 'SIBA SANKAR DASH', 'siba@gmail.com', '8338018281', 'Siba@2004', 'administrator'),
(4, 'Ajaya', 'ajaya@gmail.com', '8917628204', 'Ajaya@2003', 'administrator'),
(5, 'Bhabani Sankar Nanda', 'bhabani@gmail.com', '7008736659', 'Bhabani@2004', 'administrator'),
(6, 'Silu Muduli', 'silu@gmail.com', '9090565653', 'Silu@2003', 'administrator'),
(8, 'surajit', 'surajit@gmail.com', '8008736659', 'Sura@2001', 'customer'),
(9, 'Deba', 'deba1@gmail.com', '6371095622', 'Deba@2003', 'service-provider'),
(10, 'Ram Chandra', 'ram1@gmail.com', '8805866968', 'Ram@1', 'customer'),
(11, 'Mana Ranjan', 'mana@gmail.com', '7856625887', 'Mana@1', 'service-provider');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users_details`
--
ALTER TABLE `users_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users_details`
--
ALTER TABLE `users_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
