-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql-ucschelpdesk.alwaysdata.net
-- Generation Time: Dec 23, 2025 at 11:22 AM
-- Server version: 10.11.14-MariaDB
-- PHP Version: 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

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
(10, 'For ICter conference now we are accepting students research. Please submit them before 5th of August 2025', 'Research Abstract Submission', 17, '2025-08-30 10:23:32'),
(11, 'Dear Students ,\r\nthe wifi issue is now solved now , you can connect to UCSC wifi now\r\n\r\nThanks.', 'Wifi Issue', 29, '2025-08-31 08:57:33'),
(12, 'Dear students ,\r\n\r\nAll the results for academic year 2023/2024 are published in the UGVLE.\r\n\r\nThanks.\r\n\r\nBest regards,\r\nAcademic Affairs', 'Publication of Exam Results change', 10, '2025-10-19 07:38:26');

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
-- Table structure for table `counselors`
--

CREATE TABLE `counselors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(6, 'What should I do if there\'s a power cut or AC issue during class?', 'Use the \"Facilities Issue\" category on the Help Desk portal to report it immediately. These reports are visible to all students and can be upvoted if others are facing the same issue. Critical facility problems are prioritized and sent directly to campus maintenance,.', '2025-10-23 14:31:10');

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
(10, 1, 'water leak on E401', 'General', 'is that AC water leaks oon E401 ? is it normal ?', '2025-10-22 18:20:17', 28, 'open');

-- --------------------------------------------------------

--
-- Table structure for table `forum_replies`
--

CREATE TABLE `forum_replies` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `author_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_replies`
--

INSERT INTO `forum_replies` (`id`, `topic_id`, `content`, `author_id`, `created_at`, `updated_at`) VALUES
(1, 2, 'I usually recommend breathing exercises and time management techniques. Breaking study material into smaller chunks helps reduce overwhelm.', 2, '2024-12-06 06:12:34', '2024-12-06 06:12:34'),
(2, 2, 'Creating a realistic study schedule at least two weeks before exams has been very helpful for my students. Also encouraging them to maintain regular sleep patterns.', 31, '2024-12-06 06:12:34', '2024-12-06 06:12:34'),
(3, 3, 'The UCSC Career Center has some excellent workshops. I also recommend connecting students with alumni in their field for informational interviews.', 2, '2024-12-08 06:12:34', '2024-12-08 06:12:34'),
(4, 4, 'I start by helping them identify the root causes - is it time management, study skills, personal issues, or course difficulty? Then we create a specific action plan together.', 31, '2024-12-09 06:12:34', '2024-12-09 06:12:34'),
(5, 5, 'Regular supervision sessions with colleagues and maintaining clear boundaries between work and personal time have been essential for me.', 2, '2024-12-09 18:12:34', '2024-12-09 18:12:34');

-- --------------------------------------------------------

--
-- Table structure for table `forum_topics`
--

CREATE TABLE `forum_topics` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category` enum('mental_health','academic','career','personal','resources') DEFAULT 'mental_health',
  `is_pinned` tinyint(1) DEFAULT 0,
  `view_count` int(11) DEFAULT 0,
  `author_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_topics`
--

