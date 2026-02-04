-- SQL for meeting schedules and calendar events tables
-- Run this to add meeting scheduling functionality

-- Table structure for table `meeting_schedules`
CREATE TABLE IF NOT EXISTS `meeting_schedules` (
  `meeting_id` int(11) NOT NULL AUTO_INCREMENT,
  `counselor_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `meeting_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'Duration in minutes',
  `mode` enum('online','in-person','phone') NOT NULL DEFAULT 'online',
  `room_location` varchar(255) DEFAULT NULL,
  `meeting_link` varchar(500) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('scheduled','completed','cancelled','no-show') NOT NULL DEFAULT 'scheduled',
  `event_id` int(11) DEFAULT NULL COMMENT 'Link to calendar_events table',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`meeting_id`),
  KEY `idx_counselor` (`counselor_id`),
  KEY `idx_student` (`student_id`),
  KEY `idx_ticket` (`ticket_id`),
  KEY `idx_meeting_date` (`meeting_date`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_meeting_counselor` FOREIGN KEY (`counselor_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_meeting_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`u_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_meeting_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `calendar_events`
CREATE TABLE IF NOT EXISTS `calendar_events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_id` int(11) NOT NULL COMMENT 'User who owns this event',
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `is_all_day` tinyint(1) NOT NULL DEFAULT 0,
  `color` varchar(20) DEFAULT '#3b82f6' COMMENT 'Color for calendar display',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`event_id`),
  KEY `idx_user` (`u_id`),
  KEY `idx_event_date` (`event_date`),
  CONSTRAINT `fk_calendar_user` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add event_id foreign key to meeting_schedules after calendar_events is created
ALTER TABLE `meeting_schedules`
  ADD CONSTRAINT `fk_meeting_event` FOREIGN KEY (`event_id`) REFERENCES `calendar_events` (`event_id`) ON DELETE SET NULL;
