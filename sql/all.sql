-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 31, 2025 at 03:28 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
 /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
 /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 /*!40101 SET NAMES utf8mb4 */;

-- Use a single default charset/collation for the DB
-- (Create DB if needed)
-- CREATE DATABASE IF NOT EXISTS `support_desk_my_version`
--   DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
-- USE `support_desk_my_version`;

--
-- Database: `support_desk_my_version`
--

-- --------------------------------------------------------
-- Table structure for table `announcement`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `announcement`;
CREATE TABLE IF NOT EXISTS `announcement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `topic` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `u_id` int DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `u_id` (`u_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `announcement`
INSERT INTO `announcement` (`id`, `content`, `topic`, `u_id`, `date_time`) VALUES
(1, 'Fall 2025 registration closes on August 25, 2025. Use the academic portal.', 'Course Registration', 1, '2025-08-20 08:00:00'),
(2, 'Servers down on August 22, 2025, 11:00 PM to 3:00 AM.', 'System Maintenance', 1, '2025-08-20 10:15:00'),
(3, 'Mental health workshops from August 26-30, 2025.', 'Counseling Workshops', 2, '2025-08-18 14:45:00'),
(4, 'New staff policies effective September 1, 2025.', 'Policy Update', 3, '2025-08-19 11:30:00'),
(9, 'PlEAsHEEEEEEE WORKKKKKKK XDXDXDXD nmnmnmn', 'Hope This works(edited versions) long long version', 1, '2025-08-23 15:11:20'),
(10, 'is it wrking? testing', 'New One to CHeck', 1, '2025-08-30 10:23:32'),
(11, 'checking', 'New Annnoucenement after new databse check', 1, '2025-08-31 08:57:33');