INSERT INTO `forum_topics` (`id`, `title`, `content`, `category`, `is_pinned`, `view_count`, `author_id`, `created_at`, `updated_at`) VALUES
(1, 'Welcome to the Counseling Forum', 'This is a professional space for counselors to discuss mental health topics, share resources, and support each other. Please maintain confidentiality and professionalism in all discussions.', 'resources', 1, 152, 2, '2024-11-10 06:12:34', '2025-12-21 04:11:04'),
(2, 'Dealing with Student Anxiety During Exams', 'I\'ve noticed an increase in students reporting anxiety during exam periods. What strategies have you found effective for helping students manage exam-related stress?', 'mental_health', 0, 87, 2, '2024-12-05 06:12:34', '2024-12-05 06:12:34'),
(3, 'Career Guidance Resources for CS Students', 'Looking for recommendations on career counseling resources specifically tailored for Computer Science students. What tools or frameworks do you use?', 'career', 0, 43, 31, '2024-12-07 06:12:34', '2025-12-10 13:55:00'),
(4, 'Supporting Students with Academic Probation', 'How do you approach counseling sessions with students who are on academic probation? I want to be supportive while also helping them develop better study habits.', 'academic', 0, 67, 2, '2024-12-08 06:12:34', '2025-12-21 04:06:40'),
(5, 'Self-Care Tips for Counselors', 'Working with students\' mental health issues can be emotionally draining. What self-care practices do you recommend for counselors to prevent burnout?', 'personal', 0, 93, 31, '2024-12-09 06:12:34', '2024-12-09 06:12:34');

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
(30, 5, NULL),
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
(16, 'please work this time', 'Examination and Registration', '[\"mono\",\"polly\"]', 0, '2025-11-22 04:50:35', 10, 5),
(17, 'ko', 'Examination and Registration', '[\"ll;\"]', 0, '2025-12-18 04:36:18', 10, 5);

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
(37, 'private', '2025-10-19 11:37:02', 'Medical Submission', 8, 5, 'pending', 'medium', 'Im having some trouble submitting medical documents. is there any other method i can try ?', NULL, NULL),
(38, 'private', '2025-10-19 11:38:07', 'Registration Issue', 8, 5, 'resolved', 'medium', 'I cannot register for my elective modules', NULL, 10),
(39, 'private', '2025-10-19 12:55:12', 'Exam Issue 1', 8, 5, 'pending', 'medium', 'test', NULL, NULL),
(40, 'private', '2025-10-19 22:19:36', 'Wifi-for my new laptop', 8, 9, 'agent assigned', 'medium', 'I need new wifi access to my new laptop. how can i make a request ', NULL, 10),
(41, 'private', '2025-10-19 22:51:57', 'Semester registration', 8, 5, 'agent assigned', 'medium', 'i have some isssue with my semester registration. is it an issue with the web site ?', NULL, 19),
(42, 'private', '2025-10-21 04:31:36', 'Results', 6, 5, 'resolved', 'medium', 'It is showing an issue with resulsts. i cannot see my results', 'Requested', 19),
(43, 'private', '2025-10-22 11:46:41', 'Relationship Issue', 6, 10, 'pending', 'medium', 'I need help , so how this going to work ? just need idk , can i have a meeting ? ', 'Requested', NULL),
(46, 'private', '2025-10-22 15:24:57', 'Cant sleep', 8, 10, 'pending', 'medium', 'Im stressed so much , i just cant sleep at night. any tips ? or articles that i can try to read and find help?', NULL, NULL),
(48, 'private', '2025-10-22 17:46:16', 'Results of Module SCS 1306', 24, 5, 'agent assigned', 'medium', 'i want to submit re correction how can i do it ?', NULL, 19),
(49, 'private', '2025-10-22 17:48:03', 'Medical Submission', 24, 5, 'pending', 'medium', 'i missed my exam module SCS2506 , and i need to submit a medical document. how can i do it ?', NULL, NULL),
(50, 'private', '2025-10-22 17:55:02', 'ID renewal', 25, 5, 'pending', 'medium', 'i need to re usse my student ID. it looks like faded. whats the procedure', NULL, NULL),
(51, 'private', '2025-10-22 17:56:17', 'Forgot student ID during exams', 25, 5, 'pending', 'medium', 'i lost my ID. so how can i proceed with exams? what are those forms they give us ? i posted a not on lost and found section as well', NULL, NULL),
(52, 'private', '2025-10-22 17:58:28', 'Exam timetable/seat allocation inquiries', 26, 5, 'pending', 'medium', 'i cannot find my seat in the exams halls. is it changed ?', NULL, NULL),
(53, 'private', '2025-10-22 17:59:51', 'GPA calculation issues', 26, 5, 'pending', 'medium', 'i think my GPA has calculated wrongly. can i make a request to re calculate ?', NULL, NULL),
(54, 'private', '2025-10-22 18:01:21', 'Wifi connection', 26, 9, 'pending', 'medium', 'Wi-Fi signal issues are htere for my device. can i request for help ?', NULL, NULL),
(55, 'private', '2025-10-22 18:06:17', 'Requests for academic transcripts / results sheets', 27, 5, 'pending', 'medium', 'Requests for academic transcripts / results sheets . how can i proceed ?', NULL, NULL),
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
(73, 'private', '2025-10-23 18:58:23', 'Tips and Guide to Manage Anxiety', 25, 10, 'pending', 'medium', 'Im having anxiety attacks a lot can you recommend some tips for me', NULL, NULL),
(74, 'private', '2025-10-23 18:59:33', 'Help is needed', 26, 10, 'pending', 'medium', 'i just need someone to talk to. can i have a meeting?', 'Requested', NULL),
(75, 'private', '2025-10-23 19:02:04', 'Meeting Request', 12, 10, 'pending', 'medium', 'i have very personal issue to discuss can i have a meeting ?', 'Requested', NULL),
(82, 'template', '2025-11-22 07:17:43', 'please work this time', 24, 5, 'agent assigned', 'medium', '[1]. Mono: lets see if this works\n[2]. Polly: hopefully working this time , at last plss work\n', NULL, 10),
(83, 'private', '2025-11-22 07:37:35', 'wewewe', 24, 5, 'agent assigned', 'medium', 'sdsds', NULL, 19),
(84, 'template', '2025-12-13 10:34:10', 'please work this time', 24, 5, 'pending', 'medium', '[1]. Mono: testing the new heirachy\n[2]. Polly: testings\n', NULL, 0),
(85, 'private', '2025-12-18 04:42:15', 'see if works', 24, 5, 'pending', 'medium', 'asda', NULL, NULL),
(86, 'private', '2025-12-22 14:05:23', 'xdedexd', 8, 3, 'pending', 'medium', 'see', NULL, NULL),
(87, 'public', '2025-12-22 14:09:46', '1111111', 8, 3, 'pending', 'medium', '111111', NULL, NULL),
(88, 'public', '2025-12-22 14:22:56', '22222', 8, 5, 'pending', 'medium', '22222', NULL, NULL);

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
(42, 19, 'responce', '2025-10-21 04:43:10'),
(83, 10, 'clearing things out please wait', '2025-12-16 04:38:48');

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
(4, 4, '2025-08-31 08:00:00', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
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
(21, 37, '2025-10-19 11:37:02', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(22, 38, '2025-10-19 11:38:07', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(23, 39, '2025-10-19 12:55:12', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(24, 40, '2025-10-19 22:19:36', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(25, 41, '2025-10-19 22:51:57', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(26, 42, '2025-10-21 04:31:36', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(27, 43, '2025-10-22 11:46:41', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(30, 46, '2025-10-22 15:24:57', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(32, 48, '2025-10-22 17:46:16', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(33, 49, '2025-10-22 17:48:03', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(34, 50, '2025-10-22 17:55:02', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(35, 51, '2025-10-22 17:56:17', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(36, 52, '2025-10-22 17:58:28', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(37, 53, '2025-10-22 17:59:51', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(38, 54, '2025-10-22 18:01:21', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(39, 55, '2025-10-22 18:06:17', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(40, 56, '2025-10-22 18:10:40', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(41, 57, '2025-10-22 18:11:31', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(42, 58, '2025-10-22 18:12:22', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(43, 59, '2025-10-22 18:15:24', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(44, 60, '2025-10-22 18:16:26', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(45, 61, '2025-10-22 18:21:57', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(46, 62, '2025-10-22 18:23:14', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(47, 63, '2025-10-22 18:26:22', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2025-12-18 04:31:03'),
(48, 64, '2025-10-22 18:27:25', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(56, 72, '2025-10-23 18:57:09', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(57, 73, '2025-10-23 18:58:23', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(58, 74, '2025-10-23 18:59:33', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(59, 75, '2025-10-23 19:02:04', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(66, 82, '2025-11-22 07:17:43', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(67, 83, '2025-11-22 07:37:35', '2025-12-16 07:12:41', '2025-12-16 04:38:49', '2025-12-16 04:40:03', '0000-00-00 00:00:00', '2025-12-16 07:12:46', '2025-12-16 07:13:06'),
(68, 84, '2025-12-15 17:28:57', '2025-12-17 06:11:52', '2025-12-17 06:11:53', NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2025-12-17 06:11:55'),
(69, 85, '2025-12-18 04:42:15', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(70, 86, '2025-12-22 14:05:23', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(71, 87, '2025-12-22 14:09:46', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(72, 88, '2025-12-22 14:22:56', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

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
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`u_id`, `email`, `name`, `number`, `year`, `designation`, `role`, `password_hash`) VALUES
(1, 'priyantha@ucsc.edu', 'Priyantha_GA', '1234567890', NULL, 1, 'staff', '$2y$10$exampleexampleexampleexampleexampLeQWERTYUIOPasdf'),
(2, 'kamal@ucsc.edu', 'Kamal Kothalawala', '0987654321', NULL, 10, 'counselor', '$2y$10$exampleexampleexampleexampleexampLeQWERTYUIOPasdf'),
(5, 'student2@ucsc.edu', 'Nimal_Lecturer', '0723456789', '2024', NULL, 'lecturer', '$2y$10$exampleexampleexampleexampleexampLeQWERTYUIOPasdf'),
(6, '2023cs136@stu.ucsc.cmb.ac.lk', 'Kaweesha Pathirana', '0764055512', '0000', NULL, 'student', '$2y$10$URDlM7AsTgt/cnixVgOKFOg6en1IvJMiO3hX11B428nXY5zom4sKa'),
(7, 'admin@kaweesha.com', 'Admin Kaweesha', '0764055512', NULL, 0, 'admin', '$2y$10$o..U5.JB6lbOAk0ymcep1et9VOnJUsfOBJjL/eE9tyWiYje/jwsMS'),
(8, '2023cs221@stu.ucsc.cmb.ac.lk', 'Student Brian', '0712785885', NULL, 0, 'student', '$2y$10$CPS.kVFgKqkM7rtGxEtHkefrlye1c6vmvUzwX0LsFN10BiW6rkyoC'),
(9, 'satffkav@ucsc.edu', 'Staff Kavindu', '', NULL, 2, 'staff', '$2y$10$JXwb.Fiq6gcsF8wo//Q5IuBJKaUAe.nOVXy.EFU59KKJ5qXaL8vq.'),
(10, 'examsatff1@gmail.com', 'Exam', '0778084755', NULL, 3, 'staff', '$2y$10$6QzGHEj96Age7Q/Pq2.TJePbxn3IG6S2D89cGd69/zsv615EFOq/m'),
(11, 'student@ben.com', 'Namal Jayawardana', '', NULL, 0, 'student', '$2y$10$B4gpIaw.cBrLkmXGIfaen.ZmOftQxT6lWuM6g0dCI84ZusyTVdEjG'),
(12, 'dakshina@gmail.com', 'Dakshina', '0812456378', '0000', NULL, 'student', '$2y$10$gZmNzLETbVJx28MfBXRGgOK28QBoJQZRP3UO0pVLpmzIe9TsdwDSu'),
(13, 'Praveen@gmail.com', 'Praveen', '076598231', NULL, 10, 'counselor', '$2y$10$.qm/ab0YxcqZPPEZNsVkku7V5F02yc4Rh6o6BFu75tC.mxlBP15AK'),
(14, 'Naweesha@gmail.com', 'Naweesha_PostGrad', '0812377212', NULL, 4, 'staff', '$2y$10$V/xLz1Y9aO16io2otOH3guxS.OqNhkK8.NRSN2sz3rTDDwCglKZQq'),
(15, 'staff@kumara.com', 'kumara_NOC', '', NULL, 9, 'staff', '$2y$10$KwL6GsxaER6gVkkmPEz3M.uFgyuwspJ7R7e3fiDZOODXOer/RWPZe'),
(16, 'examstaffanjalee@gmail.com', 'Anajalee_Exam', '', NULL, 5, 'staff', '$2y$10$KcqHWJzcY6qM8z9jTHrGAOPJfeEkryqp9CFc6GfEhLDBFBpZj8i1a'),
(17, 'postgrad@gmail.com', 'staff_postGrad', '0112365478', NULL, 4, 'staff', '$2y$10$Xz6q.YwDP7v9dXmDxdSmjO3bqIGH5gsUWbG97hMy4MaxRrxUPG0tS'),
(18, 'counselor@brian.com', 'Dinuni', '', NULL, 0, 'counselor', '$2y$10$f/VbB1VcAfinJt6EEdaBw.HNbCS328CZxo8uMkOzCIGpvW1PJjY/G'),
(19, 'examstaff2@gmail.com', 'staff2_Exam', '1234567892', NULL, 5, 'staff', '$2y$10$ZfDRH8PfYbkUtC.GUwSMZeVU07alUkJoAnid7aDDWMDDhTKXm8sI6'),
(20, 'namal@gmail.com', 'Namal Dickwalla', '0764055512', NULL, 10, 'counselor', '$2y$10$WsIbDTLBSeggVlqcQVzUDuw9y2S..RsQUlcjtBzHc.t8PRZihluLK'),
(21, 'tharushi@gmail.com', 'Tharushi', '', '0000', NULL, 'counselor', '$2y$10$rKlxA4QfyP19n3qCSX9ci.FNxVgG3qeOIjx4ilASKxy357yN243.O'),
(23, 'coun1@gmail.com', 'counselor1', '02345678915', NULL, NULL, 'counselor', '$2y$10$3C0VjyuVikYv945osZetw.3dp8ePBlhAtO6INuJHV4goelAeXxTHG'),
(24, 'student1@gmail.com', 'student1', '07756423956', '2023', NULL, 'student', '$2y$10$UtoxfTzTPNIitcOZuXYLF.FPRfP.p28eEpWYLfm1t0Y.o4f8/.cqi'),
(25, 'student2@gmail.com', 'student2', '07895623145', '2022', NULL, 'student', '$2y$10$Nt4CTo/oRosjNwC4klkcNuOUgT1si7o/WlVSuwG/VWZzcBMBi2qaO'),
(26, 'student3@gmail.com', 'student3', '0789563214', '2022', NULL, 'student', '$2y$10$8Q2SeYwmRZ3vyCATGdGz7eZWfZ5LunFf4JaHvU.0N/AiY292E0rH2'),
(27, 'student4@gmail.com', 'student4', '011236547892', '2023', NULL, 'student', '$2y$10$./uUkgp9tb/eOEp6IdDQX.2SaGrTWV3TwNAYQJGHWAE2RhZP8SLy6'),
(28, 'student5@gmail.com', 'student5', '0745693246', '2024', NULL, 'student', '$2y$10$Tw6NNq/3ntz1T8SI0SwmeeMUeDILrJiJ7PhIiKhj0jvaF03Va3rv6'),
(29, 'nocstaff@gmail.com', 'NocStaff', '0728965412', NULL, 9, 'staff', '$2y$10$0s4kdniSjrGt4qPJm5zXtewAXeeWeF4Ln5WFzyA2lVSjht9Nk7HAW'),
(30, 'staff@brian.com', 'Brian', '', NULL, 5, 'staff', '$2y$10$edJ3C8ECxUKr1qbh9jUCPuSSGSLyHl0CaYIxASex.DrTy7U5iQSF2'),
(31, 'counsellor@brian.com', 'Brian', '', NULL, 10, 'counselor', '$2y$10$VUWaoot845mznIVieoMzl.PAzAW1lMTwA3eqFTJMP4sbs/mhDn1D2'),
(32, 'exam@staff.com', 'Brian', '', '2001', 5, 'staff', '$2y$10$3/zq5TlpFeTJw.H1.FiZ1OpbkEZytwIRZ6CXX0yaI4blrVOHu5jpm');

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
-- Indexes for table `counselors`
--
ALTER TABLE `counselors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_counselors_user_id` (`user_id`);

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
-- Indexes for table `forum_q`
--
ALTER TABLE `forum_q`
  ADD PRIMARY KEY (`q_id`),
  ADD KEY `u_id` (`u_id`);

--
-- Indexes for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_topic` (`topic_id`),
  ADD KEY `idx_author` (`author_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_author` (`author_id`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_pinned` (`is_pinned`);

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
  ADD KEY `staff_division_h_level_id` (`h_id`);

--
-- Indexes for table `staff_hierachy`
--
ALTER TABLE `staff_hierachy`
  ADD PRIMARY KEY (`h_id`),
  ADD KEY `hirechy_did_constarint` (`did`);

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
  ADD KEY `u_id` (`u_id`);

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
  ADD KEY `idx_users_role` (`role`);

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
-- AUTO_INCREMENT for table `counselors`
--
ALTER TABLE `counselors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `division`
--
ALTER TABLE `division`
  MODIFY `did` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `forum_q`
--
ALTER TABLE `forum_q`
  MODIFY `q_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `forum_replies`
--
ALTER TABLE `forum_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `forum_topics`
--
ALTER TABLE `forum_topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `news_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `ticket_chat`
--
ALTER TABLE `ticket_chat`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_messages`
--
ALTER TABLE `ticket_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_timeline`
--
ALTER TABLE `ticket_timeline`
  MODIFY `timeline_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `u_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

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
-- Constraints for table `forum_q`
--
ALTER TABLE `forum_q`
  ADD CONSTRAINT `forum_q_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD CONSTRAINT `fk_forum_replies_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_forum_replies_topic` FOREIGN KEY (`topic_id`) REFERENCES `forum_topics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD CONSTRAINT `fk_forum_topics_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

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
