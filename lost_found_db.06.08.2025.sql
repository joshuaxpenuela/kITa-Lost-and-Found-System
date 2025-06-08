-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2025 at 01:49 PM
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
(4, 'josh', 'JOSHUA', 'PENUELA', '1a1dc91c907325c69271ddf0c944bc72', 'uploads/profile_images/admin_4_1744830871.jpg', 'enable', 'security_moderator'),
(7, 'admin1', 'Romulo', 'Gomez', '5f4dcc3b5aa765d61d8327deb882cf99', '', 'enable', 'security_moderator');

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `log_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `action`, `details`, `log_date`) VALUES
(1, 0, 'Claim Approval', 'Approved claim for item ID 3', '2024-12-11 13:18:25'),
(2, 0, 'Claim Approval', 'Approved claim for item ID 2', '2024-12-11 13:20:29'),
(3, 0, 'Claim Approval', 'Approved claim for item ID 11', '2025-01-05 14:27:12'),
(4, 0, 'Claim Approval', 'Approved claim for item ID 17', '2025-01-07 16:02:27'),
(5, 0, 'Claim Approval', 'Approved claim for item ID 7', '2025-01-08 09:57:48'),
(6, 0, 'Claim Approval', 'Approved claim for item ID 5', '2025-01-08 17:41:26'),
(7, 0, 'Claim Approval', 'Approved claim for item ID 9', '2025-01-08 17:42:45'),
(8, 0, 'Claim Approval', 'Approved claim for item ID 20', '2025-01-14 12:36:56');

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
  `media_url` varchar(255) NOT NULL,
  `view_status` tinyint(1) DEFAULT 0 COMMENT '0=unread, 1=read'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_message`
--

