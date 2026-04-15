-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql-ucschelpdesk.alwaysdata.net
-- Generation Time: Apr 05, 2026 at 01:52 AM
-- Server version: 10.11.15-MariaDB
-- PHP Version: 8.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ucschelpdesk_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `id` int(11) NOT NULL,
  `content` varchar(500) DEFAULT NULL,
  `topic` varchar(50) DEFAULT NULL,
  `u_id` int(11) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcement`
--

INSERT INTO `announcement` (`id`, `content`, `topic`, `u_id`, `date_time`) VALUES
(1, '2025/2024 registration closes on June 10th, 2025. Use the academic portal.', 'Course Registration', 10, '2025-08-20 08:00:00'),
(2, 'Servers down on August 22, 2025, 11:00 PM to 3:50 AM.', 'System Maintenance', 1, '2025-08-20 10:15:00'),
(3, 'Mental health workshops from August 26-30, 2025.', 'Counseling Workshops', 2, '2025-08-18 14:45:00'),
(4, 'New staff policies effective September 1, 2025.', 'Policy Update', NULL, '2025-08-19 11:30:00'),
(10, 'For ICter conference now we are accepting students research. Please submit them before 5th of August 2026', 'Research Abstract Submission', 17, '2025-08-30 10:23:32'),
(11, 'Dear Students ,\r\nthe wifi issue is now solved now , you can connect to UCSC wifi now\r\n\r\nThankss', 'Wifi Issue', 29, '2025-08-31 08:57:33');

-- --------------------------------------------------------

--
-- Table structure for table `announcement_files`
--

CREATE TABLE `announcement_files` (
  `file_id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `file_size` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcement_files`
--

INSERT INTO `announcement_files` (`file_id`, `announcement_id`, `file_name`, `file_path`, `file_type`, `file_size`) VALUES
(6, 11, '23000139 (16).pdf', 'uploads/announcements/11/1756610853_23000139 (16).pdf', 'application/pdf', 690061);

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL,
  `entity_type` varchar(50) NOT NULL COMMENT 'e.g., ticket, forum, kb, announcement',
  `entity_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `file_size` int(11) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attachments`
--

INSERT INTO `attachments` (`id`, `entity_type`, `entity_id`, `file_name`, `file_path`, `file_type`, `file_size`, `uploaded_by`, `uploaded_at`) VALUES
(1, 'ticket', 131, 'test.rtf', 'uploads/ticket/69cd0f16d7b41_1775046422.rtf', '0', 391, 8, '2026-04-01 12:27:03'),
(2, 'ticket', 132, 'Screenshot 2026-03-24 at 00.07.14.png', 'uploads/ticket/69cd101c5869d_1775046684.png', '0', 71982, 8, '2026-04-01 12:31:24'),
(3, 'forum', 17, 'Screenshot 2026-03-24 at 00.07.14.png', 'uploads/forum/69cd107138ff4_1775046769.png', '0', 71982, 8, '2026-04-01 12:32:49'),
(4, 'ticket', 133, 'Screenshot 2026-03-24 at 00.07.14.png', 'uploads/ticket/69cd10bb70b26_1775046843.png', '0', 71982, 8, '2026-04-01 12:34:03'),
(5, 'ticket', 136, 'Screenshot 2026-03-24 at 00.07.14.png', 'uploads/ticket/69d19f3b08e83_1775345467.png', '0', 71982, 8, '2026-04-04 23:31:07');

-- --------------------------------------------------------

--
-- Table structure for table `calendar_events`
--

CREATE TABLE `calendar_events` (
  `event_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `is_all_day` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `calendar_events`
--

