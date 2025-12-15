-- StageOne Modeling Portal Database Schema
-- Version 1.0

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('user', 'moderator', 'admin') DEFAULT 'user',
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `equipment` text DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `is_online` tinyint(1) DEFAULT 0,
  `last_active` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_username` (`username`),
  INDEX `idx_email` (`email`),
  INDEX `idx_last_active` (`last_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories table
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL UNIQUE,
  `description` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions table
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `session_date` date DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `status` enum('draft', 'private', 'public') DEFAULT 'draft',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_created_at` (`created_at`),
  CONSTRAINT `fk_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Session categories junction table
CREATE TABLE IF NOT EXISTS `session_categories` (
  `session_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`session_id`, `category_id`),
  CONSTRAINT `fk_session_categories_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_session_categories_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Images table
CREATE TABLE IF NOT EXISTS `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_session_id` (`session_id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_sort_order` (`sort_order`),
  CONSTRAINT `fk_images_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_images_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Session likes table
CREATE TABLE IF NOT EXISTS `session_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_session_user` (`session_id`, `user_id`),
  INDEX `idx_session_id` (`session_id`),
  INDEX `idx_user_id` (`user_id`),
  CONSTRAINT `fk_session_likes_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_session_likes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Image likes table
CREATE TABLE IF NOT EXISTS `image_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_image_user` (`image_id`, `user_id`),
  INDEX `idx_image_id` (`image_id`),
  INDEX `idx_user_id` (`user_id`),
  CONSTRAINT `fk_image_likes_image` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_image_likes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Image ratings table
CREATE TABLE IF NOT EXISTS `image_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_image_user_rating` (`image_id`, `user_id`),
  INDEX `idx_image_id` (`image_id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_rating` (`rating`),
  CONSTRAINT `fk_image_ratings_image` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_image_ratings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Session comments table
CREATE TABLE IF NOT EXISTS `session_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `status` enum('pending', 'approved', 'rejected') DEFAULT 'pending',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_session_id` (`session_id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_status` (`status`),
  CONSTRAINT `fk_session_comments_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_session_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Followers table
CREATE TABLE IF NOT EXISTS `followers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `follower_id` int(11) NOT NULL,
  `followed_id` int(11) NOT NULL,
  `status` enum('pending', 'active', 'blocked') DEFAULT 'active',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_follower_followed` (`follower_id`, `followed_id`),
  INDEX `idx_follower_id` (`follower_id`),
  INDEX `idx_followed_id` (`followed_id`),
  CONSTRAINT `fk_followers_follower` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_followers_followed` FOREIGN KEY (`followed_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- References table
CREATE TABLE IF NOT EXISTS `references` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_user_id` int(11) NOT NULL,
  `to_user_id` int(11) NOT NULL,
  `reference_text` text NOT NULL,
  `status` enum('pending', 'approved', 'rejected') DEFAULT 'pending',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_from_user` (`from_user_id`),
  INDEX `idx_to_user` (`to_user_id`),
  INDEX `idx_status` (`status`),
  CONSTRAINT `fk_references_from_user` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_references_to_user` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Messages table
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `encrypted_message` text DEFAULT NULL,
  `encryption_iv` varchar(255) DEFAULT NULL,
  `encryption_tag` varchar(255) DEFAULT NULL,
  `is_encrypted` tinyint(1) DEFAULT 0,
  `is_read` tinyint(1) DEFAULT 0,
  `is_deleted_sender` tinyint(1) DEFAULT 0,
  `is_deleted_recipient` tinyint(1) DEFAULT 0,
  `sent_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `read_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_sender` (`sender_id`),
  INDEX `idx_recipient` (`recipient_id`),
  INDEX `idx_sent_at` (`sent_at`),
  INDEX `idx_conversation` (`sender_id`, `recipient_id`),
  CONSTRAINT `fk_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_messages_recipient` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications table
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `from_user_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_is_read` (`is_read`),
  INDEX `idx_created_at` (`created_at`),
  CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_notifications_from_user` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Castings table
CREATE TABLE IF NOT EXISTS `castings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `requirements` text DEFAULT NULL,
  `budget` varchar(100) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` enum('open', 'closed', 'filled') DEFAULT 'open',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_deadline` (`deadline`),
  CONSTRAINT `fk_castings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Casting categories junction table
CREATE TABLE IF NOT EXISTS `casting_categories` (
  `casting_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`casting_id`, `category_id`),
  CONSTRAINT `fk_casting_categories_casting` FOREIGN KEY (`casting_id`) REFERENCES `castings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_casting_categories_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Casting applications table
CREATE TABLE IF NOT EXISTS `casting_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `casting_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `application_text` text DEFAULT NULL,
  `cv_file` varchar(255) DEFAULT NULL,
  `portfolio_files` text DEFAULT NULL,
  `status` enum('pending', 'accepted', 'rejected') DEFAULT 'pending',
  `applied_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_casting_user` (`casting_id`, `user_id`),
  INDEX `idx_casting_id` (`casting_id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_status` (`status`),
  CONSTRAINT `fk_casting_applications_casting` FOREIGN KEY (`casting_id`) REFERENCES `castings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_casting_applications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Casting favorites table
CREATE TABLE IF NOT EXISTS `casting_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `casting_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_casting_user_favorite` (`casting_id`, `user_id`),
  INDEX `idx_casting_id` (`casting_id`),
  INDEX `idx_user_id` (`user_id`),
  CONSTRAINT `fk_casting_favorites_casting` FOREIGN KEY (`casting_id`) REFERENCES `castings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_casting_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Session collaborators table
CREATE TABLE IF NOT EXISTS `session_collaborators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(100) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_session_id` (`session_id`),
  INDEX `idx_user_id` (`user_id`),
  CONSTRAINT `fk_session_collaborators_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_session_collaborators_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Image collaborators table
CREATE TABLE IF NOT EXISTS `image_collaborators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(100) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_image_id` (`image_id`),
  INDEX `idx_user_id` (`user_id`),
  CONSTRAINT `fk_image_collaborators_image` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_image_collaborators_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Moderator permissions table
CREATE TABLE IF NOT EXISTS `moderator_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `permission` varchar(100) NOT NULL,
  `granted_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_permission` (`user_id`, `permission`),
  INDEX `idx_user_id` (`user_id`),
  CONSTRAINT `fk_moderator_permissions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions view for public access
CREATE OR REPLACE VIEW `public_sessions_view` AS
SELECT 
    s.id,
    s.user_id,
    s.title,
    s.description,
    s.location,
    s.session_date,
    s.cover_image,
    s.created_at,
    u.username,
    u.avatar,
    (SELECT COUNT(*) FROM session_likes sl WHERE sl.session_id = s.id) AS likes_count,
    (SELECT COUNT(*) FROM session_comments sc WHERE sc.session_id = s.id AND sc.status = 'approved') AS comments_count
FROM sessions s
JOIN users u ON s.user_id = u.id
WHERE s.status = 'public';

SET FOREIGN_KEY_CHECKS = 1;