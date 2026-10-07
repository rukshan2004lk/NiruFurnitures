-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.42 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for niru_furniture
CREATE DATABASE IF NOT EXISTS `niru_furniture` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `niru_furniture`;

-- Dumping structure for table niru_furniture.cards
CREATE TABLE IF NOT EXISTS `cards` (
  `id` int DEFAULT NULL,
  `number` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exp_num` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cvv` int DEFAULT NULL,
  `amount` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table niru_furniture.cards: ~0 rows (approximately)
DELETE FROM `cards`;
INSERT INTO `cards` (`id`, `number`, `exp_num`, `cvv`, `amount`) VALUES
	(1, '4242424242424242', '12/28', 123, 552633);

-- Dumping structure for table niru_furniture.carts
CREATE TABLE IF NOT EXISTS `carts` (
  `cart_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `session_token` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `idx_carts_user` (`user_id`),
  CONSTRAINT `fk_carts_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table niru_furniture.carts: ~3 rows (approximately)
DELETE FROM `carts`;
INSERT INTO `carts` (`cart_id`, `user_id`, `session_token`, `created_at`, `updated_at`) VALUES
	(1, 2, 'sess_8a7f23c91b4e05da', '2026-09-06 20:53:26', '2026-09-06 20:53:26'),
	(2, 3, 'sess_1c5d98e72f3a64b0', '2026-09-06 20:53:26', '2026-09-06 20:53:26'),
	(3, 1, 'sess_a0bf10236a8490c9', '2026-09-06 21:21:51', '2026-09-06 21:21:51');

-- Dumping structure for table niru_furniture.cart_items
CREATE TABLE IF NOT EXISTS `cart_items` (
  `cart_item_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `unit_price` decimal(12,2) NOT NULL,
  `color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cart_item_id`),
  UNIQUE KEY `uq_cart_product` (`cart_id`,`product_id`),
  KEY `idx_cart_items_product` (`product_id`),
  CONSTRAINT `fk_cart_items_cart` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`cart_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cart_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table niru_furniture.cart_items: ~0 rows (approximately)
DELETE FROM `cart_items`;

-- Dumping structure for table niru_furniture.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `uq_category_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table niru_furniture.categories: ~6 rows (approximately)
DELETE FROM `categories`;
INSERT INTO `categories` (`category_id`, `name`, `description`, `image_path`, `status`, `created_at`) VALUES
	(1, 'Living Room', 'Furniture for comfortable and elegant living spaces.', 'Images/Category/chair.png', 'active', '2026-09-06 20:53:26'),
	(2, 'Bedroom', 'Beds and bedroom furniture designed for comfort.', 'Images/Category/bed.png', 'active', '2026-09-06 20:53:26'),
	(3, 'Dining & Kitchen', 'Dining tables, chairs and kitchen furniture.', 'Images/Category/table.png', 'active', '2026-09-06 20:53:26'),
	(4, 'Office', 'Modern furniture for productive workspaces.', 'Images/Category/office.png', 'active', '2026-09-06 20:53:26'),
	(5, 'Lighting', 'Decorative and functional lighting.', 'Images/Category/lighting.png', 'active', '2026-09-06 20:53:26'),
	(6, 'Storage', 'Sideboards and practical storage furniture.', 'Images/Category/storage.png', 'active', '2026-09-06 20:53:26');

-- Dumping structure for table niru_furniture.inventory
CREATE TABLE IF NOT EXISTS `inventory` (
  `inventory_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`inventory_id`),
  UNIQUE KEY `uq_inventory_product` (`product_id`),
  CONSTRAINT `fk_inventory_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table niru_furniture.inventory: ~6 rows (approximately)
DELETE FROM `inventory`;
INSERT INTO `inventory` (`inventory_id`, `product_id`, `quantity`, `updated_at`, `last_updated`) VALUES
	(1, 1, 23, '2026-10-03 00:35:28', '2026-10-03 00:57:27'),
	(2, 2, 5, '2026-09-06 20:53:26', '2026-10-03 00:57:27'),
	(3, 3, 0, '2026-09-06 20:53:26', '2026-10-03 00:57:27'),
	(4, 4, 16, '2026-10-03 00:19:17', '2026-10-03 00:57:27'),
	(5, 5, 10, '2026-09-06 21:28:08', '2026-10-03 00:57:27'),
	(6, 6, 7, '2026-10-03 00:58:44', '2026-10-03 00:58:44'),
	(7, 7, 3, '2026-10-03 01:08:50', '2026-10-03 01:08:50');

-- Dumping structure for table niru_furniture.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `payment_status` enum('pending','paid','failed','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `order_status_id` int unsigned NOT NULL DEFAULT '1',
  `customer_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_postal_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `placed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `uq_orders_number` (`order_number`),
  KEY `idx_orders_user` (`user_id`),
  KEY `fk_orders_order_status` (`order_status_id`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table niru_furniture.orders: ~9 rows (approximately)
DELETE FROM `orders`;
INSERT INTO `orders` (`order_id`, `order_number`, `user_id`, `total_amount`, `payment_status`, `order_status_id`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `shipping_city`, `shipping_postal_code`, `placed_at`) VALUES
	(1, 'ORD-20260901-001', 2, 1670.00, 'paid', 9, 'Kasun Perera', 'kasun.perera@example.com', '0771234567', '45 Galle Road, Bambalapitiya', 'Colombo', '00400', '2026-09-06 20:53:26'),
	(2, 'ORD-20260905-002', 3, 1850.00, 'pending', 6, 'Nadeesha Fernando', 'nadeesha.f@example.com', '0719876543', '12 Kandy Road, Peradeniya', 'Kandy', '20400', '2026-09-06 20:53:26'),
	(3, 'ORD-20260906-E4F838', 1, 1850.00, 'paid', 6, 'Sandeesha Rukshan', 'sandeesharukshan321@gmail.com', '0713218157', 'E2-612/B, NEW TOWN, EMBILIPITIYA', 'Embilipitiya', '70200', '2026-09-06 21:14:14'),
	(4, 'ORD-20260906-03C318', 1, 5660.00, 'paid', 7, 'Sandeesha Rukshan', 'sandeesharukshan321@gmail.com', '0713218157', 'E2-612/B, NEW TOWN, EMBILIPITIYA', 'Embilipitiya', '70200', '2026-09-06 21:28:08'),
	(5, 'ORD-20260906-23489F', 1, 1850.00, 'paid', 6, 'Sandeesha Rukshan', 'sandeesharukshan321@gmail.com', '0713218157', 'E2-612/B, NEW TOWN, EMBILIPITIYA', 'Embilipitiya', '70200', '2026-09-06 23:28:26'),
	(6, 'ORD-20260906-40FE50', 1, 420.00, 'paid', 8, 'Sandeesha Rukshan', 'sandeesharukshan321@gmail.com', '0713218157', 'E2-612/B, NEW TOWN, EMBILIPITIYA', 'Embilipitiya', '70200', '2026-09-06 23:43:56'),
	(7, 'ORD-20261002-EEBB82', 1, 980.00, 'paid', 11, 'Sandeesha Rukshan', 'sandeesharukshan321@gmail.com', '0713218157', 'E2-612/B,NEW TOWN, EMBILIPITIYA', 'Embilipitiya', '70200', '2026-10-02 14:23:18'),
	(9, 'ORD-20261002-FBCD79', 1, 420.00, 'paid', 6, 'Sandeesha Rukshan', 'sandeesharukshan321@gmail.com', '0713218157', 'E2-612/B,NEW TOWN, EMBILIPITIYA', 'Embilipitiya', '70200', '2026-10-03 00:17:27'),
	(10, 'ORD-20261002-D28747', 1, 420.00, 'paid', 6, 'Sandeesha Rukshan', 'sandeesharukshan321@gmail.com', '0713218157', 'E2-612/B,NEW TOWN, EMBILIPITIYA', 'Embilipitiya', '70200', '2026-10-03 00:19:17'),
	(11, 'ORD-20261002-893988', 1, 1250.00, 'paid', 6, 'Sandeesha Rukshan', 'sandeesharukshan321@gmail.com', '0713218157', 'E2-612/B,NEW TOWN, EMBILIPITIYA', 'Embilipitiya', '70200', '2026-10-03 00:35:28');

-- Dumping structure for table niru_furniture.order_items
CREATE TABLE IF NOT EXISTS `order_items` (
  `order_item_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `product_name` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `unit_price` decimal(12,2) NOT NULL,
  `line_total` decimal(12,2) NOT NULL,
  `color` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `fk_order_items_product` (`product_id`),
  KEY `idx_order_items_order` (`order_id`),
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table niru_furniture.order_items: ~8 rows (approximately)
DELETE FROM `order_items`;
INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `product_name`, `quantity`, `unit_price`, `line_total`, `color`) VALUES
	(1, 1, 1, 'Nordic Lounge Chair', 1, 1250.00, 1250.00, NULL),
	(2, 1, 4, 'Aura Floor Lamp', 1, 420.00, 420.00, NULL),
	(3, 2, 6, 'Serene Sleep Bed', 1, 1850.00, 1850.00, NULL),
	(4, 3, 6, 'Serene Sleep Bed', 1, 1850.00, 1850.00, NULL),
	(5, 4, 5, 'Linear Sideboard', 2, 980.00, 1960.00, NULL),
	(6, 4, 6, 'Serene Sleep Bed', 2, 1850.00, 3700.00, NULL),
	(7, 5, 6, 'Serene Sleep Bed', 1, 1850.00, 1850.00, NULL),
	(8, 6, 4, 'Aura Floor Lamp (Warm Saddle)', 1, 420.00, 420.00, 'Warm Saddle'),
	(9, 10, 4, 'Aura Floor Lamp (Warm Saddle)', 1, 420.00, 420.00, 'Warm Saddle'),
	(10, 11, 1, 'Nordic Lounge Chair (Oatmeal Cream)', 1, 1250.00, 1250.00, 'Oatmeal Cream');

-- Dumping structure for table niru_furniture.payments
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `payment_method` enum('card','cash_on_delivery','bank_transfer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'card',
  `amount` decimal(12,2) NOT NULL,
  `status` enum('pending','paid','failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `transaction_id` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `fk_payments_order` (`order_id`),
  CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table niru_furniture.payments: ~8 rows (approximately)
DELETE FROM `payments`;
INSERT INTO `payments` (`payment_id`, `order_id`, `payment_method`, `amount`, `status`, `transaction_id`, `paid_at`) VALUES
	(1, 1, 'card', 1670.00, 'paid', 'TXN_99812401823', '2026-09-01 14:32:10'),
	(2, 2, 'cash_on_delivery', 1850.00, 'pending', NULL, NULL),
	(3, 3, 'card', 1850.00, 'paid', 'TXN_D84DC7567D0E', '2026-09-06 21:14:14'),
	(4, 4, 'card', 5660.00, 'paid', 'TXN_346A17820478', '2026-09-06 21:28:08'),
	(5, 5, 'card', 1850.00, 'paid', 'TXN_E8DACEB493BD', '2026-09-06 23:28:26'),
	(6, 6, 'card', 420.00, 'paid', 'TXN_763E066465F5', '2026-09-06 23:43:56'),
	(7, 10, 'card', 420.00, 'paid', 'TXN_9F86D17753CF', '2026-10-03 00:19:17'),
	(8, 11, 'card', 1250.00, 'paid', 'TXN_70CFFAF1FCCF', '2026-10-03 00:35:28');

-- Dumping structure for table niru_furniture.products
CREATE TABLE IF NOT EXISTS `products` (
  `product_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `name` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `status_id` int unsigned NOT NULL DEFAULT '4',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`),
  KEY `fk_products_category` (`category_id`),
  KEY `fk_products_status` (`status_id`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table niru_furniture.products: ~7 rows (approximately)
DELETE FROM `products`;
INSERT INTO `products` (`product_id`, `category_id`, `name`, `description`, `price`, `featured`, `status_id`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Nordic Lounge Chair', 'European Oak and wool-blend fabric.', 1250.00, 1, 1, '2026-09-06 20:53:26', '2026-09-06 20:54:57'),
	(2, 3, 'Elysian Oak Table', 'Refined dining table designed around natural wood grain.', 1450.00, 1, 1, '2026-09-06 20:53:26', '2026-09-06 20:54:57'),
	(3, 1, 'Obelisk Sofa', 'Modular-inspired statement sofa.', 2800.00, 1, 1, '2026-09-06 20:53:26', '2026-10-02 20:40:29'),
	(4, 5, 'Aura Floor Lamp', 'Modern minimalist floor lamp.', 420.00, 1, 1, '2026-09-06 20:53:26', '2026-09-06 20:54:57'),
	(5, 6, 'Linear Sideboard', 'Functional sideboard with refined minimalist profile.', 980.00, 0, 1, '2026-09-06 20:53:26', '2026-09-06 20:54:57'),
	(6, 2, 'Serene Sleep Bed', 'Minimalist bed frame designed for comfort.', 1850.00, 1, 1, '2026-09-06 20:53:26', '2026-09-06 20:54:57'),
	(7, 5, 'VC1204 Varendah Chair', 'The VC1204 Varendah Chair is a traditional Sri Lankan verandah armchair built from solid teak, designed for the slower hours of the day spent on the porch, in the sitting room,', 1200.00, 0, 1, '2026-10-03 01:06:11', '2026-10-03 01:06:11');

-- Dumping structure for table niru_furniture.product_images
CREATE TABLE IF NOT EXISTS `product_images` (
  `image_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `image_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`image_id`),
  KEY `fk_images_product` (`product_id`),
  CONSTRAINT `fk_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table niru_furniture.product_images: ~13 rows (approximately)
DELETE FROM `product_images`;
INSERT INTO `product_images` (`image_id`, `product_id`, `image_path`, `is_primary`, `sort_order`) VALUES
	(1, 1, 'Images/mainproduct/whitechair (5).png', 1, 1),
	(2, 1, 'Images/mainproduct/whitechair (1).png', 0, 2),
	(3, 1, 'Images/mainproduct/whitechair (2).png', 0, 3),
	(4, 1, 'Images/mainproduct/whitechair (3).png', 0, 4),
	(5, 1, 'Images/mainproduct/whitechair (4).png', 0, 5),
	(6, 2, 'Images/products/elysian_oak_table.png', 1, 1),
	(7, 3, 'Images/products/obelisk_sofa.png', 1, 1),
	(8, 4, 'Images/products/aura_floor_lamp.png', 1, 1),
	(9, 5, 'Images/products/linear_sideboard.png', 1, 1),
	(10, 6, 'Images/products/serene_sleep.png', 1, 1),
	(11, 7, 'Images/products/prod_6ac007ab3e39c_0.jpg', 1, 1),
	(12, 7, 'Images/products/prod_6ac007ab3fb5b_1.jpg', 0, 2);

-- Dumping structure for table niru_furniture.reviews
CREATE TABLE IF NOT EXISTS `reviews` (
  `review_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `rating` tinyint unsigned NOT NULL,
  `review_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  KEY `fk_reviews_product` (`product_id`),
  KEY `fk_reviews_user` (`user_id`),
  CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reviews_chk_1` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table niru_furniture.reviews: ~3 rows (approximately)
DELETE FROM `reviews`;
INSERT INTO `reviews` (`review_id`, `product_id`, `user_id`, `rating`, `review_text`, `created_at`) VALUES
	(1, 1, 2, 5, 'Exceptional build quality and very comfortable for reading.', '2026-09-06 20:53:26'),
	(2, 4, 2, 4, 'Provides nice soft ambient light, matches my modern setup.', '2026-09-06 20:53:26'),
	(3, 6, 1, 2, 'bad', '2026-10-02 20:22:33'),
	(4, 5, 1, 5, 'good', '2026-10-02 20:23:03');

-- Dumping structure for table niru_furniture.status
CREATE TABLE IF NOT EXISTS `status` (
  `status_id` int unsigned NOT NULL AUTO_INCREMENT,
  `status_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`status_id`),
  UNIQUE KEY `uq_status_name` (`status_name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table niru_furniture.status: ~11 rows (approximately)
DELETE FROM `status`;
INSERT INTO `status` (`status_id`, `status_name`) VALUES
	(1, 'Active'),
	(5, 'Archived'),
	(3, 'Banned'),
	(9, 'Delivered'),
	(4, 'Draft'),
	(11, 'Failed Delivery'),
	(2, 'Inactive'),
	(8, 'Out for Delivery'),
	(7, 'Packed'),
	(6, 'Pending Dispatch'),
	(10, 'Returned');

-- Dumping structure for table niru_furniture.user
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` int unsigned NOT NULL DEFAULT '2',
  `status_id` int unsigned NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uq_user_email` (`email`),
  KEY `fk_user_role` (`role_id`),
  KEY `fk_user_status` (`status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table niru_furniture.user: ~3 rows (approximately)
DELETE FROM `user`;
INSERT INTO `user` (`user_id`, `first_name`, `last_name`, `email`, `phone`, `address_line1`, `address_line2`, `city`, `postal_code`, `password_hash`, `role_id`, `status_id`, `created_at`, `updated_at`) VALUES
	(1, 'Sandeesha', 'Rukshan', 'sandeesharukshan321@gmail.com', '0713218157', 'E2-612/B,NEW TOWN', 'EMBILIPITIYA', 'Embilipitiya', '70200', '$2y$10$aKtdkZk/VyaovrBG2dKeH.0hq47xlynUiZhsnT/vWbx8QXQbY4KP6', 1, 1, '2026-09-06 20:53:26', '2026-10-02 14:19:13'),
	(2, 'Kasun', 'Perera', 'kasun.perera@example.com', '0771234567', '45 Galle Road', 'Bambalapitiya', 'Colombo', '00400', '$2y$10$e8wYhT2dK4mBv9XqZbYrCe6N2KxLmNpQrTuVwXyZ1AbCdEfGhIjKl', 2, 1, '2026-09-06 20:53:26', '2026-09-06 20:53:26'),
	(3, 'Nadeesha', 'Fernando', 'nadeesha.f@example.com', '0719876543', '12 Kandy Road', 'Peradeniya', 'Kandy', '20400', '$2y$10$f9xZiU3eL5nCw0YrAcZsDf7O3LyMnPqRsUvWxYzA2BcDeFgHiJkLm', 2, 1, '2026-09-06 20:53:26', '2026-10-02 20:46:42');

-- Dumping structure for table niru_furniture.user_role
CREATE TABLE IF NOT EXISTS `user_role` (
  `role_id` int unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `uq_role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table niru_furniture.user_role: ~2 rows (approximately)
DELETE FROM `user_role`;
INSERT INTO `user_role` (`role_id`, `role_name`) VALUES
	(1, 'Admin'),
	(2, 'Customer');

-- Dumping structure for table niru_furniture.wishlists
CREATE TABLE IF NOT EXISTS `wishlists` (
  `wishlist_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`wishlist_id`),
  UNIQUE KEY `uq_wishlist_user_product` (`user_id`,`product_id`),
  KEY `fk_wishlist_product` (`product_id`),
  CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table niru_furniture.wishlists: ~2 rows (approximately)
DELETE FROM `wishlists`;
INSERT INTO `wishlists` (`wishlist_id`, `user_id`, `product_id`, `created_at`) VALUES
	(3, 3, 1, '2026-09-06 20:53:26'),
	(12, 1, 1, '2026-10-05 12:56:32');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
