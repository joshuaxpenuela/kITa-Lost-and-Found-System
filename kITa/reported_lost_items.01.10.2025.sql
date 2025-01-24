-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 10, 2025 at 09:41 AM
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
-- Table structure for table `reported_lost_items`
--

CREATE TABLE `reported_lost_items` (
  `id_item` int(11) NOT NULL,
  `Fname` varchar(255) NOT NULL,
  `Lname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_no` varchar(100) NOT NULL,
  `dept_college` varchar(255) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_category` varchar(255) NOT NULL,
  `location_lost` varchar(255) NOT NULL,
  `report_date` date NOT NULL,
  `report_time` time(6) NOT NULL,
  `other_details` varchar(500) NOT NULL,
  `img1` varchar(255) DEFAULT NULL,
  `img2` varchar(255) DEFAULT NULL,
  `img3` varchar(255) DEFAULT NULL,
  `img4` varchar(255) DEFAULT NULL,
  `img5` varchar(255) DEFAULT NULL,
  `status` varchar(10) NOT NULL,
  `view_status` tinyint(4) DEFAULT 0 COMMENT '0=unread, 1=read	'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reported_lost_items`
--

INSERT INTO `reported_lost_items` (`id_item`, `Fname`, `Lname`, `email`, `contact_no`, `dept_college`, `item_name`, `item_category`, `location_lost`, `report_date`, `report_time`, `other_details`, `img1`, `img2`, `img3`, `img4`, `img5`, `status`, `view_status`) VALUES
(1, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09622849505', 'CEIT', 'Selfie', 'Others', 'DIT shed', '2025-01-09', '10:08:00.000000', 'Blurred', '677fd8b5894ce_img1_1736431796732.jpg', '', '', '', '', 'Missing', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reported_lost_items`
--
ALTER TABLE `reported_lost_items`
  ADD PRIMARY KEY (`id_item`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reported_lost_items`
--
ALTER TABLE `reported_lost_items`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