INSERT INTO `admin_message` (`id`, `sender_id`, `receiver_id`, `admin_id`, `user_id`, `message`, `created_at`, `media_url`, `view_status`) VALUES
(1, 0, 1, 2, 1, 'We have received your attempt to claim an item. You can now claim it at Gate 2 lost and found depository.', '2024-12-11 06:07:40', '', 0),
(6, 1, 1, 0, 1, 'Thank you po!', '2024-12-11 06:13:11', '', 0),
(7, 1, 2, 0, 1, 'Thank you po!', '2024-12-11 06:13:11', '', 0),
(8, 1, 3, 0, 1, 'Thank you po!', '2024-12-11 06:13:11', '', 0),
(9, 1, 4, 0, 1, 'Thank you po!', '2024-12-11 06:13:11', '', 0),
(11, 0, 1, 2, 1, 'Your\'re Welcome!', '2024-12-11 06:13:33', '', 0),
(12, 1, 1, 0, 1, 'hi', '2025-01-05 15:48:13', '', 0),
(13, 1, 2, 0, 1, 'hi', '2025-01-05 15:48:13', '', 0),
(14, 1, 3, 0, 1, 'hi', '2025-01-05 15:48:13', '', 0),
(15, 1, 4, 0, 1, 'hi', '2025-01-05 15:48:13', '', 0),
(16, 0, 1, 2, 1, 'hello', '2025-01-05 23:48:55', '', 0),
(17, 0, 1, 2, 1, 'how can i help you', '2025-01-05 23:49:13', '', 0),
(18, 0, 3, 4, 3, 'yow', '2025-01-06 03:59:22', '', 0),
(19, 2, 1, 0, 2, 'Hello!', '2025-01-06 05:48:59', '', 0),
(20, 2, 2, 0, 2, 'Hello!', '2025-01-06 05:48:59', '', 0),
(21, 2, 3, 0, 2, 'Hello!', '2025-01-06 05:48:59', '', 0),
(22, 2, 4, 0, 2, 'Hello!', '2025-01-06 05:48:59', '', 0),
(23, 0, 2, 4, 2, 'Good afternoon, sir', '2025-01-06 05:49:17', '', 0),
(24, 2, 1, 0, 2, 'Sir, nakita ko po ung laptop nasa inyo na Po ba?', '2025-01-07 00:01:06', '', 0),
(25, 2, 2, 0, 2, 'Sir, nakita ko po ung laptop nasa inyo na Po ba?', '2025-01-07 00:01:06', '', 0),
(26, 2, 3, 0, 2, 'Sir, nakita ko po ung laptop nasa inyo na Po ba?', '2025-01-07 00:01:06', '', 0),
(27, 2, 4, 0, 2, 'Sir, nakita ko po ung laptop nasa inyo na Po ba?', '2025-01-07 00:01:06', '', 0),
(28, 0, 2, 2, 2, 'Yes po sir, nasa amin na po', '2025-01-07 08:01:54', '', 0),
(29, 0, 2, 2, 2, 'Paki claim nalang po dito sa gate 2', '2025-01-07 08:02:08', '', 0),
(30, 2, 1, 0, 2, 'good morning po', '2025-01-07 17:58:15', '', 0),
(31, 2, 2, 0, 2, 'good morning po', '2025-01-07 17:58:15', '', 0),
(32, 2, 3, 0, 2, 'good morning po', '2025-01-07 17:58:15', '', 0),
(33, 2, 4, 0, 2, 'good morning po', '2025-01-07 17:58:15', '', 0),
(34, 0, 2, 4, 2, 'good morning sir', '2025-01-08 01:58:30', '', 0),
(35, 3, 1, 0, 3, 'Do you already have my charger po?', '2025-01-14 04:34:06', '', 0),
(36, 3, 2, 0, 3, 'Do you already have my charger po?', '2025-01-14 04:34:06', '', 0),
(37, 3, 3, 0, 3, 'Do you already have my charger po?', '2025-01-14 04:34:06', '', 0),
(38, 3, 4, 0, 3, 'Do you already have my charger po?', '2025-01-14 04:34:06', '', 0),
(39, 3, 7, 0, 3, 'Do you already have my charger po?', '2025-01-14 04:34:06', '', 0),
(40, 0, 3, 4, 3, 'Yes, sir. paki claim na lang po dito sa may Gate 2. bring your id na inupload niyo po and proof of ownership din po. thanks', '2025-01-14 04:34:52', '', 0),
(41, 3, 1, 0, 3, 'Thank you po for bringing back my lost charger!', '2025-01-14 04:37:43', '', 0),
(42, 3, 2, 0, 3, 'Thank you po for bringing back my lost charger!', '2025-01-14 04:37:43', '', 0),
(43, 3, 3, 0, 3, 'Thank you po for bringing back my lost charger!', '2025-01-14 04:37:43', '', 0),
(44, 3, 4, 0, 3, 'Thank you po for bringing back my lost charger!', '2025-01-14 04:37:43', '', 0),
(45, 3, 7, 0, 3, 'Thank you po for bringing back my lost charger!', '2025-01-14 04:37:43', '', 0),
(46, 3, 1, 0, 3, 'Hi, sir! Thesis defended na po kami!! thank you so much po for everything!', '2025-01-17 03:31:45', '', 0),
(47, 3, 2, 0, 3, 'Hi, sir! Thesis defended na po kami!! thank you so much po for everything!', '2025-01-17 03:31:45', '', 0),
(48, 3, 3, 0, 3, 'Hi, sir! Thesis defended na po kami!! thank you so much po for everything!', '2025-01-17 03:31:45', '', 0),
(49, 3, 4, 0, 3, 'Hi, sir! Thesis defended na po kami!! thank you so much po for everything!', '2025-01-17 03:31:45', '', 0),
(50, 3, 7, 0, 3, 'Hi, sir! Thesis defended na po kami!! thank you so much po for everything!', '2025-01-17 03:31:45', '', 0),
(51, 0, 3, 4, 3, 'Naks! Congrats, sir! Job well done po!', '2025-01-17 03:32:04', '', 0),
(52, 3, 1, 0, 3, 'Good morning, sir. testing lang po hihi', '2025-02-23 06:07:39', '', 0),
(53, 3, 2, 0, 3, 'Good morning, sir. testing lang po hihi', '2025-02-23 06:07:39', '', 0),
(54, 3, 3, 0, 3, 'Good morning, sir. testing lang po hihi', '2025-02-23 06:07:39', '', 0),
(55, 3, 4, 0, 3, 'Good morning, sir. testing lang po hihi', '2025-02-23 06:07:39', '', 0),
(56, 3, 7, 0, 3, 'Good morning, sir. testing lang po hihi', '2025-02-23 06:07:39', '', 0),
(57, 0, 3, 1, 3, 'Hi po, kumusta ka na?', '2025-02-23 06:07:52', '', 0),
(58, 3, 1, 0, 3, 'Doing good naman, sir sa OJT po namin hihi', '2025-02-23 06:08:21', '', 0),
(59, 3, 2, 0, 3, 'Doing good naman, sir sa OJT po namin hihi', '2025-02-23 06:08:21', '', 0),
(60, 3, 3, 0, 3, 'Doing good naman, sir sa OJT po namin hihi', '2025-02-23 06:08:21', '', 0),
(61, 3, 4, 0, 3, 'Doing good naman, sir sa OJT po namin hihi', '2025-02-23 06:08:21', '', 0),
(62, 3, 7, 0, 3, 'Doing good naman, sir sa OJT po namin hihi', '2025-02-23 06:08:21', '', 0),
(63, 0, 3, 1, 3, 'naks wish you the best!', '2025-02-23 06:08:31', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `claim_reports`
--

CREATE TABLE `claim_reports` (
  `id_claim` int(10) NOT NULL,
  `id_item` int(10) NOT NULL,
  `Fname` varchar(255) NOT NULL,
  `Lname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_no` varchar(100) NOT NULL,
  `dept_college` varchar(255) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_category` varchar(255) NOT NULL,
  `location_found` varchar(255) NOT NULL,
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
  `ProofOwner` varchar(255) NOT NULL,
  `claim_desc` varchar(255) NOT NULL,
  `claim_Fname` varchar(255) NOT NULL,
  `claim_Lname` varchar(255) NOT NULL,
  `claim_email` varchar(255) NOT NULL,
  `claim_contact` varchar(255) NOT NULL,
  `claim_dept` varchar(255) NOT NULL,
  `claim_date` date NOT NULL,
  `claim_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `claim_reports`
--

INSERT INTO `claim_reports` (`id_claim`, `id_item`, `Fname`, `Lname`, `email`, `contact_no`, `dept_college`, `item_name`, `item_category`, `location_found`, `report_date`, `report_time`, `other_details`, `img1`, `img2`, `img3`, `img4`, `img5`, `status`, `remark`, `validID`, `ProofOwner`, `claim_desc`, `claim_Fname`, `claim_Lname`, `claim_email`, `claim_contact`, `claim_dept`, `claim_date`, `claim_time`) VALUES
(1, 7, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123123131', 'CEIT', 'wallet', 'Wallet', 'CAFENR', '2024-12-10', '14:53:00.000000', 'wallet black', '675801cb56d51_img1.jpg', '', '', '', '', 'Claimed', 'Approved', '67583c3731ce7_validID.jpg', '67583c3731cea_proofOwner.jpg', 'akin po yan', 'Clark Angelo', 'Mendoza', 'clarkangelo.mendoza@cvsu.edu.ph', '09123456789', 'CEIT', '2024-12-10', '14:03:51'),
(2, 5, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09123456789', 'CEIT', 'Pencil', 'School Supplies', 'CEMDS', '2024-12-09', '11:50:00.000000', 'pencil na yellow', '67580108b9e67_img1.jpg', '', '', '', '', 'Unclaimed', 'Denied', '67583c68bd743_validID.jpg', '67583c68bd74a_proofOwner.jpg', 'pencil ko Po yan', 'Clark Angelo', 'Mendoza', 'clarkangelo.mendoza@cvsu.edu.ph', '09123456789', 'CEIT', '2024-12-10', '14:04:40'),
(3, 2, 'Maria', 'Mercedes', 'maria@mercedes.com', '09115151515', 'CON', 'Aquaflask', 'Tumbler', 'CSPEAR Gym', '2024-12-02', '09:42:00.000000', 'Aquaflask na dilaw', '6757ffa3bbcfd_img1.jpg', '', '', '', '', 'Claimed', 'Approved', '67583c87d55cb_validID.jpg', '67583c87d55ce_proofOwner.jpg', 'may pangalan Po sa ilalim', 'Clark Angelo', 'Mendoza', 'clarkangelo.mendoza@cvsu.edu.ph', '09123456789', 'CEIT', '2024-12-10', '14:05:11'),
(4, 3, 'Maria', 'Mercedes', 'maria@mercedes.com', '09151515151', 'CON', 'iPhone', 'Gadgets/Electronics', 'Library', '2024-12-08', '10:46:00.000000', 'iPhone na square', '67580028a9dde_img1.jpg', '', '', '', '', 'Unclaimed', 'Denied', '67583ca400b4e_validID.jpg', '67583ca400b52_proofOwner.jpg', 'eto Po apple', 'Clark Angelo', 'Mendoza', 'clarkangelo.mendoza@cvsu.edu.ph', '09123456789', 'CEIT', '2024-12-10', '14:05:40'),
(5, 3, 'Maria', 'Mercedes', 'maria@mercedes.com', '09151515151', 'CON', 'iPhone', 'Gadgets/Electronics', 'Library', '2024-12-08', '10:46:00.000000', 'iPhone na square', '67580028a9dde_img1.jpg', '', '', '', '', 'Claimed', 'Approved', '67583cee417a9_validID.jpg', '67583cee417ac_proofOwner.jpg', 'Meron Po Yang photocard sa likod', 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09622849505', 'CEIT', '2024-12-10', '14:06:54'),
(6, 9, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123131455', 'CEIT', 'Uniform', 'Clothes', 'CED', '2024-12-01', '10:56:00.000000', 'uniform polo', '675802791b124_img1.jpg', '', '', '', '', 'Claimed', 'Approved', '67583d0895071_validID.jpg', '67583d0895075_proofOwner.jpg', 'may name Po sya sa baba', 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09622849505', 'CEIT', '2024-12-10', '14:07:20'),
(7, 3, '', '', '', '', '', 'iPhone', 'Gadgets/Electronics', 'Library', '0000-00-00', '00:00:00.000000', 'Meron Po Yang photocard sa likod', NULL, NULL, NULL, NULL, NULL, '', '', '67583cee417a9_validID.jpg', '67583cee417ac_proofOwner.jpg', '', 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09622849505', 'CEIT', '2024-12-11', '00:00:00'),
(8, 2, '', '', '', '', '', 'Aquaflask', 'Tumbler', 'CSPEAR Gym', '0000-00-00', '00:00:00.000000', 'may pangalan Po sa ilalim', NULL, NULL, NULL, NULL, NULL, '', '', '67583c87d55cb_validID.jpg', '67583c87d55ce_proofOwner.jpg', '', 'Clark Angelo', 'Mendoza', 'clarkangelo.mendoza@cvsu.edu.ph', '09123456789', 'CEIT', '2024-12-11', '00:00:00'),
(9, 5, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09123456789', 'CEIT', 'Pencil', 'School Supplies', 'CEMDS', '2024-12-09', '11:50:00.000000', 'pencil na yellow', '67580108b9e67_img1.jpg', '', '', '', '', 'Claimed', 'Approved', '6777834c7a720_validID.jpg', '6777834c7a724_proofOwner.jpg', 'aken', 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09622849505', 'CEIT', '2025-01-03', '07:27:24'),
(10, 11, 'Clark', 'mendoza', 'clarkangelo.mendoza@cvsu.edu.ph', '09164713453', 'CEIT', 'wallet na black', 'Wallet', 'Oval', '2025-01-05', '01:14:00.000000', 'black', '677a23e833c08_img1.jpg', '677a23e833e69_img2.jpg', '', '', '', 'Claimed', 'Approved', '677a25dee75dc_validID.jpg', '677a25dee75e3_proofOwner.jpg', '64654', 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09622849505', 'CEIT', '2025-01-05', '07:25:34'),
(11, 11, '', '', '', '', '', 'wallet na black', 'Wallet', 'Oval', '0000-00-00', '00:00:00.000000', '64654', NULL, NULL, NULL, NULL, NULL, '', '', '677a25dee75dc_validID.jpg', '677a25dee75e3_proofOwner.jpg', '', 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09622849505', 'CEIT', '2025-01-05', '00:00:00'),
(12, 16, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09458955569', 'CEIT', 'Bostanten Wallet', 'Wallet', 'Library', '2025-01-06', '08:30:00.000000', 'It\\\'s a Black Bostanten wallet with cash inside and some identification.', '677b6dfa61402_img1.jpg', '', '', '', '', 'Unclaimed', 'Pending', '677b6eb80949a_validID.jpg', '677b6eb80949d_proofOwner.jpg', 'That\'s my wallet, I lost it in the library.', 'Clark Angelo', 'Mendoza', 'clarkangelo.mendoza@cvsu.edu.ph', '09123456789', 'CEIT', '2025-01-06', '06:48:40'),
(13, 16, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09458955569', 'CEIT', 'Bostanten Wallet', 'Wallet', 'Library', '2025-01-06', '08:30:00.000000', 'It\\\'s a Black Bostanten wallet with cash inside and some identification.', '677b6dfa61402_img1.jpg', '', '', '', '', 'Unclaimed', 'Pending', '677b8bdf02e29_validID.jpg', '677b8bdf02e2c_proofOwner.jpg', 'That\'s wallet is mine!', 'Hanzel', 'Ramirez', 'hanzel.ramirez@cvsu.edu.ph', '09151586440', 'CEIT', '2025-01-06', '08:53:03'),
(14, 16, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09458955569', 'CEIT', 'Bostanten Wallet', 'Wallet', 'Library', '2025-01-06', '08:30:00.000000', 'It\\\'s a Black Bostanten wallet with cash inside and some identification.', '677b6dfa61402_img1.jpg', '', '', '', '', 'Unclaimed', 'Pending', '677b96f1c36fc_validID.jpg', '677b96f1c3700_proofOwner.jpg', 'akin po yang wallet check nio po ung loob', 'Clark Angelo', 'Mendoza', 'clarkangelo.mendoza@cvsu.edu.ph', '09123456789', 'CEIT', '2025-01-06', '09:40:17'),
(15, 16, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09458955569', 'CEIT', 'Bostanten Wallet', 'Wallet', 'Library', '2025-01-06', '08:30:00.000000', 'It\\\'s a Black Bostanten wallet with cash inside and some identification.', '677b6dfa61402_img1.jpg', '', '', '', '', 'Unclaimed', 'Pending', '677b9707f0d41_validID.jpg', '677b9707f0d45_proofOwner.jpg', 'akin po yang wallet check nio po ung loob', 'Clark Angelo', 'Mendoza', 'clarkangelo.mendoza@cvsu.edu.ph', '09123456789', 'CEIT', '2025-01-06', '09:40:39'),
(16, 17, 'Jake', 'Ersando', 'jake.ersando@cvsu.edu.ph', '09164713453', 'CEIT', 'Dell Laptop', 'Gadgets/Electronics', 'CCL102 DIT Building', '2025-01-07', '14:55:00.000000', 'Black laptop, Dell brand', '677cde687f343_img1.jpg', '677cde687f555_img2.jpg', '', '', '', 'Claimed', 'Approved', '677cdf1fbd8b4_validID.jpg', '677cdf1fbd8b8_proofOwner.jpg', '512gb SSD', 'Clark Angelo', 'Mendoza', 'clarkangelo.mendoza@cvsu.edu.ph', '09123456789', 'CEIT', '2025-01-07', '09:00:31'),
(17, 17, '', '', '', '', '', 'Dell Laptop', 'Gadgets/Electronics', 'CCL102 DIT Building', '0000-00-00', '00:00:00.000000', '512gb SSD', NULL, NULL, NULL, NULL, NULL, '', '', '677cdf1fbd8b4_validID.jpg', '677cdf1fbd8b8_proofOwner.jpg', '', 'Clark Angelo', 'Mendoza', 'clarkangelo.mendoza@cvsu.edu.ph', '09123456789', 'CEIT', '2025-01-07', '00:00:00'),
(18, 7, '', '', '', '', '', 'wallet', 'Wallet', 'CAFENR', '0000-00-00', '00:00:00.000000', 'akin po yan', NULL, NULL, NULL, NULL, NULL, '', '', '67583c3731ce7_validID.jpg', '67583c3731cea_proofOwner.jpg', '', 'Clark Angelo', 'Mendoza', 'clarkangelo.mendoza@cvsu.edu.ph', '09123456789', 'CEIT', '2025-01-08', '00:00:00'),
(19, 5, '', '', '', '', '', 'Pencil', 'School Supplies', 'CEMDS', '0000-00-00', '00:00:00.000000', 'aken', NULL, NULL, NULL, NULL, NULL, '', '', '6777834c7a720_validID.jpg', '6777834c7a724_proofOwner.jpg', '', 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09622849505', 'CEIT', '2025-01-08', '00:00:00'),
(20, 9, '', '', '', '', '', 'Uniform', 'Clothes', 'CED', '0000-00-00', '00:00:00.000000', 'may name Po sya sa baba', NULL, NULL, NULL, NULL, NULL, '', '', '67583d0895071_validID.jpg', '67583d0895075_proofOwner.jpg', '', 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09622849505', 'CEIT', '2025-01-08', '00:00:00'),
(21, 20, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123456789', 'CEIT', 'Charger', 'Gadgets/Electronics', 'DIT bldg. 2nd floor', '2025-01-14', '09:00:00.000000', 'Has a blue sticker on it.', '6785e8e50e33d_img1.jpg', '', '', '', '', 'Claimed', 'Approved', '6785e9cdf05a6_validID.jpg', '6785e9cdf05ac_proofOwner.jpg', 'It has a cover for the wire po na black and white and then may blue sticker na nakadikit po.', 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123456789', 'CEIT', '2025-01-14', '05:36:29'),
(22, 20, '', '', '', '', '', 'Charger', 'Gadgets/Electronics', 'DIT bldg. 2nd floor', '0000-00-00', '00:00:00.000000', 'It has a cover for the wire po na black and white and then may blue sticker na nakadikit po.', NULL, NULL, NULL, NULL, NULL, '', '', '6785e9cdf05a6_validID.jpg', '6785e9cdf05ac_proofOwner.jpg', '', 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123456789', 'CEIT', '2025-01-14', '00:00:00'),
(23, 21, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09622849505', 'CEIT', 'Grad Pic Price List', 'Documents', 'CEMDS bldg.', '2025-01-14', '22:09:00.000000', 'Grad pics price list with packages', '6786707b54f3f_img1.jpg', '', '', '', '', 'Unclaimed', 'Pending', '6786710469d44_validID.jpg', '6786710469d49_proofOwner.jpg', 'the ranges of prices of the packages are around 3k pesos', 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09622849505', 'CEIT', '2025-01-14', '15:13:24');

-- --------------------------------------------------------

--
-- Table structure for table `colleges`
--

CREATE TABLE `colleges` (
  `college_id` int(10) NOT NULL,
  `college` varchar(255) NOT NULL,
  `status` enum('enabled','disabled') DEFAULT 'enabled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colleges`
--

INSERT INTO `colleges` (`college_id`, `college`, `status`) VALUES
(1, 'Select College', 'enabled'),
(2, 'CAFENR', 'enabled'),
(3, 'CAS', 'enabled'),
(4, 'CCJ', 'enabled'),
(5, 'CED', 'enabled'),
(6, 'CEMDS', 'enabled'),
(7, 'CEIT', 'enabled'),
(8, 'CON', 'enabled'),
(9, 'CSPEAR', 'enabled'),
(10, 'CVMBS', 'enabled'),
(11, 'COM', 'enabled'),
(12, 'Graduate School', 'enabled'),
(13, 'Admin', 'enabled'),
(14, 'OSAS', 'enabled'),
(18, 'New College', 'disabled'),
(19, 'KITA', 'disabled'),
(20, 'Visitors', 'enabled');

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
  `view_status` tinyint(1) DEFAULT 0 COMMENT '0=unread, 1=read'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reported_items`
--

INSERT INTO `reported_items` (`id_item`, `Fname`, `Lname`, `email`, `contact_no`, `dept_college`, `item_name`, `item_category`, `location_found`, `report_date`, `report_time`, `other_details`, `img1`, `img2`, `img3`, `img4`, `img5`, `status`, `remark`, `view_status`) VALUES
(1, 'maria', 'mercedes', 'maria@mercedes.com', '09151515151', 'CON', 'ID library', 'ID', 'bleachers', '2024-12-10', '08:39:00.000000', 'ID sya for library', '6757febbb75c1_img1.jpg', '', '', '', '', 'Unclaimed', 'Approved', 1),
(2, 'Maria', 'Mercedes', 'maria@mercedes.com', '09115151515', 'CON', 'Aquaflask', 'Tumbler', 'CSPEAR Gym', '2024-12-02', '09:42:00.000000', 'Aquaflask na dilaw', '6757ffa3bbcfd_img1.jpg', '', '', '', '', 'Claimed', 'Approved', 1),
(3, 'Maria', 'Mercedes', 'maria@mercedes.com', '09151515151', 'CON', 'iPhone', 'Gadgets/Electronics', 'Library', '2024-12-08', '10:46:00.000000', 'iPhone na square', '67580028a9dde_img1.jpg', '', '', '', '', 'Claimed', 'Approved', 1),
(4, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09123456780', 'CEIT', 'Black payong', 'Umbrella', 'CON', '2024-12-10', '07:48:00.000000', 'may pangalan po sa handle', '675800cbe0ba9_img1.jpg', '', '', '', '', 'Unclaimed', 'Approved', 1),
(5, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09123456789', 'CEIT', 'Pencil', 'School Supplies', 'CEMDS', '2024-12-09', '11:50:00.000000', 'pencil na yellow', '67580108b9e67_img1.jpg', '', '', '', '', 'Claimed', 'Approved', 1),
(6, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09163141245', 'CEIT', 'cellphone', 'Gadgets/Electronics', 'CEIT', '2024-11-28', '08:51:00.000000', 'cellphone white', '675801473c11e_img1.jpg', '', '', '', '', 'Unclaimed', 'Approved', 1),
(7, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123123131', 'CEIT', 'wallet', 'Wallet', 'CAFENR', '2024-12-10', '14:53:00.000000', 'wallet black', '675801cb56d51_img1.jpg', '', '', '', '', 'Claimed', 'Approved', 1),
(8, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123414341', 'CEIT', 'Laptop', 'Gadgets/Electronics', 'CSPEAR', '2024-12-09', '06:54:00.000000', 'laptop white', '67580241dceb8_img1.jpg', '', '', '', '', 'Unclaimed', 'Approved', 1),
(9, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123131455', 'CEIT', 'Uniform', 'Clothes', 'CED', '2024-12-01', '10:56:00.000000', 'uniform polo', '675802791b124_img1.jpg', '', '', '', '', 'Claimed', 'Approved', 1),
(10, '', '', '', '', '', '', '', '', '0000-00-00', '00:00:00.000000', '', '', '', '', '', '', 'Unclaimed', 'Reject', 1),
(11, 'Clark', 'mendoza', 'clarkangelo.mendoza@cvsu.edu.ph', '09164713453', 'CEIT', 'wallet na black', 'Wallet', 'Oval', '2025-01-05', '01:14:00.000000', 'black', '677a23e833c08_img1.jpg', '677a23e833e69_img2.jpg', '', '', '', 'Claimed', 'Approved', 0),
(12, '', '', '', '', '', '', '', '', '0000-00-00', '00:00:00.000000', '', '', '', '', '', '', 'Unclaimed', 'Reject', 0),
(13, '', '', '', '', '', '', '', '', '0000-00-00', '00:00:00.000000', '', '', '', '', '', '', 'Unclaimed', 'Reject', 0),
(16, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09458955569', 'CEIT', 'Bostanten Wallet', 'Wallet', 'Library', '2025-01-06', '08:30:00.000000', 'It\\\'s a Black Bostanten wallet with cash inside and some identification.', '677b6dfa61402_img1.jpg', '', '', '', '', 'Unclaimed', 'Pending', 0),
(17, 'Jake', 'Ersando', 'jake.ersando@cvsu.edu.ph', '09164713453', 'CEIT', 'Dell Laptop', 'Gadgets/Electronics', 'CCL102 DIT Building', '2025-01-07', '14:55:00.000000', 'Black laptop, Dell brand', '677cde687f343_img1.jpg', '677cde687f555_img2.jpg', '', '', '', 'Claimed', 'Approved', 0),
(19, 'Clark Angelo', 'Mendoza', 'clarkangelo.mendoza@cvsu.edu.ph', '09123456789', 'CEIT', 'Varsity Jacket', 'Clothes', 'CVMBS bldg', '2025-01-12', '15:06:00.000000', 'Color blue. Medium Size', '67836a791e6e2_img1.jpg', '', '', '', '', 'Unclaimed', 'Approved', 0),
(20, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123456789', 'CEIT', 'Charger', 'Gadgets/Electronics', 'DIT bldg. 2nd floor', '2025-01-14', '09:00:00.000000', 'Has a blue sticker on it.', '6785e8e50e33d_img1.jpg', '', '', '', '', 'Claimed', 'Approved', 0),
(21, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09622849505', 'CEIT', 'Grad Pic Price List', 'Documents', 'CEMDS bldg.', '2025-01-14', '22:09:00.000000', 'Grad pics price list with packages', '6786707b54f3f_img1.jpg', '', '', '', '', 'Unclaimed', 'Pending', 0),
(22, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123456789', 'CEIT', 'CvSU OJT Document', 'Documents', 'Unitop Mall Dasma', '2025-02-07', '11:05:00.000000', 'HTE name: DOLE Cavite', '67a7d5c256541_img1.jpg', '', '', '', '', 'Unclaimed', 'Approved', 0),
(23, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123456789', 'CEIT', 'CvSU Map', 'Documents', 'OSAS', '2025-02-06', '11:09:00.000000', 'Vector type', '67a7d6589751b_img1.jpg', '', '', '', '', 'Unclaimed', 'Approved', 0),
(24, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123456789', 'CEIT', 'OJT Resume', 'Documents', 'SM Dasma', '2025-02-09', '05:45:00.000000', 'black theme', '67a7d703c9c1b_img1.jpg', '', '', '', '', 'Unclaimed', 'Reject', 0),
(25, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123456789', 'CEIT', 'Black Coat Suit', 'Clothes', 'Gate 1 CvSU Main', '2025-02-09', '22:30:00.000000', 'L size', '67a7d7caa2201_img1.jpg', '', '', '', '', 'Unclaimed', 'Reject', 0),
(26, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123456789', 'CEIT', 'Black Coat Suit', 'Clothes', 'Gate 1 CvSU Main', '2025-02-08', '17:30:00.000000', 'L size', '67a7d848cf071_img1.jpg', '', '', '', '', 'Unclaimed', 'Approved', 0);

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
(1, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09622849505', 'CEIT', 'National ID', 'ID', 'DIT shed', '2025-01-12', '09:33:00.000000', 'Temporary National ID po siya. Then 2024 po registration date. Feb 12, 2001 po bday', '67831c8f9f5ae_img1_1736645769462.jpg', '', '', '', '', 'Found', 0),
(2, 'Clark Angelo', 'Mendoza', 'clarkangelo.mendoza@cvsu.edu.ph', '09123456789', 'CEIT', 'Mid Range PC', 'Wallet', 'CON Bldg.', '2025-01-10', '11:36:00.000000', 'Cyberpunk Edgerunner Wallpaper', '67831d11d9ea9_img1_1736645903411.jpg', '', '', '', '', 'Missing', 0),
(5, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123456789', 'CEIT', 'Oppo Charger', 'Gadgets/Electronics', 'DIT building 2nd Floor', '2025-01-14', '08:30:00.000000', 'I have lost my phone charger. It has a blue sticker on it and it says \\\"Drooliest\\\".', '6785e8710bb79_img1_1736829034673.jpg', '', '', '', '', 'Found', 0),
(6, 'Clark', 'Mendoza', 'clarkangelo.mendoza@cvsu.edu.ph', '09164713453', 'CEMDS', 'Grad Pic Menu', 'Documents', 'CEMDS bldg.', '2025-01-14', '08:45:00.000000', 'Pricelist of Grad pics', '67866fb99cfd5_img1_1736863665155.jpg', '', '', '', '', 'Found', 0),
(7, 'Joshua', 'Penuela', 'joshua.penuela@cvsu.edu.ph', '09622849505', 'CEIT', 'Dextrose Powder', 'Others', 'Dasma', '2025-01-17', '10:27:00.000000', '200 grams', '6789ce87e2303_img1_1737084546808.jpg', '', '', '', '', 'Missing', 0),
(8, 'Alexander', 'Llano', 'alexander.llano@cvsu.edu.ph', '09123456789', 'CEIT', 'OJT Document', 'Documents', 'Unitop Mall', '2025-02-07', '09:03:00.000000', 'HTE name: DOLE-Cavite', '67a7d52216ed8_img1_1739052321069.jpg', '', '', '', '', 'Found', 0);

-- --------------------------------------------------------

--
-- Table structure for table `signatories`
--

CREATE TABLE `signatories` (
  `id` int(11) NOT NULL,
  `role` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `signatories`
--

INSERT INTO `signatories` (`id`, `role`, `name`, `position`) VALUES
(1, 'Prepared by', 'JOSHUA PENUELA', ''),
(2, 'Noted by', 'JOENEIL L. ERNI', 'Property Custodian'),
(3, 'Reviewed by', 'LAARNI A. ROM', 'Administrative Aide III'),
(4, 'Approved by', 'ROMULO L. GOMEZ', 'Director, Civil Security Services');

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
  `dataPrivacy` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `Lname`, `Fname`, `email`, `dept`, `password`, `contactNo`, `otp`, `reset_pass_time`, `dataPrivacy`) VALUES
(1, 'Penuela', 'Joshua', 'joshua.penuela@cvsu.edu.ph', 'CEIT', '$2y$10$vIVdT5QxYdCPGC.w3mUiQemBKGGzfeQFTbF2iyJ2R7nyzKX0OAoLC', '09622849505', NULL, '2025-06-08 05:06:01', 'Agreed'),
(2, 'Mendoza', 'Clark Angelo', 'clarkangelo.mendoza@cvsu.edu.ph', 'CEIT', '$2y$10$Hleh4OhhjocGBQn6Guypo.qVyh9p8udJV10XGVDEVC9YcC2h.v1iG', '09123456789', NULL, '2024-12-27 07:11:05', 'Agreed'),
(3, 'Llano', 'Alexander', 'alexander.llano@cvsu.edu.ph', 'CEIT', '$2y$10$dSdz6TikGIeP93RUi5JJCun8s/iNG0FD6EsOyo3NTSnmtCQsuYwYy', '09123456789', '530962', '2025-06-07 21:38:42', 'Disagreed'),
(4, 'Agor', 'Sharon', 'sharon.agor@cvsu.edu.ph', 'CON', '$2y$10$GQJj0msKOEX78suc0e4/teH1lsOxf9PqDpkaJlRODlLYioZncsURu', '09123456789', NULL, '2024-12-27 07:12:13', 'Disagreed'),
(5, 'Penuela', 'Joshua', 'joshuapenuela12@gmail.com', 'CEIT', '$2y$10$J2UoVqDfYsMOlziVt/wXGek8kDIFGD4bkiD1k5evE3vNAfIfhpGni', '09622849506', NULL, '2024-12-29 01:22:09', 'Agreed'),
(6, 'Penuela', 'Joshua', 'joshuapenuela@gmail.com', 'CEIT', '$2y$10$/VU6hkjxAHI.40.QHM/NiuiLpHb.WF1tcQhlt4sO.jJYNKMhiRJCa', '09123456789', NULL, '2024-12-29 01:22:14', 'Agreed'),
(7, 'Panganiban', 'Catherine', 'catherine.panganiban@cvsu.edu.ph', 'CEIT', '$2y$10$ldTm26Nm78HSRmNBwO.M9uKahB5R.CNujTIJXDDtaEFPVILmTFZbm', '09165326656', NULL, '2024-12-27 07:12:24', 'Disagreed'),
(8, 'Escover', 'Miggy', 'miguel.escover@cvsu.edu.ph', 'Graduate School', '$2y$10$wwtUhjOO92jeb/S7eIKR6OghSTv6OGGt2cxV5A3Mk4DByBvceMbg6', '09789789456', NULL, '2024-12-27 07:12:27', 'Disagreed'),
(9, 'Dadula', 'Cyndell', 'cyndell.dadula@cvsu.edu.ph', 'CVMBS', '$2y$10$T8hvIf5tRcpABvt5hG33PuscMTsBOsWhuajKRecHp8YRLhLKZNyl2', '09132789799', NULL, '2024-12-29 01:19:34', 'Agreed'),
(10, 'Mojica', 'Neil', 'neil.mojica@cvsu.edu.ph', 'CEMDS', '$2y$10$nVfXODpVcTX1nEs4rkwQfuf/Elql2Olj8EqCkVpFLLcZPJdJu2lxu', '09321465465', NULL, '2024-12-29 01:21:16', 'Disagreed'),
(11, 'Estrella', 'John Robin', 'johnrobin.estrella@cvsu.edu.ph', 'CEIT', '$2y$10$BhP9WR3DgbtzITKqW2VrlunSNNR6PW5sg6wM7EkTdbseIFE3CYvpm', '09254616816', NULL, '2025-01-03 23:39:25', 'Agreed'),
(12, 'Ramirez', 'Hanzel', 'hanzel.ramirez@cvsu.edu.ph', 'CEIT', '$2y$10$md4qO2RBIvH592CxZmwNN.3CnUAwqisqHaNQAMt7OQ7EtMsiJpY5W', '09151586440', NULL, '2025-01-06 07:49:28', 'Agreed'),
(13, 'Ersando', 'Jake', 'jake.ersando@cvsu.edu.ph', 'CEIT', '$2y$10$YjC9eAuKQQL8Yp5UKMlu/eI6N4k6nQ9SLLjCIq.L19SO8KAqW384y', '09161231252', NULL, '2025-01-07 07:55:07', 'Agreed');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_message`
--
ALTER TABLE `admin_message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `claim_reports`
--
ALTER TABLE `claim_reports`
  ADD PRIMARY KEY (`id_claim`);

--
-- Indexes for table `colleges`
--
ALTER TABLE `colleges`
  ADD PRIMARY KEY (`college_id`);

--
-- Indexes for table `reported_items`
--
ALTER TABLE `reported_items`
  ADD PRIMARY KEY (`id_item`);

--
-- Indexes for table `reported_lost_items`
--
ALTER TABLE `reported_lost_items`
  ADD PRIMARY KEY (`id_item`);

--
-- Indexes for table `signatories`
--
ALTER TABLE `signatories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `admin_message`
--
ALTER TABLE `admin_message`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `claim_reports`
--
ALTER TABLE `claim_reports`
  MODIFY `id_claim` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `colleges`
--
ALTER TABLE `colleges`
  MODIFY `college_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `reported_items`
--
ALTER TABLE `reported_items`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `reported_lost_items`
--
ALTER TABLE `reported_lost_items`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `signatories`
--
ALTER TABLE `signatories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