-- --------------------------------------------------------
-- Table structure for table `announcement_files`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `announcement_files`;
CREATE TABLE IF NOT EXISTS `announcement_files` (
  `file_id` int NOT NULL AUTO_INCREMENT,
  `announcement_id` int NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file_size` int NOT NULL,
  PRIMARY KEY (`file_id`),
  KEY `announcement_id` (`announcement_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `announcement_files` (relative paths)
INSERT INTO `announcement_files` (`file_id`, `announcement_id`, `file_name`, `file_path`, `file_type`, `file_size`) VALUES
(5, 9, 'Packet-1.png', 'uploads/announcements/9/1755942080_Packet-1.png', 'image/png', 64472),
(6, 11, '23000139 (16).pdf', 'uploads/announcements/11/1756610853_23000139 (16).pdf', 'application/pdf', 690061);

-- --------------------------------------------------------
-- Table structure for table `division`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `division`;
CREATE TABLE IF NOT EXISTS `division` (
  `did` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`did`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- Data for table `division`
INSERT INTO `division` (`did`, `name`) VALUES
(1, 'Academic'),
(2, 'IT'),
(3, 'Counseling'),
(4, 'Admin'),
(5, 'Library');

-- --------------------------------------------------------
-- Table structure for table `forum_q`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `forum_q`;
CREATE TABLE IF NOT EXISTS `forum_q` (
  `q_id` int NOT NULL AUTO_INCREMENT,
  `status` enum('open','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'open',
  `question` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `topic` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `u_id` int NOT NULL,
  PRIMARY KEY (`q_id`),
  KEY `u_id` (`u_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `knowledgebase`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `knowledgebase`;
CREATE TABLE IF NOT EXISTS `knowledgebase` (
  `base_id` int NOT NULL AUTO_INCREMENT,
  `topic` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`base_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `knowledgebase_files`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `knowledgebase_files`;
CREATE TABLE IF NOT EXISTS `knowledgebase_files` (
  `base_id` int NOT NULL,
  `filepath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`base_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `lost_found`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `lost_found`;
CREATE TABLE IF NOT EXISTS `lost_found` (
  `q_id` int NOT NULL AUTO_INCREMENT,
  `u_id` int NOT NULL,
  `item_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('lost','found','claimed') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'lost',
  `contact_num` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`q_id`),
  KEY `u_id` (`u_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `lost_found_item_files`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `lost_found_item_files`;
CREATE TABLE IF NOT EXISTS `lost_found_item_files` (
  `q_id` int NOT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`q_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `lost_found_response`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `lost_found_response`;
CREATE TABLE IF NOT EXISTS `lost_found_response` (
  `u_id` int NOT NULL,
  `q_id` int NOT NULL,
  `date_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`u_id`,`q_id`),
  KEY `q_id` (`q_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `news`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `news_id` int NOT NULL AUTO_INCREMENT,
  `u_id` int NOT NULL,
  `topic` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `contact_num` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`news_id`),
  KEY `u_id` (`u_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `news_document`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `news_document`;
CREATE TABLE IF NOT EXISTS `news_document` (
  `news_id` int NOT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`news_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `reply_q`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `reply_q`;
CREATE TABLE IF NOT EXISTS `reply_q` (
  `q_id` int NOT NULL,
  `u_id` int NOT NULL,
  `response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`q_id`,`u_id`),
  KEY `u_id` (`u_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `staff_division`
-- (use `did` to match `division.did`)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `staff_division`;
CREATE TABLE IF NOT EXISTS `staff_division` (
  `u_id` int NOT NULL,
  `did` int NOT NULL,
  PRIMARY KEY (`u_id`,`did`),
  KEY `did` (`did`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- Data for table `staff_division`
INSERT INTO `staff_division` (`u_id`, `did`) VALUES
(1, 1),
(1, 2),
(2, 3),
(3, 4);

-- --------------------------------------------------------
-- Table structure for table `supporting_documents`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `supporting_documents`;
CREATE TABLE IF NOT EXISTS `supporting_documents` (
  `ticket_id` int NOT NULL,
  `doc_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`ticket_id`,`doc_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `templates`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `templates`;
CREATE TABLE IF NOT EXISTS `templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fields` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `process` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `outcome` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `letter_required` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `templates`
INSERT INTO `templates` (`id`, `name`, `category`, `fields`, `process`, `outcome`, `letter_required`, `created_at`, `created_by`) VALUES
(1, 'Sample template thingy HIkzz', 'Technical Support', '[\"id\"]', 'please attach these documents', 'wait till a responce', 1, '2025-08-30 20:16:38', 1),
(2, 'Sample template thingy HIkzz', 'Technical Support', '[\"id\",\"name\",\"date\"]', 'please attach these documents', 'wait till a responce', 1, '2025-08-30 20:16:51', 1),
(3, 'Wi-Fi Password Reset', 'Technical Support', '[\"student_id\", \"email\", \"mac_address\"]', 'Student submits form, ticket is created...', 'Password reset and emailed', 1, '2025-08-30 20:23:36', 1),
(4, 'Lost Student ID Card', 'Admin', '[\"index_number\", \"nic_copy\"]', 'Submit request, verify documents...', 'New ID issued', 1, '2025-08-30 20:23:36', 1);

-- --------------------------------------------------------
-- Table structure for table `template_submissions`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `template_submissions`;
CREATE TABLE IF NOT EXISTS `template_submissions` (
  `submission_id` int NOT NULL AUTO_INCREMENT,
  `template_id` int NOT NULL,
  `u_id` int NOT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `generated_letter` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ticket_id` int NOT NULL,
  `submitted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`submission_id`),
  KEY `template_id` (`template_id`),
  KEY `u_id` (`u_id`),
  KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `tickets`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tickets`;
CREATE TABLE IF NOT EXISTS `tickets` (
  `ticket_id` int NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `u_id` int DEFAULT NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('pending','agent assigned','resolved','agent-closed','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `priority` enum('high','medium','low') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `meeting_requested` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`ticket_id`),
  KEY `u_id` (`u_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- Data for table `tickets`
INSERT INTO `tickets` (`ticket_id`, `created_at`, `title`, `u_id`, `category`, `status`, `priority`, `description`, `meeting_requested`) VALUES
(1, '2025-08-16 15:07:05', 'Course material access issue', 1, 'Coursework', 'pending', 'high', 'Cannot access uploaded lecture slides', 'Requested'),
(2, '2025-08-16 15:07:05', 'Grade appeal request', 1, 'Grades', 'resolved', 'medium', 'Appealing grade for Assignment 2', NULL),
(3, '2025-08-16 15:07:05', 'Exam schedule conflict', 1, 'Exam', 'pending', 'low', 'Exam date clashes with another registered course', NULL),
(4, '2025-08-31 08:00:00', 'Course material access issue', 4, 'Coursework', 'pending', 'high', 'Cannot access uploaded lecture slides', 'Requested'),
(5, '2025-08-31 08:05:00', 'Grade appeal request', 4, 'Grades', 'resolved', 'medium', 'Appealing grade for Assignment 2', NULL),
(6, '2025-08-31 08:10:00', 'Exam schedule conflict', 5, 'Exam', 'pending', 'low', 'Exam date clashes with another registered course', NULL);

-- --------------------------------------------------------
-- Table structure for table `ticket_response`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `ticket_response`;
CREATE TABLE IF NOT EXISTS `ticket_response` (
  `ticket_id` int NOT NULL,
  `u_id` int NOT NULL,
  `response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ticket_id`,`u_id`),
  KEY `u_id` (`u_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `u_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL UNIQUE,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `number` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `year` YEAR DEFAULT NULL,
  `designation` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` enum('staff','student','lecturer','admin','counselor') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'student',
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`u_id`),
  KEY `idx_users_role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- Data for table `users` (now includes password_hash)
-- Example hashes are placeholders; generate with PHP password_hash()
INSERT INTO `users` (`u_id`, `email`, `name`, `number`, `year`, `designation`, `role`, `password_hash`) VALUES
(1, 'john.doe@ucsc.edu', 'John Doe', '1234567890', NULL, 'IT Support', 'staff', '$2y$10$exampleexampleexampleexampleexampLeQWERTYUIOPasdf'),
(2, 'jane.smith@ucsc.edu', 'Jane Smith', '0987654321', NULL, 'Counselor', 'counselor', '$2y$10$exampleexampleexampleexampleexampLeQWERTYUIOPasdf'),
(3, 'alice.jones@ucsc.edu', 'Alice Jones', '1122334455', NULL, 'Admin Officer', 'admin', '$2y$10$exampleexampleexampleexampleexampLeQWERTYUIOPasdf'),
(4, 'student1@ucsc.edu', 'Kaweesha Pathirana', '0712345678', 2025, NULL, 'student', '$2y$10$exampleexampleexampleexampleexampLeQWERTYUIOPasdf'),
(5, 'student2@ucsc.edu', 'Nimal Perera', '0723456789', 2024, NULL, 'lecturer', '$2y$10$exampleexampleexampleexampleexampLeQWERTYUIOPasdf');

-- --------------------------------------------------------
-- Constraints (Foreign Keys)
-- --------------------------------------------------------

-- announcement → users
ALTER TABLE `announcement`
  ADD CONSTRAINT `announcement_ibfk_1`
  FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE SET NULL;

-- announcement_files → announcement
ALTER TABLE `announcement_files`
  ADD CONSTRAINT `announcement_files_ibfk_1`
  FOREIGN KEY (`announcement_id`) REFERENCES `announcement` (`id`) ON DELETE CASCADE;

-- forum_q → users
ALTER TABLE `forum_q`
  ADD CONSTRAINT `forum_q_ibfk_1`
  FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

-- knowledgebase_files → knowledgebase
ALTER TABLE `knowledgebase_files`
  ADD CONSTRAINT `knowledgebase_files_ibfk_1`
  FOREIGN KEY (`base_id`) REFERENCES `knowledgebase` (`base_id`) ON DELETE CASCADE;

-- lost_found → users
ALTER TABLE `lost_found`
  ADD CONSTRAINT `lost_found_ibfk_1`
  FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

-- lost_found_item_files → lost_found
ALTER TABLE `lost_found_item_files`
  ADD CONSTRAINT `lost_found_item_files_ibfk_1`
  FOREIGN KEY (`q_id`) REFERENCES `lost_found` (`q_id`) ON DELETE CASCADE;

-- lost_found_response → users & lost_found
ALTER TABLE `lost_found_response`
  ADD CONSTRAINT `lost_found_response_ibfk_1`
  FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lost_found_response_ibfk_2`
  FOREIGN KEY (`q_id`) REFERENCES `lost_found` (`q_id`) ON DELETE CASCADE;

-- news → users
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1`
  FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

-- news_document → news
ALTER TABLE `news_document`
  ADD CONSTRAINT `news_document_ibfk_1`
  FOREIGN KEY (`news_id`) REFERENCES `news` (`news_id`) ON DELETE CASCADE;

-- reply_q → forum_q & users
ALTER TABLE `reply_q`
  ADD CONSTRAINT `reply_q_ibfk_1`
  FOREIGN KEY (`q_id`) REFERENCES `forum_q` (`q_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reply_q_ibfk_2`
  FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

-- staff_division → users & division
ALTER TABLE `staff_division`
  ADD CONSTRAINT `staff_division_ibfk_1`
  FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_division_ibfk_2`
  FOREIGN KEY (`did`) REFERENCES `division` (`did`) ON DELETE CASCADE;

-- supporting_documents → tickets
ALTER TABLE `supporting_documents`
  ADD CONSTRAINT `supporting_documents_ibfk_1`
  FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE;

-- tickets → users
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1`
  FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE SET NULL;

-- ticket_response → tickets & users
ALTER TABLE `ticket_response`
  ADD CONSTRAINT `ticket_response_ibfk_1`
  FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_response_ibfk_2`
  FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

-- template_submissions → templates & tickets (added)
ALTER TABLE `template_submissions`
  ADD CONSTRAINT `ts_template_fk`
  FOREIGN KEY (`template_id`) REFERENCES `templates`(`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ts_ticket_fk`
  FOREIGN KEY (`ticket_id`) REFERENCES `tickets`(`ticket_id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
 /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
 /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