INSERT INTO `calendar_events` (`event_id`, `u_id`, `title`, `description`, `event_date`, `start_time`, `end_time`, `is_all_day`, `created_at`, `updated_at`) VALUES
(5, 21, 'Meeting of Alice at 9 am', NULL, '2025-12-26', NULL, NULL, 1, '2025-12-25 21:14:07', NULL),
(7, 21, 'Freshers night', NULL, '2025-12-30', NULL, NULL, 1, '2025-12-30 08:40:06', NULL),
(16, 21, '100th anniversary of UCSC', 'All cheaf guests are comming', '2026-01-28', '10:00:00', '11:00:00', 1, '2026-01-11 07:12:03', '2026-01-11 07:23:17'),
(22, 21, 'Reid Handawa', 'Can enjoy but limited access', '2026-01-30', '18:00:00', '12:00:00', 0, '2026-01-12 17:00:02', NULL),
(26, 8, 'event', 'hehe', '2026-01-13', '09:00:00', '10:00:00', 0, '2026-01-15 08:10:46', NULL),
(27, 8, 'hooo', NULL, '2026-01-15', NULL, NULL, 1, '2026-01-15 08:11:15', NULL),
(28, 8, 'hooo', NULL, '2026-01-15', NULL, NULL, 1, '2026-01-15 08:11:16', NULL),
(29, 23, 'Test case', 'Testing the vent', '2026-01-16', '09:00:00', '10:00:00', 0, '2026-01-15 10:04:15', NULL),
(30, 23, 'Test case', 'Testing the vent', '2026-01-16', '09:00:00', '10:00:00', 0, '2026-01-15 10:04:17', NULL),
(31, 8, 'hehe', NULL, '2026-01-28', '09:00:00', '10:00:00', 0, '2026-01-31 17:22:54', NULL),
(32, 8, 'test', NULL, '2026-01-31', '09:00:00', '10:00:00', 0, '2026-01-31 17:29:40', NULL),
(33, 8, 'test 2', NULL, '2026-02-01', '09:00:00', '10:00:00', 0, '2026-01-31 17:29:49', NULL),
(34, 8, 'yes', NULL, '2026-02-02', '09:00:00', '10:00:00', 0, '2026-02-02 13:23:18', NULL),
(35, 8, 'test', NULL, '2026-02-03', '09:00:00', '10:00:00', 0, '2026-02-02 13:28:00', NULL),
(43, 7, 'Meeting', NULL, '2026-02-04', '21:00:00', '22:00:00', 0, '2026-02-04 08:29:28', NULL),
(44, 21, 'Counseling Session', 'Meeting ID: 2', '2026-02-27', '20:30:00', '21:30:00', 0, '2026-02-04 15:56:57', NULL),
(52, 12, 'Meeting: Meeting Request', 'Meeting ID: 7', '2026-02-19', '10:53:00', '11:53:00', 0, '2026-02-15 18:23:42', NULL),
(53, 21, 'Meeting: Meeting Request', 'Meeting ID: 8', '2026-03-12', '00:24:00', '01:24:00', 0, '2026-03-06 04:51:18', NULL),
(54, 12, 'Meeting: Meeting Request', 'Meeting ID: 8', '2026-03-12', '00:24:00', '01:24:00', 0, '2026-03-06 04:51:19', NULL),
(56, 12, 'Meeting: Meeting Request', 'Meeting ID: 9', '2026-04-09', '14:13:00', '15:13:00', 0, '2026-04-01 10:41:49', NULL),
(58, 12, 'Meeting: Meeting Request', 'Meeting ID: 10', '2026-04-08', '18:58:00', '19:58:00', 0, '2026-04-01 15:24:21', NULL),
(72, 36, 'Meeting: Meeting Request', 'Meeting ID: 17', '2026-04-04', '09:39:00', '10:39:00', 0, '2026-04-02 06:06:32', NULL),
(76, 36, 'Meeting: Meeting Request', 'Meeting ID: 19', '2026-04-16', '09:53:00', '10:53:00', 0, '2026-04-02 06:20:23', NULL),
(80, 36, 'Meeting: Meeting Request', 'Meeting ID: 21', '2026-04-25', '10:10:00', '10:25:00', 0, '2026-04-02 06:36:28', NULL),
(88, 36, 'Meeting: Meeting Request', 'Meeting ID: 25', '2026-04-25', '13:58:00', '14:58:00', 0, '2026-04-02 10:25:22', NULL),
(90, 36, 'Meeting: Meeting Request', 'Meeting ID: 26', '2026-05-14', '10:08:00', '11:08:00', 0, '2026-04-02 18:35:14', NULL),
(91, 21, 'Meeting: Meeting Request', 'Meeting ID: 27', '2026-04-16', '12:11:00', '13:11:00', 0, '2026-04-02 20:41:27', NULL),
(92, 36, 'Meeting: Meeting Request', 'Meeting ID: 27', '2026-04-16', '12:11:00', '13:11:00', 0, '2026-04-02 20:41:28', NULL),
(98, 36, 'Meeting: Meeting Request', 'Meeting ID: 30', '2026-04-21', '03:42:00', '03:57:00', 0, '2026-04-02 22:13:06', NULL),
(102, 36, 'Meeting: Meeting Request', 'Meeting ID: 32', '2026-04-10', '01:50:00', '02:50:00', 0, '2026-04-02 22:19:55', NULL),
(118, 26, 'Meeting: Help is needed', 'Meeting ID: 40', '2026-04-11', '02:11:00', '03:11:00', 0, '2026-04-02 22:39:43', NULL),
(119, 21, 'Meeting: Meeting Request', 'Meeting ID: 41', '2026-05-16', '11:42:00', '11:57:00', 0, '2026-04-03 08:13:21', NULL),
(120, 36, 'Meeting: Meeting Request', 'Meeting ID: 41', '2026-05-16', '11:42:00', '11:57:00', 0, '2026-04-03 08:13:21', NULL),
(122, 36, 'Meeting: Meeting Request', 'Meeting ID: 42', '2026-04-11', '07:52:00', '08:07:00', 0, '2026-04-03 16:23:09', NULL),
(123, 21, 'Meeting: Meeting Request', 'Meeting ID: 43', '2026-04-10', '08:01:00', '08:16:00', 0, '2026-04-03 16:32:23', NULL),
(124, 36, 'Meeting: Meeting Request', 'Meeting ID: 43', '2026-04-10', '08:01:00', '08:16:00', 0, '2026-04-03 16:32:23', NULL),
(126, 36, 'Meeting: Meeting Request', 'Meeting ID: 44', '2026-04-04', '08:12:00', '09:12:00', 0, '2026-04-03 16:43:09', NULL),
(128, 36, 'Meeting: Meeting Request', 'Meeting ID: 45', '2026-05-01', '08:21:00', '09:21:00', 0, '2026-04-03 16:52:17', NULL),
(129, 21, 'Meeting: Meeting Request', 'Meeting ID: 46', '2026-04-25', '17:15:00', '17:30:00', 0, '2026-04-03 20:42:49', NULL),
(130, 12, 'Meeting: Meeting Request', 'Meeting ID: 46', '2026-04-25', '17:15:00', '17:30:00', 0, '2026-04-03 20:42:49', NULL),
(132, 36, 'Meeting: Meeting Request', 'Meeting ID: 47', '2026-04-18', '16:20:00', '17:20:00', 0, '2026-04-03 20:51:15', NULL),
(133, 21, 'Meeting: Help is needed', 'Meeting ID: 48', '2026-06-04', '13:00:00', '13:15:00', 0, '2026-04-04 09:36:07', NULL),
(134, 26, 'Meeting: Help is needed', 'Meeting ID: 48', '2026-06-04', '13:00:00', '13:15:00', 0, '2026-04-04 09:36:07', NULL),
(135, 21, 'Meeting: Meeting Request', 'Meeting ID: 49', '2026-05-14', '13:15:00', '13:30:00', 0, '2026-04-04 09:46:11', NULL),
(136, 36, 'Meeting: Meeting Request', 'Meeting ID: 49', '2026-05-14', '13:15:00', '13:30:00', 0, '2026-04-04 09:46:12', NULL),
(137, 21, 'Meeting: Meeting Request', 'Meeting ID: 50', '2026-04-11', '13:25:00', '14:25:00', 0, '2026-04-04 09:55:22', NULL),
(138, 12, 'Meeting: Meeting Request', 'Meeting ID: 50', '2026-04-11', '13:25:00', '14:25:00', 0, '2026-04-04 09:55:22', NULL),
(139, 21, 'Meeting: Meeting Request', 'Meeting ID: 51', '2026-04-26', '13:37:00', '14:07:00', 0, '2026-04-04 10:08:02', NULL),
(140, 12, 'Meeting: Meeting Request', 'Meeting ID: 51', '2026-04-26', '13:37:00', '14:07:00', 0, '2026-04-04 10:08:03', NULL),
(141, 21, 'Meeting: Meeting Request', 'Meeting ID: 52', '2026-04-30', '15:30:00', '15:45:00', 0, '2026-04-04 10:21:33', NULL),
(142, 12, 'Meeting: Meeting Request', 'Meeting ID: 52', '2026-04-30', '15:30:00', '15:45:00', 0, '2026-04-04 10:21:34', NULL),
(144, 12, 'Meeting: Meeting Request', 'Meeting ID: 53', '2026-05-07', '14:00:00', '15:00:00', 0, '2026-04-04 10:30:53', NULL),
(146, 12, 'Meeting: Meeting Request', 'Meeting ID: 54', '2026-04-18', '14:07:00', '15:07:00', 0, '2026-04-04 10:38:25', NULL),
(147, 21, 'Meeting: Meeting Request', 'Meeting ID: 55', '2026-05-08', '14:19:00', '15:19:00', 0, '2026-04-04 10:50:51', NULL),
(148, 12, 'Meeting: Meeting Request', 'Meeting ID: 55', '2026-05-08', '14:19:00', '15:19:00', 0, '2026-04-04 10:50:51', NULL),
(149, 21, 'Meeting: Meeting Request', 'Meeting ID: 56', '2026-07-04', '14:37:00', '14:52:00', 0, '2026-04-04 11:08:27', NULL),
(150, 36, 'Meeting: Meeting Request', 'Meeting ID: 56', '2026-07-04', '14:37:00', '14:52:00', 0, '2026-04-04 11:08:27', NULL),
(151, 21, 'Meeting: Meeting Request', 'Meeting ID: 57', '2026-05-09', '14:44:00', '14:59:00', 0, '2026-04-04 11:15:52', NULL),
(152, 36, 'Meeting: Meeting Request', 'Meeting ID: 57', '2026-05-09', '14:44:00', '14:59:00', 0, '2026-04-04 11:15:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `counselors`
--

CREATE TABLE `counselors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `counselor_availability`
--

CREATE TABLE `counselor_availability` (
  `availability_id` int(11) NOT NULL,
  `counselor_id` int(11) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `division`
--

CREATE TABLE `division` (
  `did` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `division`
--

INSERT INTO `division` (`did`, `name`) VALUES
(1, 'General Administration'),
(2, 'Establishment'),
(3, 'Academic Publication and Welfare'),
(4, 'Postgraduate Research and Project'),
(5, 'Examination and Registration'),
(6, 'Engineering'),
(7, 'Finance'),
(8, 'Library'),
(9, 'CSC and NOC'),
(10, 'Counselling');

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`id`, `question`, `answer`, `created_at`) VALUES
(1, 'How do I connect to University wifi on a new device', 'Go to the template options in ticket creation and use the Request wifi template', '2025-10-23 14:26:53'),
(2, 'How do I reset my UCSC portal password if I forgot it?', 'Go to the UCSC_Login Portal, click on \"Forgot Password,\" and follow the instructions. You\'ll need to enter your student ID and registered email address. If you don\'t receive the reset email within 5 minutes, check your spam folder or create a ticket through UCSC HelpDesk.', '2025-10-23 14:29:00'),
(3, 'The classroom projector isn\'t working - who do I report this to?', 'Please create a new ticket under the Facilities category and describe the room number, time, and the issue you encountered. Urgent facility issues may be prioritized and routed directly to maintenance staff,', '2025-10-23 14:29:26'),
(4, 'Can I change or cancel an appointment I made with the help desk?', 'Yes. Ilf your ticket includes an appointment, reply to the ticket and request a new time. For same-day changes, please submit a new comment on the ticket so staff are notified immediately.', '2025-10-23 14:29:51'),
(5, 'I can\'t access my email or LMS (Learning Management System). What should I do?', 'First, verify you can sign in to the UCSC portal. If portal sign-in works, try resetting your LMS/email password if available. Clear your browser cache or try a different browser. If the issue persists, submit a ticket with screenshots and timestamps.', '2025-10-23 14:30:42'),
(6, 'What should I do if there\'s a power cut or AC issue during class?', 'Use the \"Facilities Issue\" category on the Help Desk portal to report it immediately. These reports are visible to all students and can be upvoted if others are facing the same issue. Critical facility problems are prioritized and sent directly to campus maintenance,.', '2025-10-23 14:31:10'),
(10, 'checking the FAQs', 'Please work . arghhhhhh', '2026-01-03 05:37:34');

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`id`, `ticket_id`, `student_id`, `rating`, `feedback`, `created_at`) VALUES
(1, 34, 8, 5, 'fine', '2026-04-04 23:46:12'),
(2, 137, 8, 4, 'fine', '2026-04-04 23:51:56');

-- --------------------------------------------------------

--
-- Table structure for table `forum_comments`
--

CREATE TABLE `forum_comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_comments`
--

INSERT INTO `forum_comments` (`id`, `post_id`, `u_id`, `parent_id`, `content`, `created_at`) VALUES
(1, 15, 8, NULL, 'testing comments', '2026-02-14 17:39:30'),
(2, 15, 8, 1, 'testing replies', '2026-02-14 17:39:48'),
(3, 15, 8, 2, 'testing replies to replies', '2026-02-14 17:40:08'),
(5, 15, 8, 1, '2nd reply', '2026-02-14 17:43:48'),
(6, 15, 34, 3, 'i know right', '2026-02-14 17:59:17');

-- --------------------------------------------------------

--
-- Table structure for table `forum_q`
--

CREATE TABLE `forum_q` (
  `q_id` int(11) NOT NULL,
  `is_Public` tinyint(1) DEFAULT 0,
  `title` text NOT NULL,
  `topic` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `description` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `u_id` int(11) NOT NULL,
  `status` enum('open','answered') NOT NULL DEFAULT 'open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_q`
--

INSERT INTO `forum_q` (`q_id`, `is_Public`, `title`, `topic`, `description`, `created_at`, `u_id`, `status`) VALUES
(1, 0, 'this is private', 'Finance', 'test', NULL, 8, 'open'),
(2, 1, 'this is public', 'IT Support', 'hey', '2025-10-19 16:09:30', 8, 'open'),
(5, 1, 'Admission card isnt printing', 'Examinations', 'Is it me or , im facing an issue with printing an admission card? is there any more students from other years have this issue ? or just us 2nd years ?', '2025-10-22 16:39:36', 12, 'open'),
(6, 1, 'Admission Issue', 'Examinations', 'is it only for 2nd years? we cannot print it', '2025-10-22 17:45:20', 24, 'open'),
(7, 1, 'Examination Admission issue', 'Examinations', 'i cannot clearly print the admission, and its due tommrow. im a 1st yr . please anyone , help me ? is it just me ?', '2025-10-22 17:52:11', 25, 'open'),
(8, 1, 'Food in Bawana', 'General', 'Is the food given by Bawana good? i had food poisoning other day . is it just me ? .', '2025-10-22 18:02:36', 26, 'open'),
(9, 1, 'Admission print button dosent work', 'IT Support', 'Im not sure who to inform . is this only me? is it a technical issue or my device issue ?', '2025-10-22 18:13:39', 27, 'open'),
(10, 1, 'water leak on E401', 'General', 'is that AC water leaks oon E401 ? is it normal ?', '2025-10-22 18:20:17', 28, 'open'),
(15, 1, 'testing voting', 'General', 'test', '2026-02-14 14:10:59', 8, 'open'),
(16, 1, 'test 2', 'IT Support', 'test', '2026-02-14 18:29:26', 8, 'open'),
(17, 1, 'cwcew', 'General', 'cec', '2026-04-01 14:32:49', 8, 'open');

-- --------------------------------------------------------

--
-- Table structure for table `forum_votes`
--

CREATE TABLE `forum_votes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `vote_type` tinyint(2) NOT NULL DEFAULT 0 COMMENT '1: up, -1: down',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_votes`
--

INSERT INTO `forum_votes` (`id`, `post_id`, `u_id`, `vote_type`, `created_at`) VALUES
(1, 15, 8, 1, '2026-02-14 16:11:01'),
(3, 15, 34, 1, '2026-02-14 16:14:02'),
(8, 5, 21, 1, '2026-03-05 18:28:05'),
(9, 15, 21, -1, '2026-04-01 08:43:22'),
(10, 17, 7, 1, '2026-04-03 15:31:42');

-- --------------------------------------------------------

--
-- Table structure for table `kb_files`
--

CREATE TABLE `kb_files` (
  `file_id` int(11) NOT NULL,
  `kb_id` int(11) NOT NULL,
  `file_name` text NOT NULL,
  `file_path` text NOT NULL,
  `file_type` text NOT NULL,
  `file_size` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kb_files`
--

INSERT INTO `kb_files` (`file_id`, `kb_id`, `file_name`, `file_path`, `file_type`, `file_size`) VALUES
(1, 13, 'CMPLS_version_2.drawio.png', 'uploads/kb/10/1765626808_CMPLS_version_2.drawio.png', 'image/png', '184652'),
(3, 4, 'oop.pdf', 'uploads/kb/10/1765704913_oop.pdf', 'application/pdf', '492869'),
(4, 5, 'Group_05.pdf', 'uploads/kb/10/1765706125_Group_05.pdf', 'application/pdf', '197872');

-- --------------------------------------------------------

--
-- Table structure for table `knowledgebase`
--

CREATE TABLE `knowledgebase` (
  `base_id` int(11) NOT NULL,
  `topic` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `section` enum('General Documents','Policies and Rules','Academic Resources') NOT NULL DEFAULT 'General Documents',
  `updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `type` enum('Guide','Schedule') DEFAULT 'Guide'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `knowledgebase`
--

INSERT INTO `knowledgebase` (`base_id`, `topic`, `description`, `section`, `updated`, `created_by`, `type`) VALUES
(1, 'Map of UCSC', 'Detailed floor map of UCSC buildings and facilities.', 'General Documents', '2025-11-26 08:32:52', 10, 'Guide'),
(2, 'Student handbook 2025', 'Complete guide for new and continuing students.', 'General Documents', '2025-11-26 08:33:48', 10, 'Guide'),
(3, 'Undergraduate academic calender', 'Complete timeline of the semesters and exams for students of all years', 'General Documents', '2025-11-26 08:40:57', NULL, 'Schedule'),
(4, 'Academic Calender', 'Complete timeline of the semesters and exams for students of all years', 'Policies and Rules', '2025-12-14 09:35:10', 10, 'Schedule'),
(5, 'Academic Calender', 'Complete timeline of the semesters and exams for students of all years', 'Academic Resources', '2025-12-14 09:55:23', 10, 'Schedule'),
(6, 'Student handbook 2025', 'Complete guide for new and continuing students.\r\n', 'Policies and Rules', '2025-03-04 12:51:03', 10, 'Guide'),
(7, 'Student handbook 2025\r\n', 'Complete guide for new and continuing students.\r\n', 'Academic Resources', '2025-10-08 11:52:10', 10, 'Guide'),
(8, ' Map of UCSC', 'Detailed floor map of UCSC buildings and facilities.\r\n', 'Policies and Rules', '2025-08-11 11:52:51', 10, 'Guide'),
(9, 'Map of UCSC', 'Detailed floor map of UCSC buildings and facilities.\r\n', 'Academic Resources', '2025-06-09 11:53:40', 10, 'Guide'),
(13, 'testing', 'asda', 'Policies and Rules', '2025-12-13 11:53:28', 10, 'Guide');

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

CREATE TABLE `lecturers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `department` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`id`, `user_id`, `department`) VALUES
(1, 2, 'UCSS'),
(2, 7, 'UCSC'),
(3, 10, 'IT');

-- --------------------------------------------------------

--
-- Table structure for table `lost_found`
--

CREATE TABLE `lost_found` (
  `q_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `item_title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `category` enum('electronics','accessories','documents','other') DEFAULT NULL,
  `when` datetime DEFAULT NULL,
  `item_details` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('lost','found','claimed') DEFAULT 'lost',
  `contact_mobile` varchar(20) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lost_found`
--

INSERT INTO `lost_found` (`q_id`, `u_id`, `item_title`, `category`, `when`, `item_details`, `created_at`, `status`, `contact_mobile`, `contact_email`) VALUES
(3, 8, 'Bluse samsung earbuds', 'electronics', '2025-10-22 22:10:19', 'last seen at canteen', '2025-10-17 01:24:25', 'lost', '0712345678', 'brian@gmail.com'),
(4, 8, 'Laptop', 'electronics', '2025-10-15 22:10:27', 'MacBook Pro', '2025-10-17 01:24:25', 'found', NULL, NULL),
(5, 8, 'Student ID', 'documents', '2025-10-21 22:10:32', '23002891', '2025-10-17 01:24:25', 'found', '0723627274', 'kaweesha@gmail.com'),
(8, 8, 'Lost a charger', 'accessories', '2025-10-22 05:13:00', 'Asus white color charger with a c port is lost', '2025-10-17 18:43:33', 'lost', '0752753645', 'brian@gmail.com'),
(9, 11, 'Car keys', 'accessories', '2025-10-08 00:39:35', 'Honda car keys with a blue key chain is lost.', '2025-10-17 18:53:43', 'claimed', NULL, NULL),
(10, 6, 'Note Book', 'documents', '2025-10-18 21:32:00', 'Lost a note book regarding a math module. you can see the calculations regarding to statistics. Its a blue color Atlas book', '2025-10-18 18:02:56', 'lost', '07793293092', NULL),
(12, 24, 'Water Bottle', 'accessories', '2025-09-17 21:19:00', 'I lost my water bottle , a blue on on hall E401', '2025-10-22 17:50:10', 'claimed', '071456956', 'student1@gmail.com'),
(16, 8, 'Wallet', 'accessories', '2025-10-15 00:31:00', 'Brown leather wallet, in hall E401, after the statistics lecture', '2025-10-23 21:01:55', 'found', NULL, 'brian@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `lost_found_item_files`
--

CREATE TABLE `lost_found_item_files` (
  `q_id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lost_found_response`
--

CREATE TABLE `lost_found_response` (
  `u_id` int(11) NOT NULL,
  `q_id` int(11) NOT NULL,
  `date_time` datetime NOT NULL DEFAULT current_timestamp(),
  `response` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meeting_participants`
--

CREATE TABLE `meeting_participants` (
  `participant_id` int(11) NOT NULL,
  `meeting_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('host','participant','observer') DEFAULT 'participant',
  `status` enum('invited','accepted','declined','tentative') DEFAULT 'invited',
  `joined_at` timestamp NULL DEFAULT NULL,
  `left_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meeting_reminders`
--

CREATE TABLE `meeting_reminders` (
  `reminder_id` int(11) NOT NULL,
  `meeting_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `remind_at` timestamp NOT NULL,
  `reminder_type` enum('email','notification','sms') DEFAULT 'notification',
  `is_sent` tinyint(1) DEFAULT 0,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meeting_schedules`
--

CREATE TABLE `meeting_schedules` (
  `meeting_id` int(11) NOT NULL,
  `counselor_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `meeting_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `duration` int(11) DEFAULT 60 COMMENT 'Duration in minutes',
  `mode` enum('online','in-person','phone') DEFAULT 'online',
  `room_location` varchar(100) DEFAULT NULL,
  `meeting_type` enum('individual','group') DEFAULT 'individual',
  `status` enum('scheduled','confirmed','cancelled','completed') DEFAULT 'scheduled',
  `meeting_link` varchar(500) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meeting_schedules`
--

INSERT INTO `meeting_schedules` (`meeting_id`, `counselor_id`, `student_id`, `ticket_id`, `event_id`, `title`, `description`, `meeting_date`, `start_time`, `end_time`, `duration`, `mode`, `room_location`, `meeting_type`, `status`, `meeting_link`, `location`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, 8, 46, NULL, 'Meeting: Can\'t sleep', NULL, '2026-01-30', '09:00:00', '10:00:00', 60, 'in-person', 'E401', 'individual', 'scheduled', NULL, NULL, 'Bring a A4 sheet and a pen', '2026-01-24 15:01:37', '2026-01-24 15:01:37'),
(2, 21, NULL, NULL, 44, 'Counseling Session', NULL, '2026-02-27', '20:30:00', '21:30:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://support.zoom.com', NULL, NULL, '2026-02-04 14:56:56', '2026-02-04 14:56:57'),
(3, 21, NULL, NULL, NULL, 'Counseling Session', NULL, '2026-02-20', '20:31:00', '21:31:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://support.zoom.com', NULL, 'bring a book', '2026-02-04 14:58:25', '2026-02-04 14:58:26'),
(4, 21, NULL, NULL, NULL, 'Counseling Session', NULL, '2026-02-27', '20:47:00', '21:47:00', 60, 'online', 'S104', 'individual', 'scheduled', 'https://support.zoom.com', NULL, NULL, '2026-02-04 15:14:25', '2026-02-04 15:14:25'),
(5, 21, NULL, NULL, NULL, 'Counseling Session', NULL, '2026-02-11', '22:24:00', '23:24:00', 60, 'online', NULL, 'individual', 'scheduled', NULL, NULL, NULL, '2026-02-04 16:51:18', '2026-02-04 16:51:18'),
(6, 21, NULL, NULL, NULL, 'Counseling Session', NULL, '2026-02-19', '22:40:00', '23:40:00', 60, 'online', 'S104', 'individual', 'scheduled', 'https://support.zoom.com', NULL, NULL, '2026-02-04 17:08:05', '2026-02-04 17:08:06'),
(7, 21, 12, 75, NULL, 'Meeting: Meeting Request', NULL, '2026-02-19', '10:53:00', '11:53:00', 60, 'online', 'E401', 'individual', 'scheduled', NULL, NULL, NULL, '2026-02-15 17:23:41', '2026-02-15 17:23:42'),
(8, 21, 12, 75, 53, 'Meeting: Meeting Request', NULL, '2026-03-12', '00:24:00', '01:24:00', 60, 'online', 'hj', 'individual', 'scheduled', 'https://support.zoom.com', NULL, 'tetsings', '2026-03-06 03:51:18', '2026-03-06 03:51:19'),
(9, 21, 12, 75, NULL, 'Meeting: Meeting Request', NULL, '2026-04-09', '14:13:00', '15:13:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://support.zoom.com', NULL, 'bring a pen', '2026-04-01 08:41:49', '2026-04-01 08:41:50'),
(10, 21, 12, 75, NULL, 'Meeting: Meeting Request', NULL, '2026-04-08', '18:58:00', '19:58:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://support.zoom.com', NULL, NULL, '2026-04-01 13:24:21', '2026-04-01 13:24:22'),
(11, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-17', '00:15:00', '01:15:00', 60, 'online', 'S104', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-01 14:48:36', '2026-04-01 14:48:37'),
(12, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-03', '11:40:00', '12:40:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-01 18:08:19', '2026-04-01 18:08:20'),
(13, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-10', '15:18:00', '16:18:00', 60, 'online', 'S104', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-01 18:45:53', '2026-04-01 18:46:03'),
(14, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-11', '00:24:00', '01:24:00', 60, 'online', 'S104', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-01 18:52:25', '2026-04-01 18:52:27'),
(15, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-11', '00:33:00', '01:33:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-01 18:59:49', '2026-04-01 18:59:50'),
(16, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-11', '09:34:00', '10:34:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 04:01:55', '2026-04-02 04:01:56'),
(17, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-04', '09:39:00', '10:39:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 04:06:31', '2026-04-02 04:06:32'),
(18, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-10', '09:52:00', '10:52:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 04:17:55', '2026-04-02 04:17:56'),
(19, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-16', '09:53:00', '10:53:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, 'gkiou', '2026-04-02 04:20:23', '2026-04-02 04:20:23'),
(20, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-11', '10:05:00', '11:05:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 04:32:46', '2026-04-02 04:32:47'),
(21, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-25', '10:10:00', '10:25:00', 15, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 04:36:27', '2026-04-02 04:36:28'),
(22, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-11', '01:12:00', '02:12:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 07:40:41', '2026-04-02 07:40:42'),
(23, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-11', '13:32:00', '14:32:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 07:59:29', '2026-04-02 07:59:30'),
(24, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-10', '13:37:00', '14:37:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 08:04:32', '2026-04-02 08:04:33'),
(25, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-25', '13:58:00', '14:58:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 08:25:21', '2026-04-02 08:25:22'),
(26, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-05-14', '10:08:00', '11:08:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 16:35:13', '2026-04-02 16:35:14'),
(27, 21, 36, 134, 91, 'Meeting: Meeting Request', NULL, '2026-04-16', '12:11:00', '13:11:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 18:41:27', '2026-04-02 18:41:28'),
(28, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-11', '01:38:00', '02:38:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 20:06:56', '2026-04-02 20:06:57'),
(29, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-10', '13:43:00', '14:43:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 20:12:07', '2026-04-02 20:12:08'),
(30, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-21', '03:42:00', '03:57:00', 15, 'online', NULL, 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 20:13:05', '2026-04-02 20:13:06'),
(31, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-02', '01:46:00', '02:46:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 20:14:46', '2026-04-02 20:14:47'),
(32, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-10', '01:50:00', '02:50:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 20:19:55', '2026-04-02 20:19:55'),
(33, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-10', '01:50:00', '02:50:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 20:20:16', '2026-04-02 20:20:17'),
(34, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-10', '01:50:00', '02:50:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 20:21:00', '2026-04-02 20:21:00'),
(35, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-10', '01:54:00', '02:54:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 20:23:01', '2026-04-02 20:23:02'),
(36, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-10', '01:54:00', '02:54:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 20:26:42', '2026-04-02 20:26:43'),
(37, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-10', '01:54:00', '02:54:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 20:29:00', '2026-04-02 20:29:01'),
(38, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-10', '02:04:00', '03:04:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 20:32:27', '2026-04-02 20:32:28'),
(39, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-10', '02:08:00', '03:08:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 20:37:56', '2026-04-02 20:37:56'),
(40, 21, 26, 74, NULL, 'Meeting: Help is needed', NULL, '2026-04-11', '02:11:00', '03:11:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-02 20:39:43', '2026-04-02 20:39:44'),
(41, 21, 36, 134, 119, 'Meeting: Meeting Request', NULL, '2026-05-16', '11:42:00', '11:57:00', 15, 'online', 'S104', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, 'Bring a book', '2026-04-03 06:13:21', '2026-04-03 06:13:22'),
(42, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-11', '07:52:00', '08:07:00', 15, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/81347916082?pwd=5xVlzx2gpnnZFheGqH93YXxoD3ugbv.1  Meeting ID: 813 4791 6082 Passcode: 6TZt9t', NULL, NULL, '2026-04-03 14:23:09', '2026-04-03 14:23:10'),
(43, 21, 36, 134, 123, 'Meeting: Meeting Request', NULL, '2026-04-10', '08:01:00', '08:16:00', 15, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-03 14:32:22', '2026-04-03 14:32:23'),
(44, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-04', '08:12:00', '09:12:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, NULL, '2026-04-03 14:43:09', '2026-04-03 14:43:10'),
(45, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-05-01', '08:21:00', '09:21:00', 60, 'online', 'E401', 'individual', 'scheduled', 'https://us05web.zoom.us/j/81347916082?pwd=5xVlzx2gpnnZFheGqH93YXxoD3ugbv.1  Meeting ID: 813 4791 6082 Passcode: 6TZt9t', NULL, NULL, '2026-04-03 14:52:17', '2026-04-03 14:52:18'),
(46, 21, 12, 75, 129, 'Meeting: Meeting Request', NULL, '2026-04-25', '17:15:00', '17:30:00', 15, 'online', NULL, 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, 'Keep a book', '2026-04-03 18:42:49', '2026-04-03 18:42:50'),
(47, 21, 36, 134, NULL, 'Meeting: Meeting Request', NULL, '2026-04-18', '16:20:00', '17:20:00', 60, 'online', NULL, 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, 'keep a book', '2026-04-03 18:51:15', '2026-04-03 18:51:16'),
(48, 21, 26, 74, 133, 'Meeting: Help is needed', NULL, '2026-06-04', '13:00:00', '13:15:00', 15, 'online', NULL, 'individual', 'scheduled', 'https://us05web.zoom.us/j/81347916082?pwd=5xVlzx2gpnnZFheGqH93YXxoD3ugbv.1  Meeting ID: 813 4791 6082 Passcode: 6TZt9t', NULL, 'Don\'t miss the meeting', '2026-04-04 07:36:06', '2026-04-04 07:36:07'),
(49, 21, 36, 134, 135, 'Meeting: Meeting Request', NULL, '2026-05-14', '13:15:00', '13:30:00', 15, 'online', NULL, 'individual', 'scheduled', 'https://us05web.zoom.us/j/81347916082?pwd=5xVlzx2gpnnZFheGqH93YXxoD3ugbv.1  Meeting ID: 813 4791 6082 Passcode: 6TZt9t', NULL, 'keep a book', '2026-04-04 07:46:11', '2026-04-04 07:46:12'),
(50, 21, 12, 75, 137, 'Meeting: Meeting Request', NULL, '2026-04-11', '13:25:00', '14:25:00', 60, 'online', NULL, 'individual', 'scheduled', 'https://us05web.zoom.us/j/81347916082?pwd=5xVlzx2gpnnZFheGqH93YXxoD3ugbv.1  Meeting ID: 813 4791 6082 Passcode: 6TZt9t', NULL, 'vbn', '2026-04-04 07:55:22', '2026-04-04 07:55:23'),
(51, 21, 12, 75, 139, 'Meeting: Meeting Request', NULL, '2026-04-26', '13:37:00', '14:07:00', 30, 'online', NULL, 'individual', 'scheduled', 'https://us05web.zoom.us/j/81347916082?pwd=5xVlzx2gpnnZFheGqH93YXxoD3ugbv.1  Meeting ID: 813 4791 6082 Passcode: 6TZt9t', NULL, 'afbn', '2026-04-04 08:08:02', '2026-04-04 08:08:03'),
(52, 21, 12, 75, 141, 'Meeting: Meeting Request', NULL, '2026-04-30', '15:30:00', '15:45:00', 15, 'online', NULL, 'individual', 'scheduled', 'https://us05web.zoom.us/j/82922297670?pwd=xB0P5dNoXHbjAb0kJvRannA1LStvMm.1', NULL, 'Join Zoom Meeting\nhttps://us05web.zoom.us/j/82922297670?pwd=xB0P5dNoXHbjAb0kJvRannA1LStvMm.1\n\nMeeting ID: 829 2229 7670\nPasscode: 7mEm9g', '2026-04-04 08:21:33', '2026-04-04 08:21:34'),
(53, 21, 12, 75, NULL, 'Meeting: Meeting Request', NULL, '2026-05-07', '14:00:00', '15:00:00', 60, 'online', NULL, 'individual', 'scheduled', 'https://us05web.zoom.us/j/82922297670?pwd=xB0P5dNoXHbjAb0kJvRannA1LStvMm.1', NULL, 'Join Zoom Meeting\nMeeting ID: 829 2229 7670\nPasscode: 7mEm9g', '2026-04-04 08:30:52', '2026-04-04 08:30:53'),
(54, 21, 12, 75, NULL, 'Meeting: Meeting Request', NULL, '2026-04-18', '14:07:00', '15:07:00', 60, 'online', NULL, 'individual', 'scheduled', 'https://us05web.zoom.us/j/82922297670?pwd=xB0P5dNoXHbjAb0kJvRannA1LStvMm.1', NULL, 'Meeting ID: 829 2229 7670\nPasscode: 7mEm9g', '2026-04-04 08:38:25', '2026-04-04 08:38:26'),
(55, 21, 12, 75, 147, 'Meeting: Meeting Request', NULL, '2026-05-08', '14:19:00', '15:19:00', 60, 'online', NULL, 'individual', 'scheduled', 'https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1', NULL, 'Meeting ID: 829 2229 7670\nPasscode: 7mEm9g', '2026-04-04 08:50:50', '2026-04-04 08:50:51'),
(56, 21, 36, 134, 149, 'Meeting: Meeting Request', NULL, '2026-07-04', '14:37:00', '14:52:00', 15, 'online', NULL, 'individual', 'scheduled', 'https://us05web.zoom.us/j/82922297670?pwd=xB0P5dNoXHbjAb0kJvRannA1LStvMm.1', NULL, 'Meeting ID: 829 2229 7670, Passcode: 7mEm9g', '2026-04-04 09:08:27', '2026-04-04 09:08:28'),
(57, 21, 36, 134, 151, 'Meeting: Meeting Request', NULL, '2026-05-09', '14:44:00', '14:59:00', 15, 'online', NULL, 'individual', 'scheduled', 'https://us05web.zoom.us/j/82922297670?pwd=xB0P5dNoXHbjAb0kJvRannA1LStvMm.1', NULL, 'Meeting ID: 829 2229 7670, Passcode: 7mEm9g', '2026-04-04 09:15:51', '2026-04-04 09:15:52');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `news_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `topic` varchar(100) NOT NULL,
  `contact_num` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news_document`
--

CREATE TABLE `news_document` (
  `news_id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notif_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `type` enum('ticket_status','ticket_assigned','ticket_response','ticket_message','meeting_scheduled','meeting_updated','meeting_cancelled','announcement','forum_reply','lost_found_match') NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL COMMENT 'e.g. ticket, meeting, announcement, forum',
  `entity_id` int(11) DEFAULT NULL COMMENT 'FK to the relevant entity (ticket_id, meeting_id, etc.)',
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notif_id`, `u_id`, `type`, `entity_type`, `entity_id`, `message`, `is_read`, `created_at`) VALUES
(1, 8, 'ticket_assigned', 'ticket', 137, 'Brian has been assigned to \"feedback\"', 0, '2026-04-04 23:51:13'),
(2, 8, 'ticket_status', 'ticket', 137, 'Your ticket \"feedback\" is now Resolved', 0, '2026-04-04 23:51:26');

-- --------------------------------------------------------

--
-- Table structure for table `reply_q`
--

CREATE TABLE `reply_q` (
  `q_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `response` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `staff_id` varchar(191) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_division`
--

CREATE TABLE `staff_division` (
  `u_id` int(11) NOT NULL,
  `did` int(11) NOT NULL,
  `h_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_division`
--

INSERT INTO `staff_division` (`u_id`, `did`, `h_id`) VALUES
(1, 2, 5),
(2, 3, 7),
(10, 5, 10),
(14, 9, NULL),
(16, 5, NULL),
(17, 4, NULL),
(19, 5, 11),
(29, 9, NULL),
(30, 5, 9),
(31, 10, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `staff_hierachy`
--

CREATE TABLE `staff_hierachy` (
  `h_id` int(11) NOT NULL,
  `did` int(11) NOT NULL,
  `position` enum('DR','DB','NM','SAR','AR','SAB','AL','WE','ANM','MA/SA','TO/SMA') NOT NULL,
  `level` enum('1','2','3') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_hierachy`
--

INSERT INTO `staff_hierachy` (`h_id`, `did`, `position`, `level`) VALUES
(1, 1, 'DR', '1'),
(2, 1, 'SAR', '2'),
(3, 1, 'MA/SA', '3'),
(4, 2, 'DR', '1'),
(5, 2, 'SAR', '2'),
(6, 2, 'MA/SA', '3'),
(7, 3, 'SAR', '2'),
(8, 3, 'MA/SA', '3'),
(9, 5, 'DR', '1'),
(10, 5, 'SAR', '2'),
(11, 5, 'MA/SA', '3'),
(12, 7, 'DB', '1'),
(13, 7, 'SAB', '2'),
(14, 7, 'MA/SA', '3'),
(15, 8, 'AL', '2'),
(16, 8, 'MA/SA', '3'),
(17, 6, 'WE', '2'),
(18, 6, 'TO/SMA', '3'),
(19, 9, 'NM', '1'),
(20, 9, 'ANM', '2'),
(21, 9, 'TO/SMA', '3');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reg_number` varchar(191) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `reg_number`) VALUES
(3, 6, '23001364'),
(5, 9, '23000139'),
(6, 13, '23002212'),
(7, 14, '123456789');

-- --------------------------------------------------------

--
-- Table structure for table `supporting_documents`
--

CREATE TABLE `supporting_documents` (
  `ticket_id` int(11) NOT NULL,
  `doc_name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `uploaded_at` date DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE `templates` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'not entered ',
  `fields` text NOT NULL,
  `letter_required` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `division` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`id`, `name`, `category`, `fields`, `letter_required`, `created_at`, `created_by`, `division`) VALUES
(3, 'Wi-Fi Password Reset', 'CSC and NOC', '[\"student_id\", \"email\", \"mac_address\"]', 1, '2025-08-30 20:23:36', 1, 9),
(4, 'Lost Student ID Card', 'Examination and Registration', '[\"index_number\", \"nic_copy\"]', 1, '2025-08-30 20:23:36', 1, 5),
(5, 'Wifi-Device-Reissue', 'CSC and NOC', '[\"Student ID\",\"Degree Program\",\"Device MAC address\",\"New Device MAC Address\"]', 1, '2025-10-23 06:43:12', 10, 9),
(6, 'Overseas Travel Notification', 'Examination and Registration', '[\"Student ID\",\"Year of Study\",\"Program\",\"Contact Details While Abroad (submit a copy of VISA )\"]', 1, '2025-10-23 06:47:53', 10, 5),
(7, 'Scholarship Application', 'Finance', '[\"Student ID\",\"Current GPA with Academic Transcript\",\"Income certificate\"]', 1, '2025-10-23 06:53:17', 10, 7),
(16, 'please work this time', 'Examination and Registration', '[\"mono\",\"polly\"]', 0, '2025-11-22 04:50:35', 10, 5);

-- --------------------------------------------------------

--
-- Table structure for table `template_submissions`
--

CREATE TABLE `template_submissions` (
  `submission_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `data` text NOT NULL,
  `generated_letter` varchar(500) DEFAULT NULL,
  `ticket_id` int(11) NOT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int(11) NOT NULL,
  `t_type` enum('private','public','template') NOT NULL DEFAULT 'private',
  `created_at` datetime DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `u_id` int(11) DEFAULT NULL,
  `division` int(10) DEFAULT NULL,
  `status` enum('pending','agent assigned','resolved','agent-closed','closed') DEFAULT 'pending',
  `priority` enum('high','medium','low') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `meeting_requested` varchar(20) DEFAULT NULL,
  `assigned_to` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`ticket_id`, `t_type`, `created_at`, `title`, `u_id`, `division`, `status`, `priority`, `description`, `meeting_requested`, `assigned_to`) VALUES
(1, 'private', '2025-08-16 15:07:05', 'Course material access issue', 1, 2, 'pending', 'high', 'Cannot access uploaded lecture slides', 'Requested', NULL),
(2, 'private', '2025-08-16 15:07:05', 'Grade appeal request', 1, 4, 'resolved', 'medium', 'Appealing grade for Assignment 2', NULL, NULL),
(3, 'private', '2025-08-16 15:07:05', 'Exam schedule conflict', 1, 9, 'pending', 'low', 'Exam date clashes with another registered course', NULL, 9),
(4, 'private', '2025-08-31 08:00:00', 'Course material access issue', NULL, 5, 'pending', 'high', 'Cannot access uploaded lecture slides', 'Requested', NULL),
(5, 'private', '2025-08-31 08:05:00', 'Grade appeal request', NULL, 5, 'resolved', 'medium', 'Appealing grade for Assignment 2', NULL, NULL),
(6, 'private', '2025-08-31 08:10:00', 'Exam schedule conflict', 5, 8, 'pending', 'low', 'Exam date clashes with another registered course', NULL, NULL),
(13, 'private', '2025-08-31 11:33:05', 'Can\'t login to ugvle', 8, 7, 'pending', 'high', 'Pretty much the title says', 'Requested', NULL),
(14, 'private', '2025-08-31 13:05:22', 'No time to do assignment, need like 2 3 extra months', 6, 1, 'pending', 'high', 'Yall seriously expects us to do all of this', 'Requested', NULL),
(20, 'private', '2025-09-01 12:16:31', 'Rats infesting', 6, 6, 'pending', 'medium', 'saw a lot of rats and a corpse of rats with bad smell near lecture hall E401', NULL, NULL),
(21, 'private', '2025-09-02 16:29:21', 'research document publication', 8, 4, 'pending', 'medium', 'whats the deadline to publish my research ?', NULL, NULL),
(24, 'private', '2025-10-15 16:31:48', 'Library AC', 8, 8, 'pending', 'high', 'can we control the lab AC ? sometimes its too freezing ?', 'Requested', NULL),
(33, 'private', '2025-10-18 16:51:33', 'Document is needed', 8, 3, 'pending', 'medium', 'I need to show my verifications to BOC bank as a student for my internship', 'Requested', NULL),
(34, 'private', '2025-10-18 16:52:17', 'results', 8, 5, 'agent-closed', 'medium', 'results', NULL, 10),
(35, 'private', '2025-10-18 16:58:49', 'Abstract submission deadline ', 8, 4, 'pending', 'medium', 'i missed my deadline to submit the Abstract. what can i do now ?', NULL, NULL),
(36, 'private', '2025-10-18 16:59:05', 'Library access', 8, 8, 'agent assigned', 'medium', 'can we borrow books now ? is the updating still going on ?', NULL, 10),
(37, 'private', '2025-10-19 11:37:02', 'Medical Submission', 8, 5, 'agent assigned', 'medium', 'Im having some trouble submitting medical documents. is there any other method i can try ?', NULL, 19),
(38, 'private', '2025-10-19 11:38:07', 'Registration Issue', 8, 5, 'resolved', 'medium', 'I cannot register for my elective modules', NULL, 10),
(39, 'private', '2025-10-19 12:55:12', 'Exam Issue 1', 8, 5, 'agent assigned', 'medium', 'test', NULL, 19),
(40, 'private', '2025-10-19 22:19:36', 'Wifi-for my new laptop', 8, 9, 'agent assigned', 'medium', 'I need new wifi access to my new laptop. how can i make a request ', NULL, 10),
(41, 'private', '2025-10-19 22:51:57', 'Semester registration', 8, 5, 'agent assigned', 'medium', 'i have some isssue with my semester registration. is it an issue with the web site ?', NULL, 19),
(42, 'private', '2025-10-21 04:31:36', 'Results', 6, 5, 'resolved', 'medium', 'It is showing an issue with resulsts. i cannot see my results', 'Requested', 19),
(43, 'private', '2025-10-22 11:46:41', 'Relationship Issue', 6, 10, 'pending', 'medium', 'I need help , so how this going to work ? just need idk , can i have a meeting ? ', 'Requested', NULL),
(46, 'private', '2025-10-22 15:24:57', 'Cant sleep', 8, 10, 'pending', 'medium', 'Im stressed so much , i just cant sleep at night. any tips ? or articles that i can try to read and find help?', NULL, NULL),
(48, 'private', '2025-10-22 17:46:16', 'Results of Module SCS 1306', 24, 5, 'agent assigned', 'medium', 'i want to submit re correction how can i do it ?', NULL, 19),
(49, 'private', '2025-10-22 17:48:03', 'Medical Submission', 24, 5, 'agent assigned', 'medium', 'i missed my exam module SCS2506 , and i need to submit a medical document. how can i do it ?', NULL, 19),
(51, 'private', '2025-10-22 17:56:17', 'Forgot student ID during exams', 25, 5, 'agent assigned', 'medium', 'i lost my ID. so how can i proceed with exams? what are those forms they give us ? i posted a not on lost and found section as well', NULL, 19),
(52, 'private', '2025-10-22 17:58:28', 'Exam timetable/seat allocation inquiries', 26, 5, 'pending', 'medium', 'i cannot find my seat in the exams halls. is it changed ?', NULL, NULL),
(53, 'private', '2025-10-22 17:59:51', 'GPA calculation issues', 26, 5, 'agent assigned', 'medium', 'i think my GPA has calculated wrongly. can i make a request to re calculate ?', NULL, 19),
(55, 'private', '2025-10-22 18:06:17', 'Requests for academic transcripts / results sheets', 27, 5, 'agent assigned', 'medium', 'Requests for academic transcripts / results sheets . how can i proceed ?', NULL, 19),
(56, 'private', '2025-10-22 18:10:40', 'course registration corrections', 27, 5, 'agent assigned', 'medium', 'it seems like i have registerd to in correct modules . so how can make a request to change it ?', NULL, 10),
(57, 'private', '2025-10-22 18:11:31', 'Exam results inquiries', 27, 5, 'pending', 'medium', 'my re sitting results are not publshied yet . is there any issue ??', NULL, NULL),
(58, 'private', '2025-10-22 18:12:22', 'Student welfare', 27, 3, 'pending', 'medium', 'how can i apply for student welfare program ? whats the procedure ?', NULL, NULL),
(59, 'private', '2025-10-22 18:15:24', 'VLE issue', 27, 5, 'agent assigned', 'medium', 'Technical help during online exams is needed for me. its always exams period', NULL, 10),
(60, 'private', '2025-10-22 18:16:26', 'water leak from AC', 27, 6, 'pending', 'medium', 'water is leaking in E401 AC', NULL, NULL),
(61, 'private', '2025-10-22 18:21:57', 'Repeat registration', 28, 5, 'agent-closed', 'medium', 'i have this issue with registering for repeat module. who should i contact , what should i do ?', NULL, 10),
(62, 'private', '2025-10-22 18:23:14', 'Requests for official letters', 28, 5, 'agent-closed', 'medium', 'i need some documents letter to show that im currently an undergraduate in ucsc', NULL, 10),
(63, 'private', '2025-10-22 18:26:22', 'Exam Excuse Documents', 28, 5, 'agent-closed', 'medium', 'im currently not well , so how can i submit an medical document ?', NULL, 19),
(64, 'private', '2025-10-22 18:27:25', 'Library fine', 28, 8, 'pending', 'medium', 'i have this fine to pay since for 6 months. to whom should i pay it ?', NULL, NULL),
(72, 'private', '2025-10-23 18:57:09', 'Need a Meeting', 24, 10, 'pending', 'medium', 'Im going through a lot quite now , so its good if i can have a meeting .anytime', 'Requested', NULL),
(74, 'private', '2025-10-23 18:59:33', 'Help is needed', 26, 10, 'pending', 'medium', 'i just need someone to talk to. can i have a meeting?', 'Requested', NULL),
(75, 'private', '2025-10-23 19:02:04', 'Meeting Request', 12, 10, 'pending', 'medium', 'i have very personal issue to discuss can i have a meeting ?', 'Requested', NULL),
(82, 'template', '2025-11-22 07:17:43', 'please work this time', 24, 5, 'agent assigned', 'medium', '[1]. Mono: lets see if this works\n[2]. Polly: hopefully working this time , at last plss work\n', NULL, 10),
(84, 'template', '2025-12-13 10:34:10', 'please work this time', 24, 5, 'resolved', 'medium', '[1]. Mono: testing the new heirachy\n[2]. Polly: testings\n', NULL, 0),
(85, 'private', '2025-12-18 04:42:15', 'see if works', 24, 5, 'resolved', 'medium', 'asda', NULL, 10),
(88, 'public', '2025-12-22 14:22:56', 'This has chat messages', 8, 5, 'agent assigned', 'medium', 'test', NULL, 30),
(93, 'private', '2026-01-17 17:50:04', 'email checking part 3', 24, 5, 'pending', 'medium', 'checking the email speed', NULL, NULL),
(94, 'private', '2026-01-18 18:07:14', 'Final Email Checking', 24, 5, 'agent assigned', 'medium', 'if this works. im happy then . it takes 20 seconds to re-load, but okay its fine', NULL, 19),
(95, 'private', '2026-02-02 15:03:52', 'Testing after some major changes', 8, 5, 'pending', 'medium', 'testing', NULL, NULL),
(96, 'private', '2025-02-10 09:30:00', 'Academic stress management help', 5, 10, 'pending', 'high', 'Feeling overwhelmed with coursework and need guidance', 'Requested', 3),
(97, 'private', '2025-02-11 10:15:00', 'Career path guidance needed', 7, 10, 'agent assigned', 'medium', 'Unsure about career direction after graduation', 'Scheduled', 3),
(98, 'private', '2025-02-12 14:20:00', 'Study skills improvement', 8, 10, 'resolved', 'low', 'Want to learn better study techniques', NULL, 4),
(99, 'private', '2025-02-13 11:00:00', 'Time management concerns', 9, 10, 'pending', 'medium', 'Struggling to balance study and personal life', NULL, NULL),
(100, 'private', '2025-02-14 15:45:00', 'Exam anxiety counselling', 10, 10, 'agent assigned', 'high', 'Severe anxiety before exams affecting performance', 'Requested', 3),
(101, 'private', '2025-01-15 09:00:00', 'Course selection advice', 11, 10, 'resolved', 'medium', 'Need help choosing elective courses', NULL, 4),
(102, 'private', '2025-01-18 13:30:00', 'Conflict with roommate', 12, 10, 'resolved', 'high', 'Having issues with accommodation', 'Scheduled', 3),
(103, 'private', '2025-01-22 10:45:00', 'Financial stress support', 13, 10, 'resolved', 'medium', 'Worried about managing expenses', NULL, 4),
(104, 'private', '2025-01-25 14:00:00', 'Family problems affecting studies', 14, 10, 'resolved', 'high', 'Personal family issues impacting academic performance', 'Scheduled', 3),
(105, 'private', '2025-01-28 16:20:00', 'Social anxiety help', 15, 10, 'agent-closed', 'low', 'Difficulty making friends and socializing', NULL, 4),
(106, 'private', '2024-12-05 11:00:00', 'Depression screening', 16, 10, 'resolved', 'high', 'Feeling persistently sad and unmotivated', 'Scheduled', 3),
(107, 'private', '2024-12-10 09:30:00', 'Academic probation counselling', 17, 10, 'resolved', 'high', 'On academic probation and need support', 'Requested', 4),
(108, 'private', '2024-12-15 14:45:00', 'Homesickness support', 18, 10, 'resolved', 'medium', 'Missing home and family, affecting wellbeing', NULL, 3),
(109, 'private', '2024-12-20 10:00:00', 'Relationship counselling', 19, 10, 'agent-closed', 'low', 'Relationship issues affecting concentration', NULL, 4),
(110, 'private', '2024-12-28 13:15:00', 'Goal setting assistance', 20, 10, 'resolved', 'medium', 'Need help setting realistic academic goals', NULL, 3),
(111, 'private', '2025-02-10 09:30:00', 'Academic stress management help', 5, 10, 'pending', 'high', 'Feeling overwhelmed with coursework and need guidance', 'Requested', 3),
(112, 'private', '2025-02-11 10:15:00', 'Career path guidance needed', 7, 10, 'agent assigned', 'medium', 'Unsure about career direction after graduation', 'Scheduled', 3),
(113, 'private', '2025-02-12 14:20:00', 'Study skills improvement', 8, 10, 'resolved', 'low', 'Want to learn better study techniques', NULL, 4),
(114, 'private', '2025-02-13 11:00:00', 'Time management concerns', 9, 10, 'pending', 'medium', 'Struggling to balance study and personal life', NULL, NULL),
(115, 'private', '2025-02-14 15:45:00', 'Exam anxiety counselling', 10, 10, 'agent assigned', 'high', 'Severe anxiety before exams affecting performance', 'Requested', 3),
(116, 'private', '2025-01-15 09:00:00', 'Course selection advice', 11, 10, 'resolved', 'medium', 'Need help choosing elective courses', NULL, 4),
(117, 'private', '2025-01-18 13:30:00', 'Conflict with roommate', 12, 10, 'resolved', 'high', 'Having issues with accommodation', 'Scheduled', 3),
(118, 'private', '2025-01-22 10:45:00', 'Financial stress support', 13, 10, 'resolved', 'medium', 'Worried about managing expenses', NULL, 4),
(119, 'private', '2025-01-25 14:00:00', 'Family problems affecting studies', 14, 10, 'resolved', 'high', 'Personal family issues impacting academic performance', 'Scheduled', 3),
(120, 'private', '2025-01-28 16:20:00', 'Social anxiety help', 15, 10, 'agent-closed', 'low', 'Difficulty making friends and socializing', NULL, 4),
(121, 'private', '2024-12-05 11:00:00', 'Depression screening', 16, 10, 'resolved', 'high', 'Feeling persistently sad and unmotivated', 'Scheduled', 3),
(122, 'private', '2024-12-10 09:30:00', 'Academic probation counselling', 17, 10, 'resolved', 'high', 'On academic probation and need support', 'Requested', 4),
(123, 'private', '2024-12-15 14:45:00', 'Homesickness support', 18, 10, 'resolved', 'medium', 'Missing home and family, affecting wellbeing', NULL, 3),
(124, 'private', '2024-12-20 10:00:00', 'Relationship counselling', 19, 10, 'agent-closed', 'low', 'Relationship issues affecting concentration', NULL, 4),
(125, 'private', '2024-12-28 13:15:00', 'Goal setting assistance', 20, 10, 'resolved', 'medium', 'Need help setting realistic academic goals', NULL, 3),
(126, 'private', '2026-02-17 06:55:00', 'dfd', 24, 5, 'pending', 'medium', 'asdaad', NULL, NULL),
(127, 'private', '2026-04-01 14:10:38', 'test uploads', 8, 5, 'pending', 'medium', 'test', NULL, NULL),
(128, 'private', '2026-04-01 14:22:40', 'test', 8, 5, 'pending', 'medium', 'test', NULL, NULL),
(129, 'private', '2026-04-01 14:23:14', 'test', 8, 5, 'pending', 'medium', 'test', NULL, NULL),
(130, 'private', '2026-04-01 14:23:43', 'test', 8, 5, 'pending', 'medium', 'test', NULL, NULL),
(131, 'private', '2026-04-01 14:27:02', 'deee', 8, 3, 'pending', 'medium', 'ecec', NULL, NULL),
(132, 'private', '2026-04-01 14:31:24', 'd', 8, 3, 'pending', 'medium', 'edd', NULL, NULL),
(133, 'private', '2026-04-01 14:34:03', 'uploads', 8, 5, 'agent assigned', 'medium', 'test', NULL, 30),
(134, 'private', '2026-04-01 16:39:56', 'Meeting Request', 36, 10, 'pending', 'medium', 'Relationship problems', 'Requested', NULL),
(135, 'private', '2026-04-04 12:35:07', 'counselling meeting check', 24, 10, 'pending', 'medium', 'sds', 'Requested', NULL),
(136, 'private', '2026-04-05 01:31:06', 'feedback test', 8, 10, 'pending', 'medium', 'nm', 'Requested', NULL),
(137, 'private', '2026-04-05 01:47:57', 'feedback', 8, 5, 'resolved', 'medium', 'hey', NULL, 30);

--
-- Triggers `tickets`
--
DELIMITER $$
CREATE TRIGGER `trg_ticket_timeline_after_ticket_insert` AFTER INSERT ON `tickets` FOR EACH ROW BEGIN
  INSERT INTO ticket_timeline (ticket_id, created)
  VALUES (NEW.ticket_id, NEW.created_at);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_chat`
--

CREATE TABLE `ticket_chat` (
  `chat_id` int(11) NOT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `u1_id` int(11) NOT NULL,
  `u2_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket_chat`
--

INSERT INTO `ticket_chat` (`chat_id`, `ticket_id`, `u1_id`, `u2_id`, `created_at`) VALUES
(1, 88, 8, 30, '2025-12-25 13:57:19'),
(2, 85, 24, 10, '2026-01-02 15:21:41'),
(3, 94, 24, 19, '2026-02-16 07:11:19'),
(4, 134, 36, 21, '2026-04-02 22:11:26'),
(5, 74, 26, 21, '2026-04-02 22:39:47'),
(6, 75, 12, 21, '2026-04-03 16:10:38'),
(7, 46, 8, 2, '2026-04-05 01:31:21');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_messages`
--

CREATE TABLE `ticket_messages` (
  `id` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `message_type` enum('text','photo','video','file') NOT NULL DEFAULT 'text',
  `reply_to_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket_messages`
--

INSERT INTO `ticket_messages` (`id`, `chat_id`, `sender_id`, `message`, `message_type`, `reply_to_id`, `is_read`, `created_at`) VALUES
(11, 2, 24, 'nm', 'text', NULL, 1, '2026-01-02 15:21:44'),
(12, 2, 10, 'hemlo', 'text', NULL, 1, '2026-01-02 15:38:52'),
(13, 1, 8, 'hey', 'text', NULL, 1, '2026-01-15 18:33:21'),
(14, 1, 30, 'wyd', 'text', NULL, 1, '2026-01-15 18:35:01'),
(15, 1, 8, 'nm', 'text', NULL, 1, '2026-01-15 18:35:33'),
(16, 1, 30, 'ok', 'text', NULL, 1, '2026-01-15 18:36:00'),
(17, 1, 8, 'hehe', 'text', NULL, 0, '2026-02-02 18:36:19'),
(18, 3, 24, 'hello dularaaa', 'text', NULL, 1, '2026-02-16 07:11:20'),
(19, 3, 19, 'hi', 'text', NULL, 1, '2026-02-16 07:11:32'),
(20, 4, 21, 'bkj', 'text', NULL, 1, '2026-04-02 22:11:29'),
(21, 4, 21, 'hii student', 'text', NULL, 1, '2026-04-02 22:13:50'),
(22, 4, 21, 'at wht time', 'text', NULL, 1, '2026-04-02 22:21:57'),
(23, 4, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Meeting Request\nDate: 2026-04-10\nTime: 01:54\nMode: online\nVenue: E401\nMeeting Link: https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1\n(Meeting Scheduled ID: 37)', 'text', NULL, 1, '2026-04-02 22:29:04'),
(24, 4, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Meeting Request\nDate: 2026-04-10\nTime: 02:04\nMode: online\nVenue: E401\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=38\n(Meeting Scheduled ID: 38)', 'text', NULL, 1, '2026-04-02 22:32:32'),
(25, 4, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Meeting Request\nDate: 2026-04-10\nTime: 02:08\nMode: online\nVenue: E401\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=39\n(Meeting Scheduled ID: 39)', 'text', NULL, 1, '2026-04-02 22:37:59'),
(26, 5, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Help is needed\nDate: 2026-04-11\nTime: 02:11\nMode: online\nVenue: E401\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=40\n(Meeting Scheduled ID: 40)', 'text', NULL, 1, '2026-04-02 22:39:49'),
(27, 5, 21, 'hi', 'text', NULL, 1, '2026-04-02 22:41:01'),
(28, 4, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Meeting Request\nDate: 2026-05-16\nTime: 11:42\nMode: online\nVenue: S104\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=41\n(Meeting Scheduled ID: 41)', 'text', NULL, 1, '2026-04-03 08:13:26'),
(29, 4, 36, 'thank you madam', 'text', NULL, 1, '2026-04-03 09:23:00'),
(30, 6, 21, 'Meeting reminder: Your scheduled session is due now.\nTitle: Meeting: Meeting Request\nDate: 2026-02-19\nTime: 10:53\nMode: online\nVenue: E401\n(Meeting ID: 7)', 'text', NULL, 1, '2026-04-03 16:10:40'),
(31, 6, 21, 'Meeting reminder: Your scheduled session is due now.\nTitle: Meeting: Meeting Request\nDate: 2026-03-12\nTime: 00:24\nMode: online\nVenue: hj\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=8\n(Meeting ID: 8)', 'text', NULL, 1, '2026-04-03 16:10:45'),
(32, 4, 21, 'Meeting reminder: Your scheduled session is due now.\nTitle: Meeting: Meeting Request\nDate: 2026-04-02\nTime: 01:46\nMode: online\nVenue: E401\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=31\n(Meeting ID: 31)', 'text', NULL, 1, '2026-04-03 16:10:49'),
(33, 4, 21, 'Meeting reminder: Your scheduled session is due now.\nTitle: Meeting: Meeting Request\nDate: 2026-04-03\nTime: 11:40\nMode: online\nVenue: E401\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=12\n(Meeting ID: 12)', 'text', NULL, 1, '2026-04-03 16:10:53'),
(34, 4, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Meeting Request\nDate: 2026-04-11\nTime: 07:52\nMode: online\nVenue: E401\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=42\n(Meeting Scheduled ID: 42)', '', NULL, 1, '2026-04-03 16:23:13'),
(35, 4, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Meeting Request\nDate: 2026-04-10\nTime: 08:01\nMode: online\nVenue: E401\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=43\n(Meeting Scheduled ID: 43)', '', NULL, 1, '2026-04-03 16:32:27'),
(36, 4, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Meeting Request\nDate: 2026-04-04\nTime: 08:12\nMode: online\nVenue: E401\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=44\n(Meeting Scheduled ID: 44)', '', NULL, 1, '2026-04-03 16:43:14'),
(37, 4, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Meeting Request\nDate: 2026-05-01\nTime: 08:21\nMode: online\nVenue: E401\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=45\n(Meeting Scheduled ID: 45)', '', NULL, 1, '2026-04-03 16:52:24'),
(38, 6, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Meeting Request\nDate: 2026-04-25\nTime: 17:15\nMode: online\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=46\n(Meeting Scheduled ID: 46)', 'text', NULL, 1, '2026-04-03 20:42:54'),
(39, 4, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Meeting Request\nDate: 2026-04-18\nTime: 16:20\nMode: online\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=47\nNotes: keep a book\n(Meeting Scheduled ID: 47)', 'text', NULL, 1, '2026-04-03 20:51:19'),
(40, 4, 21, 'hi', 'text', NULL, 1, '2026-04-03 20:59:14'),
(41, 4, 21, 'Meeting reminder: Your scheduled session is due now.\nTitle: Meeting: Meeting Request\nDate: 2026-04-04\nTime: 08:12\nMode: online\nVenue: E401\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=44\n(Meeting ID: 44)', 'text', NULL, 1, '2026-04-04 09:27:30'),
(42, 4, 21, 'Meeting reminder: Your scheduled session is due now.\nTitle: Meeting: Meeting Request\nDate: 2026-04-04\nTime: 09:39\nMode: online\nVenue: E401\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=17\n(Meeting ID: 17)', 'text', NULL, 1, '2026-04-04 09:27:34'),
(43, 5, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Help is needed\nDate: 2026-06-04\nTime: 13:00\nMode: online\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=48\nNotes: Don\\\'t miss the meeting\n(Meeting Scheduled ID: 48)', 'text', NULL, 1, '2026-04-04 09:36:11'),
(44, 4, 21, 'hi', 'text', NULL, 1, '2026-04-04 09:43:38'),
(45, 4, 21, 'hi', 'text', NULL, 1, '2026-04-04 09:45:28'),
(46, 4, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Meeting Request\nDate: 2026-05-14\nTime: 13:15\nMode: online\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=49\nNotes: keep a book\n(Meeting Scheduled ID: 49)', 'text', NULL, 1, '2026-04-04 09:46:16'),
(47, 6, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Meeting Request\nDate: 2026-04-11\nTime: 13:25\nMode: online\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=50\nNotes: vbn\n(Meeting Scheduled ID: 50)', 'text', NULL, 1, '2026-04-04 09:55:27'),
(48, 6, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Meeting Request\nDate: 2026-04-26\nTime: 13:37\nMode: online\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=51\nNotes: afbn\n(Meeting Scheduled ID: 51)', 'text', NULL, 1, '2026-04-04 10:08:29'),
(49, 6, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Meeting Request\nDate: 2026-04-30\nTime: 15:30\nMode: online\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=52\nNotes: Join Zoom Meeting\\nhttps://us05web.zoom.us/j/82922297670?pwd=xB0P5dNoXHbjAb0kJvRannA1LStvMm.1\\n\\nMeeting ID: 829 2229 7670\\nPasscode: 7mEm9g\n(Meeting Scheduled ID: 52)', 'text', NULL, 1, '2026-04-04 10:21:37'),
(50, 6, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Meeting Request\nDate: 2026-05-07\nTime: 14:00\nMode: online\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=53\nNotes: Join Zoom Meeting\\nMeeting ID: 829 2229 7670\\nPasscode: 7mEm9g\n(Meeting Scheduled ID: 53)', 'text', NULL, 1, '2026-04-04 10:30:57'),
(51, 6, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Meeting Request\nDate: 2026-04-18\nTime: 14:07\nMode: online\nMeeting Link: https://us05web.zoom.us/j/82922297670?pwd=xB0P5dNoXHbjAb0kJvRannA1LStvMm.1\nNotes: Meeting ID: 829 2229 7670\\nPasscode: 7mEm9g\n(Meeting Scheduled ID: 54)', 'text', NULL, 1, '2026-04-04 10:38:29'),
(52, 6, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Meeting Request\nDate: 2026-05-08\nTime: 14:19\nMode: online\nMeeting Link: https://us05web.zoom.us/j/85060243915?pwd=FoHG3PGMUIXnjRVQ1cv7rauyt3xnqT.1\nNotes: Meeting ID: 829 2229 7670\\nPasscode: 7mEm9g\n(Meeting Scheduled ID: 55)', 'text', NULL, 1, '2026-04-04 10:50:54'),
(53, 4, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Meeting Request\nDate: 2026-07-04\nTime: 14:37\nMode: online\nMeeting Link: http://ucschelpdesk/meetingscheduler/joinMeeting?meeting_id=56\nNotes: Meeting ID: 829 2229 7670, Passcode: 7mEm9g\n(Meeting Scheduled ID: 56)', 'text', NULL, 1, '2026-04-04 11:08:31'),
(54, 4, 21, 'Meeting scheduled successfully.\nTitle: Meeting: Meeting Request\nDate: 2026-05-09\nTime: 14:44\nMode: online\nMeeting Link: https://us05web.zoom.us/j/82922297670?pwd=xB0P5dNoXHbjAb0kJvRannA1LStvMm.1\nNotes: Meeting ID: 829 2229 7670, Passcode: 7mEm9g\n(Meeting Scheduled ID: 57)', 'text', NULL, 1, '2026-04-04 11:15:55'),
(55, 7, 2, 'Meeting reminder: Your scheduled session is due now.\nTitle: Meeting: Can\'t sleep\nDate: 2026-01-30\nTime: 09:00\nMode: in-person\nVenue: E401\nNotes: Bring a A4 sheet and a pen\n(Meeting ID: 1)', 'text', NULL, 0, '2026-04-05 01:31:22');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_response`
--

CREATE TABLE `ticket_response` (
  `ticket_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `response` text NOT NULL,
  `date_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket_response`
--

INSERT INTO `ticket_response` (`ticket_id`, `u_id`, `response`, `date_time`) VALUES
(38, 10, 'yes i saw it as well', '2025-10-19 19:58:06'),
(39, 19, 'reponce from staff exam memeber 2', '2025-10-21 02:41:17'),
(42, 19, 'responce', '2025-10-21 04:43:10');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_timeline`
--

CREATE TABLE `ticket_timeline` (
  `timeline_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `assigned` datetime DEFAULT NULL,
  `under_review` datetime DEFAULT NULL,
  `resolved` datetime DEFAULT NULL,
  `level_1` datetime NOT NULL,
  `level_2` datetime NOT NULL,
  `level_3` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket_timeline`
--

INSERT INTO `ticket_timeline` (`timeline_id`, `ticket_id`, `created`, `assigned`, `under_review`, `resolved`, `level_1`, `level_2`, `level_3`) VALUES
(1, 1, '2025-08-16 15:07:05', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 2, '2025-08-16 15:07:05', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 3, '2025-08-16 15:07:05', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 4, '2025-08-31 08:00:00', NULL, NULL, NULL, '2026-01-22 16:35:42', '2026-01-22 16:35:46', '2026-01-22 16:35:50'),
(5, 5, '2025-08-31 08:05:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(6, 6, '2025-08-31 08:10:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(7, 13, '2025-08-31 11:33:05', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(8, 14, '2025-08-31 13:05:22', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(10, 20, '2025-09-01 12:16:31', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(11, 21, '2025-09-02 16:29:21', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(12, 24, '2025-10-15 16:31:48', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(17, 33, '2025-10-18 16:51:33', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(18, 34, '2025-10-18 16:52:17', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(19, 35, '2025-10-18 16:58:49', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(20, 36, '2025-10-18 16:59:05', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(21, 37, '2025-10-19 11:37:02', '2026-01-22 17:07:20', NULL, NULL, '2026-01-22 16:35:02', '2026-01-22 16:35:13', '2026-01-22 17:07:25'),
(22, 38, '2025-10-19 11:38:07', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(23, 39, '2025-10-19 12:55:12', '2026-01-22 17:10:27', NULL, NULL, '2026-01-22 16:31:22', '2026-01-22 16:31:32', '2026-01-22 17:10:33'),
(24, 40, '2025-10-19 22:19:36', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(25, 41, '2025-10-19 22:51:57', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(26, 42, '2025-10-21 04:31:36', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(27, 43, '2025-10-22 11:46:41', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(30, 46, '2025-10-22 15:24:57', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(32, 48, '2025-10-22 17:46:16', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(33, 49, '2025-10-22 17:48:03', '2026-01-22 17:12:27', NULL, NULL, '2026-01-22 16:30:42', '2026-01-22 16:30:54', '2026-01-22 17:12:32'),
(35, 51, '2025-10-22 17:56:17', '2026-01-22 17:14:05', NULL, NULL, '2026-01-22 16:30:01', '2026-01-22 16:30:13', '2026-01-22 17:14:10'),
(36, 52, '2025-10-22 17:58:28', NULL, NULL, NULL, '2026-01-22 16:29:23', '2026-01-22 16:29:33', '2026-01-22 16:29:50'),
(37, 53, '2025-10-22 17:59:51', '2026-01-22 18:05:10', NULL, NULL, '2026-01-22 16:28:43', '2026-01-22 16:28:55', '2026-01-22 18:05:15'),
(39, 55, '2025-10-22 18:06:17', '2026-01-22 18:03:37', NULL, NULL, '2026-01-22 16:28:04', '2026-01-22 16:28:15', '2026-01-22 18:03:42'),
(40, 56, '2025-10-22 18:10:40', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(41, 57, '2025-10-22 18:11:31', NULL, NULL, NULL, '2026-01-22 16:27:24', '2026-01-22 16:27:35', '2026-01-22 16:27:52'),
(42, 58, '2025-10-22 18:12:22', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(43, 59, '2025-10-22 18:15:24', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(44, 60, '2025-10-22 18:16:26', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(45, 61, '2025-10-22 18:21:57', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(46, 62, '2025-10-22 18:23:14', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(47, 63, '2025-10-22 18:26:22', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2025-12-18 04:31:03'),
(48, 64, '2025-10-22 18:27:25', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(56, 72, '2025-10-23 18:57:09', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(58, 74, '2025-10-23 18:59:33', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(59, 75, '2025-10-23 19:02:04', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(66, 82, '2025-11-22 07:17:43', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(68, 84, '2025-12-15 17:28:57', '2025-12-17 06:11:52', '2025-12-17 06:11:53', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2025-12-17 06:11:55'),
(69, 85, '2025-12-18 04:42:15', '2025-12-24 15:18:19', NULL, NULL, '0000-00-00 00:00:00', '2025-12-24 15:18:22', '0000-00-00 00:00:00'),
(72, 88, '2025-12-22 14:22:56', '2025-12-25 13:55:25', NULL, NULL, '2025-12-25 13:55:31', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(77, 93, '2026-01-17 17:50:04', NULL, NULL, NULL, '2026-01-22 17:54:08', '2026-01-22 17:54:20', '2026-01-22 17:54:37'),
(78, 94, '2026-01-18 18:07:14', '2026-02-16 07:10:52', NULL, NULL, '2026-01-22 18:09:34', '2026-01-22 18:09:46', '2026-02-16 07:10:56'),
(79, 95, '2026-02-02 15:03:52', NULL, NULL, NULL, '2026-02-14 03:49:15', '2026-02-14 03:49:28', '2026-02-14 03:49:49'),
(80, 96, '2025-02-10 09:30:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(81, 97, '2025-02-11 10:15:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(82, 98, '2025-02-12 14:20:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(83, 99, '2025-02-13 11:00:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(84, 100, '2025-02-14 15:45:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(85, 101, '2025-01-15 09:00:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(86, 102, '2025-01-18 13:30:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(87, 103, '2025-01-22 10:45:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(88, 104, '2025-01-25 14:00:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(89, 105, '2025-01-28 16:20:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(90, 106, '2024-12-05 11:00:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(91, 107, '2024-12-10 09:30:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(92, 108, '2024-12-15 14:45:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(93, 109, '2024-12-20 10:00:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(94, 110, '2024-12-28 13:15:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(100, 111, '2025-02-10 09:30:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(101, 112, '2025-02-11 10:15:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(102, 113, '2025-02-12 14:20:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(103, 114, '2025-02-13 11:00:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(104, 115, '2025-02-14 15:45:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(105, 116, '2025-01-15 09:00:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(106, 117, '2025-01-18 13:30:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(107, 118, '2025-01-22 10:45:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(108, 119, '2025-01-25 14:00:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(109, 120, '2025-01-28 16:20:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(110, 121, '2024-12-05 11:00:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(111, 122, '2024-12-10 09:30:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(112, 123, '2024-12-15 14:45:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(113, 124, '2024-12-20 10:00:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(114, 125, '2024-12-28 13:15:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(122, 126, '2026-02-17 06:55:00', NULL, NULL, NULL, '2026-04-01 14:34:33', '2026-04-01 14:35:44', '2026-04-01 14:36:01'),
(123, 127, '2026-04-01 14:10:38', NULL, NULL, NULL, '2026-04-05 01:49:08', '2026-04-05 01:49:14', '2026-04-05 01:49:21'),
(124, 128, '2026-04-01 14:22:40', NULL, NULL, NULL, '2026-04-05 01:48:51', '2026-04-05 01:48:57', '2026-04-05 01:49:03'),
(125, 129, '2026-04-01 14:23:14', NULL, NULL, NULL, '2026-04-05 01:48:33', '2026-04-05 01:48:39', '2026-04-05 01:48:46'),
(126, 130, '2026-04-01 14:23:43', NULL, NULL, NULL, '2026-04-05 01:48:15', '2026-04-05 01:48:21', '2026-04-05 01:48:27'),
(127, 131, '2026-04-01 14:27:02', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(128, 132, '2026-04-01 14:31:24', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(129, 133, '2026-04-01 14:34:03', '2026-04-01 14:38:05', NULL, NULL, '2026-04-01 14:38:09', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(130, 134, '2026-04-01 16:39:56', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(131, 135, '2026-04-04 12:35:07', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(132, 136, '2026-04-05 01:31:06', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(133, 137, '2026-04-05 01:47:57', '2026-04-05 01:51:06', NULL, '2026-04-05 01:51:26', '2026-04-05 01:51:08', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `u_id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `number` varchar(15) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `designation` int(10) DEFAULT NULL,
  `role` enum('staff','student','lecturer','admin','counselor') NOT NULL DEFAULT 'student',
  `password_hash` varchar(255) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `is_suspended` tinyint(1) NOT NULL DEFAULT 0,
  `suspended_at` datetime DEFAULT NULL,
  `profile_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`u_id`, `email`, `name`, `number`, `year`, `designation`, `role`, `password_hash`, `is_deleted`, `deleted_at`, `is_suspended`, `suspended_at`, `profile_url`) VALUES
(1, 'priyantha@ucsc.edu', 'Priyantha_GA', '1234567890', NULL, 1, 'staff', '$2y$10$exampleexampleexampleexampleexampLeQWERTYUIOPasdf', 0, NULL, 0, NULL, NULL),
(2, 'kamal@ucsc.edu', 'Kamal Kothalawala', '0987654321', NULL, 10, 'counselor', '$2y$10$exampleexampleexampleexampleexampLeQWERTYUIOPasdf', 0, NULL, 0, NULL, NULL),
(5, 'student2@ucsc.edu', 'Nimal_Lecturer', '0723456789', '2024', NULL, 'lecturer', '$2y$10$exampleexampleexampleexampleexampLeQWERTYUIOPasdf', 0, NULL, 0, NULL, NULL),
(6, '2023cs136@stu.ucsc.cmb.ac.lk', 'Kaweesha Pathirana', '0764055512', '0000', NULL, 'student', '$2y$10$URDlM7AsTgt/cnixVgOKFOg6en1IvJMiO3hX11B428nXY5zom4sKa', 0, NULL, 1, '2025-12-23 18:16:26', NULL),
(7, 'admin@kaweesha.com', 'Kaweesha', '', '2023', 0, 'admin', '$2y$10$o..U5.JB6lbOAk0ymcep1et9VOnJUsfOBJjL/eE9tyWiYje/jwsMS', 0, NULL, 0, NULL, 'https://pub-0692a76592204ed5a86a56eda6959123.r2.dev/profiles/7_1766931164.jpg'),
(8, '2023cs221@stu.ucsc.cmb.ac.lk', 'Student Brian', '0712785885', NULL, 0, 'student', '$2y$10$CPS.kVFgKqkM7rtGxEtHkefrlye1c6vmvUzwX0LsFN10BiW6rkyoC', 0, NULL, 0, NULL, 'https://pub-0692a76592204ed5a86a56eda6959123.r2.dev/profiles/8_1766666138.jpg'),
(9, 'satffkav@ucsc.edu', 'Staff Kavindu', '', NULL, 2, 'staff', '$2y$10$JXwb.Fiq6gcsF8wo//Q5IuBJKaUAe.nOVXy.EFU59KKJ5qXaL8vq.', 0, NULL, 0, NULL, NULL),
(10, 'examsatff1@gmail.com', 'Exam', '0778084755', NULL, 3, 'staff', '$2y$10$6QzGHEj96Age7Q/Pq2.TJePbxn3IG6S2D89cGd69/zsv615EFOq/m', 0, NULL, 0, NULL, NULL),
(11, 'student@ben.com', 'Namal Jayawardana', '', NULL, 0, 'student', '$2y$10$B4gpIaw.cBrLkmXGIfaen.ZmOftQxT6lWuM6g0dCI84ZusyTVdEjG', 0, NULL, 0, NULL, NULL),
(12, 'dakshina@gmail.com', 'Dakshina2', '0812456378', NULL, 0, 'student', '$2y$10$gZmNzLETbVJx28MfBXRGgOK28QBoJQZRP3UO0pVLpmzIe9TsdwDSu', 1, '2025-12-23 12:22:11', 0, NULL, NULL),
(13, 'Praveen@gmail.com', 'Praveen', '076598231', NULL, 10, 'counselor', '$2y$10$.qm/ab0YxcqZPPEZNsVkku7V5F02yc4Rh6o6BFu75tC.mxlBP15AK', 0, NULL, 0, NULL, NULL),
(14, 'Naweesha@gmail.com', 'Naweesha_PostGrad', '0812377212', NULL, 4, 'staff', '$2y$10$V/xLz1Y9aO16io2otOH3guxS.OqNhkK8.NRSN2sz3rTDDwCglKZQq', 0, NULL, 0, NULL, NULL),
(15, 'staff@kumara.com', 'kumara_NOC', '', NULL, 9, 'staff', '$2y$10$KwL6GsxaER6gVkkmPEz3M.uFgyuwspJ7R7e3fiDZOODXOer/RWPZe', 0, NULL, 0, NULL, NULL),
(16, 'examstaffanjalee@gmail.com', 'Anajalee_Exam', '', NULL, 5, 'staff', '$2y$10$KcqHWJzcY6qM8z9jTHrGAOPJfeEkryqp9CFc6GfEhLDBFBpZj8i1a', 0, NULL, 0, NULL, NULL),
(17, 'postgrad@gmail.com', 'staff_postGrad', '0112365478', NULL, 4, 'staff', '$2y$10$Xz6q.YwDP7v9dXmDxdSmjO3bqIGH5gsUWbG97hMy4MaxRrxUPG0tS', 0, NULL, 0, NULL, NULL),
(18, 'counselor@brian.com', 'Dinuni', '', NULL, 0, 'counselor', '$2y$10$f/VbB1VcAfinJt6EEdaBw.HNbCS328CZxo8uMkOzCIGpvW1PJjY/G', 0, NULL, 0, NULL, NULL),
(19, 'kavinduattanayaka@gmail.com', 'staff2_Exam', '1234567892', NULL, 5, 'staff', '$2y$10$ZfDRH8PfYbkUtC.GUwSMZeVU07alUkJoAnid7aDDWMDDhTKXm8sI6', 0, NULL, 0, NULL, NULL),
(20, 'namal@gmail.com', 'Namal Dickwalla', '0764055512', NULL, 10, 'counselor', '$2y$10$WsIbDTLBSeggVlqcQVzUDuw9y2S..RsQUlcjtBzHc.t8PRZihluLK', 0, NULL, 0, NULL, NULL),
(21, 'tharushi@gmail.com', 'Tharushi', '', '0000', NULL, 'counselor', '$2y$10$rKlxA4QfyP19n3qCSX9ci.FNxVgG3qeOIjx4ilASKxy357yN243.O', 0, NULL, 0, NULL, NULL),
(23, 'coun1@gmail.com', 'counselor1', '02345678915', NULL, NULL, 'counselor', '$2y$10$3C0VjyuVikYv945osZetw.3dp8ePBlhAtO6INuJHV4goelAeXxTHG', 0, NULL, 0, NULL, NULL),
(24, 'student1@gmail.com', 'student1', '07756423956', '2023', NULL, 'student', '$2y$10$UtoxfTzTPNIitcOZuXYLF.FPRfP.p28eEpWYLfm1t0Y.o4f8/.cqi', 0, NULL, 0, NULL, NULL),
(25, 'student2@gmail.com', 'student2', '07895623145', '2022', NULL, 'student', '$2y$10$Nt4CTo/oRosjNwC4klkcNuOUgT1si7o/WlVSuwG/VWZzcBMBi2qaO', 0, NULL, 0, NULL, NULL),
(26, 'student3@gmail.com', 'student3', '0789563214', '2022', NULL, 'student', '$2y$10$8Q2SeYwmRZ3vyCATGdGz7eZWfZ5LunFf4JaHvU.0N/AiY292E0rH2', 0, NULL, 0, NULL, NULL),
(27, 'student4@gmail.com', 'student4', '011236547892', '2023', NULL, 'student', '$2y$10$./uUkgp9tb/eOEp6IdDQX.2SaGrTWV3TwNAYQJGHWAE2RhZP8SLy6', 0, NULL, 0, NULL, NULL),
(28, 'student5@gmail.com', 'student5', '0745693246', '2024', NULL, 'student', '$2y$10$Tw6NNq/3ntz1T8SI0SwmeeMUeDILrJiJ7PhIiKhj0jvaF03Va3rv6', 0, NULL, 0, NULL, NULL),
(29, 'nocstaff@gmail.com', 'NocStaff', '0728965412', NULL, 9, 'staff', '$2y$10$0s4kdniSjrGt4qPJm5zXtewAXeeWeF4Ln5WFzyA2lVSjht9Nk7HAW', 0, NULL, 0, NULL, NULL),
(30, 'staff@brian.com', 'Brian', '', NULL, 5, 'staff', '$2y$10$edJ3C8ECxUKr1qbh9jUCPuSSGSLyHl0CaYIxASex.DrTy7U5iQSF2', 0, NULL, 0, NULL, NULL),
(31, 'counsellor@brian.com', 'Brian', '', NULL, 10, 'counselor', '$2y$10$VUWaoot845mznIVieoMzl.PAzAW1lMTwA3eqFTJMP4sbs/mhDn1D2', 0, NULL, 0, NULL, NULL),
(32, 'exam@staff.com', 'Brian', '', '2001', 5, 'staff', '$2y$10$3/zq5TlpFeTJw.H1.FiZ1OpbkEZytwIRZ6CXX0yaI4blrVOHu5jpm', 0, NULL, 0, NULL, NULL),
(33, 'temp@gmail.com', 'Kaweesha Temp', '', '2023', NULL, 'student', '$2y$10$o4XoqXvNgj78mAkz7uhZc.xCMOgOZa5U04G2pNAQSKgFsuWzV7Odu', 1, '2025-12-23 12:34:41', 0, NULL, NULL),
(34, 'student@brian.com', 'Brian Weerasinghe', '', '2026', NULL, 'student', '$2y$10$.s6WlDc1moxPg/ixmrcTy.iysCDg.SWZ297VYNADO/ryHLR6ke9pm', 0, NULL, 0, NULL, NULL),
(35, 'admin@brian.com', 'Brian Weerasinghe', '', '2026', NULL, 'admin', '$2y$10$ns4tiz4jH5OQ7mXTJMJxMuj0qHwsXi8ypiZYiarCOmigLCrtzuUe6', 0, NULL, 0, NULL, NULL),
(36, 'tharukanilmini511@gmail.com', 'Nilmini Tharuka', '0776320890', '2023', NULL, 'student', '$2y$10$p0ZSQv/YlJkbPsOhV9M6PeKwQmHM1feFgaTLhjYauFrqfcKAusVau', 0, NULL, 0, NULL, NULL);

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `users_before_update_deleted` BEFORE UPDATE ON `users` FOR EACH ROW BEGIN
    IF NEW.is_deleted = 1 AND OLD.is_deleted = 0 THEN
        SET NEW.deleted_at = NOW();
    ELSEIF NEW.is_deleted = 0 AND OLD.is_deleted = 1 THEN
        SET NEW.deleted_at = NULL;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `users_before_update_suspended` BEFORE UPDATE ON `users` FOR EACH ROW BEGIN
    IF NEW.is_suspended = 1 AND OLD.is_suspended = 0 THEN
        SET NEW.suspended_at = NOW();
    ELSEIF NEW.is_suspended = 0 AND OLD.is_suspended = 1 THEN
        SET NEW.suspended_at = NULL;
    END IF;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `u_id` (`u_id`);

--
-- Indexes for table `announcement_files`
--
ALTER TABLE `announcement_files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `announcement_id` (`announcement_id`);

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_entity` (`entity_type`,`entity_id`);

--
-- Indexes for table `calendar_events`
--
ALTER TABLE `calendar_events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `idx_user_date` (`u_id`,`event_date`),
  ADD KEY `idx_event_date` (`event_date`);

--
-- Indexes for table `counselors`
--
ALTER TABLE `counselors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_counselors_user_id` (`user_id`);

--
-- Indexes for table `counselor_availability`
--
ALTER TABLE `counselor_availability`
  ADD PRIMARY KEY (`availability_id`),
  ADD KEY `idx_counselor_day` (`counselor_id`,`day_of_week`);

--
-- Indexes for table `division`
--
ALTER TABLE `division`
  ADD PRIMARY KEY (`did`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `forum_comments`
--
ALTER TABLE `forum_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `u_id` (`u_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `forum_q`
--
ALTER TABLE `forum_q`
  ADD PRIMARY KEY (`q_id`),
  ADD KEY `u_id` (`u_id`);

--
-- Indexes for table `forum_votes`
--
ALTER TABLE `forum_votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vote` (`post_id`,`u_id`),
  ADD KEY `fk_forum_votes_user` (`u_id`);

--
-- Indexes for table `kb_files`
--
ALTER TABLE `kb_files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `kb_file_id` (`kb_id`);

--
-- Indexes for table `knowledgebase`
--
ALTER TABLE `knowledgebase`
  ADD PRIMARY KEY (`base_id`),
  ADD KEY `fk_1` (`created_by`);

--
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_lecturers_user_id` (`user_id`);

--
-- Indexes for table `lost_found`
--
ALTER TABLE `lost_found`
  ADD PRIMARY KEY (`q_id`),
  ADD KEY `u_id` (`u_id`);

--
-- Indexes for table `lost_found_item_files`
--
ALTER TABLE `lost_found_item_files`
  ADD PRIMARY KEY (`q_id`,`name`);

--
-- Indexes for table `lost_found_response`
--
ALTER TABLE `lost_found_response`
  ADD PRIMARY KEY (`u_id`,`q_id`),
  ADD KEY `q_id` (`q_id`);

--
-- Indexes for table `meeting_participants`
--
ALTER TABLE `meeting_participants`
  ADD PRIMARY KEY (`participant_id`),
  ADD UNIQUE KEY `unique_meeting_user` (`meeting_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `meeting_reminders`
--
ALTER TABLE `meeting_reminders`
  ADD PRIMARY KEY (`reminder_id`),
  ADD KEY `meeting_id` (`meeting_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_remind_at` (`remind_at`,`is_sent`);

--
-- Indexes for table `meeting_schedules`
--
ALTER TABLE `meeting_schedules`
  ADD PRIMARY KEY (`meeting_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `idx_counselor_date` (`counselor_id`,`meeting_date`),
  ADD KEY `idx_student_date` (`student_id`,`meeting_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `fk_meeting_ticket` (`ticket_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`news_id`),
  ADD KEY `u_id` (`u_id`);

--
-- Indexes for table `news_document`
--
ALTER TABLE `news_document`
  ADD PRIMARY KEY (`news_id`,`name`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notif_id`),
  ADD KEY `idx_notif_user` (`u_id`),
  ADD KEY `idx_notif_unread` (`u_id`,`is_read`);

--
-- Indexes for table `reply_q`
--
ALTER TABLE `reply_q`
  ADD PRIMARY KEY (`q_id`,`u_id`),
  ADD KEY `u_id` (`u_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`),
  ADD KEY `idx_staff_user_id` (`user_id`);

--
-- Indexes for table `staff_division`
--
ALTER TABLE `staff_division`
  ADD PRIMARY KEY (`u_id`),
  ADD KEY `fk_staff_division_did` (`did`),
  ADD KEY `staff_division_h_level_id` (`h_id`),
  ADD KEY `idx_u_id_did` (`u_id`,`did`);

--
-- Indexes for table `staff_hierachy`
--
ALTER TABLE `staff_hierachy`
  ADD PRIMARY KEY (`h_id`),
  ADD KEY `hirechy_did_constarint` (`did`),
  ADD KEY `idx_h_id` (`h_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `reg_number` (`reg_number`),
  ADD KEY `idx_students_user_id` (`user_id`);

--
-- Indexes for table `supporting_documents`
--
ALTER TABLE `supporting_documents`
  ADD PRIMARY KEY (`ticket_id`,`doc_name`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `fk_2` (`division`);

--
-- Indexes for table `template_submissions`
--
ALTER TABLE `template_submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `template_id` (`template_id`),
  ADD KEY `u_id` (`u_id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `u_id` (`u_id`),
  ADD KEY `idx_status_created` (`status`,`created_at`),
  ADD KEY `idx_division_assigned` (`division`,`assigned_to`);

--
-- Indexes for table `ticket_chat`
--
ALTER TABLE `ticket_chat`
  ADD PRIMARY KEY (`chat_id`),
  ADD KEY `idx_ticket_chat_u1` (`u1_id`),
  ADD KEY `idx_ticket_chat_u2` (`u2_id`),
  ADD KEY `idx_ticket_chat_ticket` (`ticket_id`);

--
-- Indexes for table `ticket_messages`
--
ALTER TABLE `ticket_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ticket_messages_chat` (`chat_id`),
  ADD KEY `idx_ticket_messages_sender` (`sender_id`),
  ADD KEY `idx_ticket_messages_reply` (`reply_to_id`);

--
-- Indexes for table `ticket_response`
--
ALTER TABLE `ticket_response`
  ADD PRIMARY KEY (`ticket_id`,`u_id`),
  ADD KEY `u_id` (`u_id`);

--
-- Indexes for table `ticket_timeline`
--
ALTER TABLE `ticket_timeline`
  ADD PRIMARY KEY (`timeline_id`),
  ADD UNIQUE KEY `uq_ticket_timeline_ticket` (`ticket_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`u_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_role` (`role`),
  ADD KEY `idx_u_id` (`u_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `announcement_files`
--
ALTER TABLE `announcement_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `calendar_events`
--
ALTER TABLE `calendar_events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT for table `counselors`
--
ALTER TABLE `counselors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `counselor_availability`
--
ALTER TABLE `counselor_availability`
  MODIFY `availability_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `division`
--
ALTER TABLE `division`
  MODIFY `did` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `forum_comments`
--
ALTER TABLE `forum_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `forum_q`
--
ALTER TABLE `forum_q`
  MODIFY `q_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `forum_votes`
--
ALTER TABLE `forum_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `kb_files`
--
ALTER TABLE `kb_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `knowledgebase`
--
ALTER TABLE `knowledgebase`
  MODIFY `base_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `lecturers`
--
ALTER TABLE `lecturers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lost_found`
--
ALTER TABLE `lost_found`
  MODIFY `q_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `meeting_participants`
--
ALTER TABLE `meeting_participants`
  MODIFY `participant_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meeting_reminders`
--
ALTER TABLE `meeting_reminders`
  MODIFY `reminder_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meeting_schedules`
--
ALTER TABLE `meeting_schedules`
  MODIFY `meeting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `news_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notif_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_hierachy`
--
ALTER TABLE `staff_hierachy`
  MODIFY `h_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `template_submissions`
--
ALTER TABLE `template_submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `ticket_chat`
--
ALTER TABLE `ticket_chat`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ticket_messages`
--
ALTER TABLE `ticket_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `ticket_timeline`
--
ALTER TABLE `ticket_timeline`
  MODIFY `timeline_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `u_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcement`
--
ALTER TABLE `announcement`
  ADD CONSTRAINT `announcement_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE SET NULL;

--
-- Constraints for table `announcement_files`
--
ALTER TABLE `announcement_files`
  ADD CONSTRAINT `announcement_files_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcement` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `calendar_events`
--
ALTER TABLE `calendar_events`
  ADD CONSTRAINT `fk_calendar_events_user` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `counselor_availability`
--
ALTER TABLE `counselor_availability`
  ADD CONSTRAINT `counselor_availability_ibfk_1` FOREIGN KEY (`counselor_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

--
-- Constraints for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `feedbacks_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedbacks_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_comments`
--
ALTER TABLE `forum_comments`
  ADD CONSTRAINT `fk_fc_parent` FOREIGN KEY (`parent_id`) REFERENCES `forum_comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fc_post` FOREIGN KEY (`post_id`) REFERENCES `forum_q` (`q_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fc_user` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_q`
--
ALTER TABLE `forum_q`
  ADD CONSTRAINT `forum_q_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_votes`
--
ALTER TABLE `forum_votes`
  ADD CONSTRAINT `fk_forum_votes_post` FOREIGN KEY (`post_id`) REFERENCES `forum_q` (`q_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_forum_votes_user` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

--
-- Constraints for table `kb_files`
--
ALTER TABLE `kb_files`
  ADD CONSTRAINT `kb_file_id` FOREIGN KEY (`kb_id`) REFERENCES `knowledgebase` (`base_id`);

--
-- Constraints for table `knowledgebase`
--
ALTER TABLE `knowledgebase`
  ADD CONSTRAINT `fk_1` FOREIGN KEY (`created_by`) REFERENCES `staff_division` (`u_id`);

--
-- Constraints for table `lost_found`
--
ALTER TABLE `lost_found`
  ADD CONSTRAINT `lost_found_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

--
-- Constraints for table `lost_found_item_files`
--
ALTER TABLE `lost_found_item_files`
  ADD CONSTRAINT `lost_found_item_files_ibfk_1` FOREIGN KEY (`q_id`) REFERENCES `lost_found` (`q_id`) ON DELETE CASCADE;

--
-- Constraints for table `lost_found_response`
--
ALTER TABLE `lost_found_response`
  ADD CONSTRAINT `lost_found_response_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lost_found_response_ibfk_2` FOREIGN KEY (`q_id`) REFERENCES `lost_found` (`q_id`) ON DELETE CASCADE;

--
-- Constraints for table `meeting_participants`
--
ALTER TABLE `meeting_participants`
  ADD CONSTRAINT `meeting_participants_ibfk_1` FOREIGN KEY (`meeting_id`) REFERENCES `meeting_schedules` (`meeting_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meeting_participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

--
-- Constraints for table `meeting_reminders`
--
ALTER TABLE `meeting_reminders`
  ADD CONSTRAINT `meeting_reminders_ibfk_1` FOREIGN KEY (`meeting_id`) REFERENCES `meeting_schedules` (`meeting_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meeting_reminders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

--
-- Constraints for table `meeting_schedules`
--
ALTER TABLE `meeting_schedules`
  ADD CONSTRAINT `fk_meeting_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `meeting_schedules_ibfk_1` FOREIGN KEY (`counselor_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meeting_schedules_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`u_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `meeting_schedules_ibfk_3` FOREIGN KEY (`event_id`) REFERENCES `calendar_events` (`event_id`) ON DELETE SET NULL;

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

--
-- Constraints for table `news_document`
--
ALTER TABLE `news_document`
  ADD CONSTRAINT `news_document_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`news_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notif_user` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

--
-- Constraints for table `reply_q`
--
ALTER TABLE `reply_q`
  ADD CONSTRAINT `reply_q_ibfk_1` FOREIGN KEY (`q_id`) REFERENCES `forum_q` (`q_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reply_q_ibfk_2` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_division`
--
ALTER TABLE `staff_division`
  ADD CONSTRAINT `fk_staff_division_did` FOREIGN KEY (`did`) REFERENCES `division` (`did`),
  ADD CONSTRAINT `staff_division_h_level_id` FOREIGN KEY (`h_id`) REFERENCES `staff_hierachy` (`h_id`),
  ADD CONSTRAINT `staff_division_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_division_ibfk_2` FOREIGN KEY (`did`) REFERENCES `division` (`did`) ON DELETE CASCADE;

--
-- Constraints for table `staff_hierachy`
--
ALTER TABLE `staff_hierachy`
  ADD CONSTRAINT `hirechy_did_constarint` FOREIGN KEY (`did`) REFERENCES `division` (`did`);

--
-- Constraints for table `supporting_documents`
--
ALTER TABLE `supporting_documents`
  ADD CONSTRAINT `supporting_documents_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE;

--
-- Constraints for table `templates`
--
ALTER TABLE `templates`
  ADD CONSTRAINT `fk_2` FOREIGN KEY (`division`) REFERENCES `division` (`did`);

--
-- Constraints for table `template_submissions`
--
ALTER TABLE `template_submissions`
  ADD CONSTRAINT `ts_template_fk` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ts_ticket_fk` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE SET NULL;

--
-- Constraints for table `ticket_chat`
--
ALTER TABLE `ticket_chat`
  ADD CONSTRAINT `fk_ticket_chat_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ticket_chat_u1` FOREIGN KEY (`u1_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ticket_chat_u2` FOREIGN KEY (`u2_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ticket_messages`
--
ALTER TABLE `ticket_messages`
  ADD CONSTRAINT `fk_ticket_messages_chat` FOREIGN KEY (`chat_id`) REFERENCES `ticket_chat` (`chat_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ticket_messages_reply` FOREIGN KEY (`reply_to_id`) REFERENCES `ticket_messages` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ticket_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`u_id`) ON UPDATE CASCADE;

--
-- Constraints for table `ticket_response`
--
ALTER TABLE `ticket_response`
  ADD CONSTRAINT `ticket_response_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_response_ibfk_2` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

--
-- Constraints for table `ticket_timeline`
--
ALTER TABLE `ticket_timeline`
  ADD CONSTRAINT `fk_ticket_timeline_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
