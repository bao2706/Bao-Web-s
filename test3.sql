-- Personal Finance Tracker database schema
-- Import this file into MySQL/MariaDB before running the PHP app.

CREATE DATABASE IF NOT EXISTS `test3`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `test3`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `income`;
DROP TABLE IF EXISTS `members`;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `members` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `email` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `members_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `income` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `amount` decimal(12,2) NOT NULL,
    `category` varchar(100) NOT NULL,
    `type` enum('income','expense') NOT NULL,
    `content` varchar(255) DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `income_user_created_at_index` (`user_id`, `created_at`),
    CONSTRAINT `income_user_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `members` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
