
DROP TABLE IF EXISTS `forum_replies`;
DROP TABLE IF EXISTS `forum_topics`;

CREATE TABLE `forum_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `u_id` (`u_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `fk_fc_post` FOREIGN KEY (`post_id`) REFERENCES `forum_q` (`q_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fc_user` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fc_parent` FOREIGN KEY (`parent_id`) REFERENCES `forum_comments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
