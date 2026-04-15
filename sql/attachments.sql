CREATE TABLE IF NOT EXISTS `attachments` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `entity_type` VARCHAR(50) NOT NULL COMMENT 'e.g., ticket, forum, kb, announcement',
    `entity_id` INT(11) NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_type` VARCHAR(100) NOT NULL,
    `file_size` INT(11) NOT NULL,
    `uploaded_by` INT(11) NOT NULL,
    `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
