-- Grease Monkey Database Backup
-- Generated on: 2026-03-12 04:59:47 PM
-- ---------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+08:00' */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

-- ---------------------------------------------------------
-- Drop all tables in safe order (children first)
-- ---------------------------------------------------------

/*!40014 SET FOREIGN_KEY_CHECKS=0 */;
DROP TABLE IF EXISTS `stock_logs`;
DROP TABLE IF EXISTS `password_resets`;
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `login_attempts`;
DROP TABLE IF EXISTS `categories`;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;


-- ---------------------------------------------------------
-- Table structure for `categories`
-- ---------------------------------------------------------

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `sku_prefix` varchar(20) NOT NULL DEFAULT 'PROD',
  `requires_oem` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_name` (`category_name`),
  UNIQUE KEY `sku_prefix` (`sku_prefix`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for `categories`

INSERT INTO `categories` VALUES
  ('1', 'Fluids', 'FLUIDS', '0', '2026-03-12 16:23:31');


-- ---------------------------------------------------------
-- Table structure for `login_attempts`
-- ---------------------------------------------------------

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `attempted_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- No data in `login_attempts`


-- ---------------------------------------------------------
-- Table structure for `products`
-- ---------------------------------------------------------

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `sku` varchar(50) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `part_number` varchar(100) DEFAULT NULL,
  `applicable_models` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_image` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  UNIQUE KEY `part_number` (`part_number`),
  UNIQUE KEY `unique_per_model` (`part_number`,`applicable_models`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for `products`

INSERT INTO `products` VALUES
  ('1', '1', 'FLUIDS-001', 'Engine Oil', NULL, '1', '1', '1.00', '11', '2026-03-12 16:58:33', NULL, '0', NULL);


-- ---------------------------------------------------------
-- Table structure for `users`
-- ---------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `password_updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for `users`

INSERT INTO `users` VALUES
  ('1', 'greasemonkey', 'greasemonkey09876@gmail.com', '$2y$10$.pKskYZjaNs3NZicUek/j.ea7l0u23zn7HxaC6fnCOjUFb1NzFqL.', 'admin', '2026-03-12 16:20:50', '2026-03-12 16:23:10');


-- ---------------------------------------------------------
-- Table structure for `activity_logs`
-- ---------------------------------------------------------

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `activity` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `fk_activity_product` (`product_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_activity_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for `activity_logs`

INSERT INTO `activity_logs` VALUES
  ('1', '1', NULL, 'Reset password', '2026-03-12 16:23:10', '0', NULL, NULL),
  ('2', '1', NULL, 'Login Account', '2026-03-12 16:23:22', '0', NULL, NULL),
  ('3', '1', NULL, 'Generated Database Backup: greasemonkey_backup_2026-03-12_16-34-58.sql', '2026-03-12 16:34:58', '0', NULL, NULL),
  ('4', '1', NULL, 'Generated Database Backup: greasemonkey_backup_2026-03-12_16-35-36.sql', '2026-03-12 16:35:36', '0', NULL, NULL),
  ('5', '1', NULL, 'Generated Database Backup: greasemonkey_backup_2026-03-12_04-39-02-PM.sql', '2026-03-12 16:39:02', '0', NULL, NULL),
  ('6', '1', NULL, 'Generated Database Backup: greasemonkey_backup_2026-03-12_04-40-08-PM.sql', '2026-03-12 16:40:08', '0', NULL, NULL),
  ('7', '1', NULL, 'Imported Database Backup: greasemonkey_backup_2026-03-12_04-52-15-PM.sql', '2026-03-12 16:57:46', '0', NULL, NULL),
  ('8', '1', NULL, 'Imported Database Backup: greasemonkey_backup_2026-03-12_04-57-55-PM.sql', '2026-03-12 16:58:06', '0', NULL, NULL),
  ('9', '1', '1', 'Added product #1 - Engine Oil', '2026-03-12 16:58:33', '0', NULL, NULL);


-- ---------------------------------------------------------
-- Table structure for `password_resets`
-- ---------------------------------------------------------

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for `password_resets`

INSERT INTO `password_resets` VALUES
  ('1', '1', '8a2ad6859d365c0e5cb028f1110eb57dfa71a0e804f1f88978aba4da28e0b0d5', '2026-03-12 16:32:52', '1', '2026-03-12 16:22:52');


-- ---------------------------------------------------------
-- Table structure for `stock_logs`
-- ---------------------------------------------------------

CREATE TABLE `stock_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `action` enum('IN','OUT','ARCHIVE') NOT NULL,
  `quantity` int(11) NOT NULL,
  `balance_before` int(11) NOT NULL,
  `balance_after` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `stock_logs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for `stock_logs`

INSERT INTO `stock_logs` VALUES
  ('1', '1', 'IN', '10', '0', '10', '1', 'Stock added', '2026-03-12 16:58:40', '0', NULL, NULL),
  ('2', '1', 'IN', '1', '10', '11', '1', 'Stock added', '2026-03-12 16:58:44', '0', NULL, NULL);

-- ---------------------------------------------------------
-- Restore original settings
-- ---------------------------------------------------------

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
