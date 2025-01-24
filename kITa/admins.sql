-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 12, 2025 at 07:35 AM
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
-- Database: `lost_found_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) NOT NULL,
  `username` varchar(100) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `_status` varchar(10) DEFAULT 'enable',
  `admin_level` enum('security_moderator','admin') NOT NULL DEFAULT 'security_moderator'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `fname`, `lname`, `password`, `profile_image`, `_status`, `admin_level`) VALUES
(1, 'hanz', 'Hanzel', 'Ramirez', '1a1dc91c907325c69271ddf0c944bc72', 'uploads/profile_images/admin_1_1729513550.jpg', 'enable', 'security_moderator'),
(2, 'alex', 'Alexander', 'Llano', '534b44a19bf18d20b71ecc4eb77c572f', NULL, 'enable', 'admin'),
(3, 'alexander', 'Alexander', 'Llano', '18c1e101aed2d47d493f23ef96188134', NULL, 'enable', 'security_moderator'),
(4, 'josh', 'Joshua', 'Penuela', '1a1dc91c907325c69271ddf0c944bc72', 'uploads/profile_images/admin_16_1728275764.jfif', 'enable', 'security_moderator'),
(7, 'admin1', 'Romulo', 'Gomez', '5f4dcc3b5aa765d61d8327deb882cf99', '', 'enable', 'security_moderator');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
