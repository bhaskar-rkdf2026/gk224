-- =====================================================================
-- GK224.COM - MySQL Database Schema Export
-- Import this file in phpMyAdmin or MySQL CLI
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `gk224_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `gk224_db`;

-- 1. USERS TABLE
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) UNIQUE NOT NULL,
  `phone` VARCHAR(20) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `upi_id` VARCHAR(100) DEFAULT NULL,
  `avatar` VARCHAR(50) DEFAULT '👤',
  `balance` DECIMAL(12,2) NOT NULL DEFAULT 10000.00,
  `is_pro` TINYINT(1) NOT NULL DEFAULT 0,
  `pro_plan` VARCHAR(20) DEFAULT NULL,
  `kyc_status` ENUM('pending', 'review', 'verified') NOT NULL DEFAULT 'pending',
  `kyc_type` VARCHAR(50) DEFAULT NULL,
  `kyc_number` VARCHAR(100) DEFAULT NULL,
  `security_pin` VARCHAR(255) DEFAULT NULL,
  `referral_code` VARCHAR(20) UNIQUE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. TRANSACTIONS TABLE
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `txn_id` VARCHAR(50) UNIQUE NOT NULL,
  `type` ENUM('payment', 'earning', 'travel', 'learning') NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `category` VARCHAR(50) DEFAULT 'other',
  `status` ENUM('success', 'pending', 'failed') NOT NULL DEFAULT 'success',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. LUCKY SCRATCHPAD CARDS TABLE
CREATE TABLE IF NOT EXISTS `scratch_cards` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `card_code` VARCHAR(50) UNIQUE NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `reward` DECIMAL(10,2) NOT NULL,
  `status` ENUM('unscratched', 'claimed') NOT NULL DEFAULT 'unscratched',
  `claimed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. VILLAGE STUDENT SURVEYS TABLE
CREATE TABLE IF NOT EXISTS `survey_entries` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `student_name` VARCHAR(100) NOT NULL,
  `age` INT DEFAULT NULL,
  `class_name` VARCHAR(50) DEFAULT NULL,
  `village_name` VARCHAR(100) NOT NULL,
  `parent_name` VARCHAR(100) DEFAULT NULL,
  `contact` VARCHAR(20) DEFAULT NULL,
  `gender` ENUM('Male', 'Female', 'Other') DEFAULT NULL,
  `reward_paid` DECIMAL(8,2) NOT NULL DEFAULT 10.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. REFERRALS & LEADERBOARD TABLE
CREATE TABLE IF NOT EXISTS `referrals` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `referrer_id` INT UNSIGNED NOT NULL,
  `friend_name` VARCHAR(100) NOT NULL,
  `points_awarded` INT NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`referrer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Demo Admin / Test User (Password: demo1234)
INSERT INTO `users` (`name`, `email`, `phone`, `password`, `balance`, `referral_code`)
VALUES ('Demo User', 'demo@gk224.com', '+91 90000 00000', '$2y$10$eA32bO59K1X/Zz9m7i6XEuGj4uKkM5WpL8RkK5d6i7j8k9l0m1n2o', 10000.00, 'GK224DEMO')
ON DUPLICATE KEY UPDATE `id`=`id`;
