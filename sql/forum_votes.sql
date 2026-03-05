-- Create table for storing votes
CREATE TABLE IF NOT EXISTS `forum_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `vote_type` tinyint(2) NOT NULL DEFAULT 0 COMMENT '1: up, -1: down',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_vote` (`post_id`, `u_id`),
  CONSTRAINT `fk_forum_votes_post` FOREIGN KEY (`post_id`) REFERENCES `forum_q` (`q_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_forum_votes_user` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optionally add cache columns to forum_q for performance (optional, we can count on fly for now)
-- ALTER TABLE `forum_q` ADD COLUMN `upvotes` INT DEFAULT 0, ADD COLUMN `downvotes` INT DEFAULT 0;
