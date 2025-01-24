-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2024 at 08:05 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.0.25

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
  `password` varchar(100) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `_status` varchar(10) DEFAULT 'enable'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `profile_image`, `_status`) VALUES
(1, 'hanz', '1a1dc91c907325c69271ddf0c944bc72', 'uploads/profile_images/admin_1_1729513550.jpg', 'enable'),
(2, 'alex', '5f4dcc3b5aa765d61d8327deb882cf99', 'uploads/profile_images/admin_2_1731213363.jpg', 'enable'),
(3, 'alexander', '18c1e101aed2d47d493f23ef96188134', NULL, 'enable'),
(4, 'josh', '1a1dc91c907325c69271ddf0c944bc72', 'uploads/profile_images/admin_16_1728275764.jfif', 'enable'),
(5, 'alex1', '5f4dcc3b5aa765d61d8327deb882cf99', '', 'enable');

-- --------------------------------------------------------

--
-- Table structure for table `admin_message`
--

CREATE TABLE `admin_message` (
  `id` int(10) NOT NULL,
  `sender_id` int(10) NOT NULL,
  `receiver_id` int(10) NOT NULL,
  `admin_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `message` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `media_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_message`
--

INSERT INTO `admin_message` (`id`, `sender_id`, `receiver_id`, `admin_id`, `user_id`, `message`, `created_at`, `media_url`) VALUES
(1, 0, 0, 4, 1, 'Good day Joshua Penuela, I have received your attempt to reclaim a lost item.', '2024-11-09 11:43:56', '');

-- --------------------------------------------------------

--
-- Table structure for table `colleges`
--

CREATE TABLE `colleges` (
  `college_id` int(10) NOT NULL,
  `college` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colleges`
--

INSERT INTO `colleges` (`college_id`, `college`) VALUES
(1, 'Select College'),
(2, 'CAFENR'),
(3, 'CAS'),
(4, 'CCJ'),
(5, 'CED'),
(6, 'CEMDS'),
(7, 'CEIT'),
(8, 'CON'),
(9, 'CSPEAR'),
(10, 'CVMBS'),
(11, 'COM'),
(12, 'Graduate School'),
(13, 'Admin'),
(14, 'OSAS'),
(18, 'New College'),
(19, 'KITA');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_admin_sender` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `is_admin_sender`, `created_at`) VALUES
(1, 0, 2, 'hello', 1, '2024-10-17 12:27:31'),
(2, 0, 2, 'hello', 1, '2024-10-17 12:28:03');

-- --------------------------------------------------------

--
-- Table structure for table `reported_items`
--

CREATE TABLE `reported_items` (
  `id_item` int(11) NOT NULL,
  `Fname` varchar(255) NOT NULL,
  `Lname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_no` varchar(100) NOT NULL,
  `dept_college` varchar(255) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_category` varchar(255) NOT NULL,
  `location_found` varchar(50) NOT NULL,
  `report_date` date NOT NULL,
  `report_time` time(6) NOT NULL,
  `other_details` varchar(500) NOT NULL,
  `img1` varchar(255) DEFAULT NULL,
  `img2` varchar(255) DEFAULT NULL,
  `img3` varchar(255) DEFAULT NULL,
  `img4` varchar(255) DEFAULT NULL,
  `img5` varchar(255) DEFAULT NULL,
  `status` varchar(10) NOT NULL,
  `remark` varchar(10) NOT NULL,
  `validID` varchar(255) NOT NULL,
  `claim_desc` varchar(255) NOT NULL,
  `claim_Fname` varchar(255) NOT NULL,
  `claim_Lname` varchar(255) NOT NULL,
  `claim_email` varchar(255) NOT NULL,
  `claim_contact` varchar(255) NOT NULL,
  `claim_dept` varchar(255) NOT NULL,
  `claim_date` date DEFAULT NULL,
  `claim_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reported_items`
--

INSERT INTO `reported_items` (`id_item`, `Fname`, `Lname`, `email`, `contact_no`, `dept_college`, `item_name`, `item_category`, `location_found`, `report_date`, `report_time`, `other_details`, `img1`, `img2`, `img3`, `img4`, `img5`, `status`, `remark`, `validID`, `claim_desc`, `claim_Fname`, `claim_Lname`, `claim_email`, `claim_contact`, `claim_dept`, `claim_date`, `claim_time`) VALUES
(1, 'joshua', 'penuela', 'joshua@cvsu.edu.ph', '0964489506', 'CEIT', 'iPhone 11', 'Phone', 'CEIT', '2024-08-14', '15:39:00.000000', 'Space Gray', '', '', '', '', '', 'Unclaimed', 'Approved', '', '', '', '', '', '', '', NULL, NULL),
(2, 'Sharon', 'Agor', 'sharon@cvsu.edu.ph', '09123456789', 'CON', 'iPhone 11', 'Phone', 'CON Building', '2024-08-14', '15:43:00.000000', '1TB, Pinkish Color', '', '', '', '', '', 'Unclaimed', 'Approved', '', '', '', '', '', '', '', NULL, NULL),
(4, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09123456789', 'CEIT', 'Mikha Photocard', 'Accessories', 'DIT Bldg.', '2024-08-26', '16:10:00.000000', 'From Jollibee', NULL, NULL, NULL, NULL, NULL, 'Claimed', 'Approved', '', 'Bough from Jollibee', 'Clark Angelo', 'Mendoza', 'clarkangelo.mendoza@cvsu.edu.ph', '09123456789', '', '2024-09-18', '08:30:00'),
(5, 'Lyzette', 'Dominguez', 'lyzette.dominguez@cvsu.edu.ph', '09123456789', 'CEIT', 'MacBook Pro', 'Gadgets/Electronics', 'Oval', '2024-08-26', '20:56:00.000000', 'White Color, Anime Stickers', '', '', '', '', '', 'Claimed', 'Approved', '', 'Anime Lockscreen', 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09123456789', '', '2024-09-17', '09:30:00'),
(6, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09123456789', 'CAFENR', 'Birth Cert', 'Documents', 'Admin Bldg.', '2024-08-27', '03:23:00.000000', 'PSA Birth Cert', '', '', '', '', '', 'Claimed', 'Approved', '', '2003 Birth year', 'Juan', 'Dela Cruz', 'juan.delacruz@cvsu.edu.ph', '09132465789', '', '2024-09-16', '10:30:00'),
(7, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09123456789', 'CAFENR', 'Birth Cert', 'Documents', 'Admin Bldg.', '2024-08-27', '03:23:00.000000', 'PSA Birth Cert', '', '', '', '', '', 'Unclaimed', 'Approved', '', '', '', '', '', '', '', NULL, NULL),
(8, 'Juan', 'Dela Cruz', 'juan.delacruz@cvsu.edu.ph', '09123456789', 'CAS', 'School ID', 'ID', '7/11 U-Mall', '2024-08-27', '09:52:00.000000', 'School ID with StudentNo. 202112345', '', '', '', '', '', 'Unclaimed', 'Approved', '', '', '', '', '', '', '', NULL, NULL),
(9, 'Maria', 'Clara', 'maria.clara@cvsu.edu.ph', '09123456789', 'CAFENR', 'HP Laptop', 'Gadgets/Electronics', 'CEMDS bldg.', '2024-08-27', '23:14:00.000000', 'Black Color', '', '', '', '', '', 'Unclaimed', 'Approved', '', '', '', '', '', '', '', NULL, NULL),
(10, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123456789', 'CEIT', 'PE T-Shirt', 'Clothes', 'Saluysoy', '2024-08-28', '12:03:00.000000', 'Medium Size', '', '', '', '', '', 'Claimed', 'Approved', '', '', '', '', '', '', '', NULL, NULL),
(11, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123456789', 'CEIT', 'PE T-Shirt', 'Clothes', 'Saluysoy', '2024-08-28', '12:03:00.000000', 'Medium Size', '', '', '', '', '', 'Unclaimed', 'Reject', '', '', '', '', '', '', '', NULL, NULL),
(13, 'Clark', 'Mendoza', 'clarkmendoza@cvsu.edu.ph', '09123456789', 'CEIT', 'PSP 5', 'Gadgets/Electronics', 'CAS Bldg', '2024-08-28', '15:29:00.000000', '1TB, White Color', '', '', '', '', '', 'Unclaimed', 'Approved', '', '', '', '', '', '', '', NULL, NULL),
(14, 'Jose', 'Rizal', 'jose.rizal@cvsu.edu.ph', '0912465798', 'CEIT', 'Binder', 'School Supplies', 'Oval', '2024-08-29', '11:06:00.000000', 'Color Black, Long size', '', '', '', '', '', 'Unclaimed', 'Approved', '', '', '', '', '', '', '', NULL, NULL),
(15, 'Steve', 'Jobs', 'steve.jobs@apple.com', '09123456789', 'CEIT', 'iPad', 'Gadgets/Electronics', 'Admin', '2024-09-02', '12:48:00.000000', 'iPad Pro', '', '', '', '', '', 'Unclaimed', 'Approved', '', '', '', '', '', '', '', NULL, NULL),
(16, 'Bill', 'Gates', 'bill.gates@microsoft.com', '09123465789', 'CEIT', 'Samsung Tablet', 'Gadgets/Electronics', 'U-Mall', '2024-09-02', '04:20:00.000000', 'Color Black', '', '', '', '', '', 'Unclaimed', 'Approved', '', '', '', '', '', '', '', NULL, NULL),
(17, 'Mark', 'Zuckerber', 'mark@facebook.com', '0912345789', 'CEIT', 'Samsung S24', 'Gadgets/Electronics', 'New CEMDS', '2024-09-02', '13:24:00.000000', 'Gray Color', NULL, NULL, NULL, NULL, NULL, 'Unclaimed', 'Approved', '', '', '', '', '', '', '', NULL, NULL),
(18, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09123456789', 'CEIT', 'iPod', 'Gadgets/Electronics', 'Gate 1', '2024-09-07', '14:44:00.000000', 'iPad Nano', NULL, NULL, NULL, NULL, NULL, 'Claimed', 'Approved', '', '', '', '', '', '', '', NULL, NULL),
(19, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09123456789', 'CED', 'TestItem', 'Accessories', 'ICON', '2024-09-07', '15:15:00.000000', 'ASDsadsa', NULL, NULL, NULL, NULL, NULL, 'Unclaimed', 'Approved', '', '', '', '', '', '', '', NULL, NULL),
(20, 'Ichigo', 'Kurosaki', 'kurosaki@gmail.com', '09123456789', 'CEIT', 'Aiah Photocard', 'Accessories', 'CVMBS', '2024-09-14', '08:04:00.000000', 'BIni Aiah Photocard Jollibee', NULL, NULL, NULL, NULL, NULL, 'Unclaimed', 'Approved', '', '', '', '', '', '', '', NULL, NULL),
(21, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123465789', 'CEIT', 'Brief', 'Clothes', 'DIT CR', '2024-09-14', '10:32:00.000000', 'Medium Size, Black Color', NULL, NULL, NULL, NULL, NULL, 'Claimed', 'Approved', '', 'Medium Size', 'Joshua', 'Penuela', 'joshuapenuela12@gmail.com', '09132456798', '', '2024-09-15', '13:45:00'),
(22, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '0912346798', 'CEIT', 'iPhone 15 Pro', 'Gadgets/Electronics', 'DIT Bldg.', '2024-10-08', '22:27:00.000000', '512GB', '/9j/4AAQSkZJRgABAQAAAQABAAD/4gIoSUNDX1BST0ZJTEUAAQEAAAIYAAAAAAIQAABtbnRyUkdC\nIFhZWiAAAAAAAAAAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAA\nAADTLQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlk\nZXNjAAAA8AAAAHRyWFlaAAAB', '/9j/4AAQSkZJRgABAQAAAQABAAD/4gIoSUNDX1BST0ZJTEUAAQEAAAIYAAAAAAIQAABtbnRyUkdC\nIFhZWiAAAAAAAAAAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAA\nAADTLQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlk\nZXNjAAAA8AAAAHRyWFlaAAAB', '/9j/4AAQSkZJRgABAQAAAQABAAD/4gIoSUNDX1BST0ZJTEUAAQEAAAIYAAAAAAIQAABtbnRyUkdC\nIFhZWiAAAAAAAAAAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAA\nAADTLQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlk\nZXNjAAAA8AAAAHRyWFlaAAAB', NULL, NULL, 'Unclaimed', 'Approved', '', '', '', '', '', '', '', NULL, NULL),
(23, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '0912346798', 'CEIT', 'iPhone 15 Pro', 'Gadgets/Electronics', 'DIT Bldg.', '2024-10-08', '22:27:00.000000', '512GB', '/9j/4AAQSkZJRgABAQAAAQABAAD/4gIoSUNDX1BST0ZJTEUAAQEAAAIYAAAAAAIQAABtbnRyUkdC\nIFhZWiAAAAAAAAAAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAA\nAADTLQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlk\nZXNjAAAA8AAAAHRyWFlaAAAB', '/9j/4AAQSkZJRgABAQAAAQABAAD/4gIoSUNDX1BST0ZJTEUAAQEAAAIYAAAAAAIQAABtbnRyUkdC\nIFhZWiAAAAAAAAAAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAA\nAADTLQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlk\nZXNjAAAA8AAAAHRyWFlaAAAB', '/9j/4AAQSkZJRgABAQAAAQABAAD/4gIoSUNDX1BST0ZJTEUAAQEAAAIYAAAAAAIQAABtbnRyUkdC\nIFhZWiAAAAAAAAAAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAA\nAADTLQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlk\nZXNjAAAA8AAAAHRyWFlaAAAB', NULL, NULL, 'Unclaimed', 'Unapproved', '', '', '', '', '', '', '', NULL, NULL),
(24, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09123456798', 'CEIT', 'Redmi 9A', 'Gadgets/Electronics', 'Hallway', '2024-10-09', '22:57:00.000000', 'Low Qual Phone', '/9j/4AAQSkZJRgABAQAAAQABAAD/4gIoSUNDX1BST0ZJTEUAAQEAAAIYAAAAAAIQAABtbnRyUkdC\nIFhZWiAAAAAAAAAAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAA\nAADTLQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlk\nZXNjAAAA8AAAAHRyWFlaAAAB', '/9j/4AAQSkZJRgABAQAAAQABAAD/4gIoSUNDX1BST0ZJTEUAAQEAAAIYAAAAAAIQAABtbnRyUkdC\nIFhZWiAAAAAAAAAAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAA\nAADTLQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlk\nZXNjAAAA8AAAAHRyWFlaAAAB', '/9j/4AAQSkZJRgABAQAAAQABAAD/4gIoSUNDX1BST0ZJTEUAAQEAAAIYAAAAAAIQAABtbnRyUkdC\nIFhZWiAAAAAAAAAAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAA\nAADTLQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlk\nZXNjAAAA8AAAAHRyWFlaAAAB', NULL, NULL, 'Unclaimed', 'Approved', '', '', '', '', '', '', '', NULL, NULL),
(25, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09123456789', 'CEIT', 'Lapis', 'School Supplies', 'CON', '2024-10-10', '12:00:00.000000', 'Mahaba', '', '', '', '', '', 'Claimed', 'Approved', '', '', '', '', '', '', '', NULL, NULL),
(26, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09123456789', 'CEIT', 'Oppo 69', 'Gadgets/Electronics', 'ICON', '2024-10-10', '13:57:00.000000', '420', '67076d4dd1d00_img1.jpg', '67076d4dd20cc_img2.jpg', '67076d4dd2211_img3.jpg', '', '', 'Unclaimed', 'Approved', '', '', '', '', '', '', '', NULL, NULL),
(27, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09213456789', 'CEIT', 'Samsung 69', 'Gadgets/Electronics', 'CR', '2024-10-10', '14:15:00.000000', '1TB', '670771ae5f13f_img1.jpg', '670771ae5fcf6_img2.jpg', '670771ae601a2_img3.jpg', '', '', 'Claimed', 'Approved', '', '', '', '', '', '', '', NULL, NULL),
(28, 'Chai', 'Liao', 'chaiyin.liao@cvsu.edu.ph', '09123456789', 'CEIT', 'iMac', 'Gadgets/Electronics', 'New CEMDS', '2024-10-11', '15:29:00.000000', '1TB, 16GB RAM', '6708d553e7656_img1.jpg', '6708d553e8b5b_img2.jpg', '6708d553e93ee_img3.jpg', '', '', 'Claimed', 'Approved', '', '1TB, 16GB RAM', 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.com', '09132456789', '', '2024-10-12', '10:00:00'),
(29, 'Lester', 'Araneta', 'lester.araneta@cvsu.edu.ph', '09123465789', 'CEIT', 'Aquaflask', 'Tumbler', 'DIT Bldg.', '2024-10-03', '15:49:00.000000', '500ml', '6708da1a2887d_img1.jpg', '6708da1a29427_img2.jpg', '6708da1a297cd_img3.jpg', '6708da1a29b22_img4.jpg', '', 'Claimed', 'Approved', '670b3a7fd9fca.jpg', 'Testing langs', 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123456789', 'CEIT', '2024-10-13', '05:12:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `Lname` varchar(255) NOT NULL,
  `Fname` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `dept` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL DEFAULT '0',
  `contactNo` varchar(100) NOT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `reset_pass_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `Lname`, `Fname`, `email`, `dept`, `password`, `contactNo`, `otp`, `reset_pass_time`, `reset_token`) VALUES
(1, 'Penuela', 'Josh', 'joshua.penuela@cvsu.edu.ph', 'CEIT', '$2y$10$wAX1/3Ot1v1LfWQqLoxEteA.s0/ClKBaQnd.2VGjxVY6poc7Ac1v.', '09123456789', NULL, '2024-11-09 05:50:19', NULL),
(2, 'Mendoza', 'Clark Angelo', 'clarkangelo.mendoza@cvsu.edu.ph', 'CEIT', '$2y$10$Hleh4OhhjocGBQn6Guypo.qVyh9p8udJV10XGVDEVC9YcC2h.v1iG', '09123456789', NULL, '2024-11-09 05:50:19', NULL),
(3, 'Llano', 'Alexander', 'alexander.llano@cvsu.edu.ph', 'CEIT', '$2y$10$dSdz6TikGIeP93RUi5JJCun8s/iNG0FD6EsOyo3NTSnmtCQsuYwYy', '09123456789', NULL, '2024-11-09 05:50:19', NULL),
(4, 'Agor', 'Sharon', 'sharon.agor@cvsu.edu.ph', 'CON', '$2y$10$GQJj0msKOEX78suc0e4/teH1lsOxf9PqDpkaJlRODlLYioZncsURu', '09123456789', NULL, '2024-11-09 05:50:19', NULL),
(5, 'Penuela', 'Joshua', 'joshuapenuela12@gmail.com', 'CEIT', '$2y$10$J2UoVqDfYsMOlziVt/wXGek8kDIFGD4bkiD1k5evE3vNAfIfhpGni', '09622849506', NULL, '2024-11-09 05:50:19', NULL),
(6, 'Penuela', 'Joshua', 'joshuapenuela@gmail.com', 'CEIT', '$2y$10$/VU6hkjxAHI.40.QHM/NiuiLpHb.WF1tcQhlt4sO.jJYNKMhiRJCa', '09123456789', NULL, '2024-11-09 05:50:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_message`
--

CREATE TABLE `user_message` (
  `id` int(10) NOT NULL,
  `sender_id` int(10) NOT NULL,
  `receiver_id` int(10) NOT NULL,
  `message` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `media_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_message`
--
ALTER TABLE `admin_message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `colleges`
--
ALTER TABLE `colleges`
  ADD PRIMARY KEY (`college_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reported_items`
--
ALTER TABLE `reported_items`
  ADD PRIMARY KEY (`id_item`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_message`
--
ALTER TABLE `user_message`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `admin_message`
--
ALTER TABLE `admin_message`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `colleges`
--
ALTER TABLE `colleges`
  MODIFY `college_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reported_items`
--
ALTER TABLE `reported_items`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_message`
--
ALTER TABLE `user_message`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
