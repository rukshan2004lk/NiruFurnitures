-- ============================================================
-- NiRu-Furnitures Database Schema
-- Engine: MySQL 8 | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Create & use database
CREATE DATABASE IF NOT EXISTS `niru_furnitures`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `niru_furnitures`;

-- ============================================================
-- TABLE: users
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id`             INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`           VARCHAR(120)     NOT NULL,
  `email`          VARCHAR(180)     NOT NULL UNIQUE,
  `password_hash`  VARCHAR(255)     NOT NULL,
  `role`           ENUM('customer','admin','manager') NOT NULL DEFAULT 'customer',
  `phone`          VARCHAR(20)      DEFAULT NULL,
  `avatar`         VARCHAR(255)     DEFAULT NULL,
  `status`         TINYINT(1)       NOT NULL DEFAULT 1 COMMENT '1=active 0=banned',
  `reset_token`    VARCHAR(100)     DEFAULT NULL,
  `reset_expires`  DATETIME         DEFAULT NULL,
  `created_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: categories
-- ============================================================
CREATE TABLE IF NOT EXISTS `categories` (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)  NOT NULL,
  `slug`        VARCHAR(120)  NOT NULL UNIQUE,
  `description` TEXT          DEFAULT NULL,
  `image`       VARCHAR(255)  DEFAULT NULL,
  `sort_order`  INT           NOT NULL DEFAULT 0,
  `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: products
-- ============================================================
CREATE TABLE IF NOT EXISTS `products` (
  `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `category_id`  INT UNSIGNED    NOT NULL,
  `name`         VARCHAR(200)    NOT NULL,
  `slug`         VARCHAR(220)    NOT NULL UNIQUE,
  `description`  LONGTEXT        DEFAULT NULL,
  `price`        DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  `sale_price`   DECIMAL(10,2)   DEFAULT NULL,
  `stock`        INT             NOT NULL DEFAULT 0,
  `sku`          VARCHAR(80)     DEFAULT NULL UNIQUE,
  `images`       JSON            DEFAULT NULL COMMENT 'Array of image filenames',
  `featured`     TINYINT(1)      NOT NULL DEFAULT 0,
  `status`       ENUM('active','inactive','draft') NOT NULL DEFAULT 'active',
  `weight`       DECIMAL(8,2)    DEFAULT NULL COMMENT 'kg',
  `dimensions`   VARCHAR(100)    DEFAULT NULL COMMENT 'L x W x H cm',
  `material`     VARCHAR(150)    DEFAULT NULL,
  `color`        VARCHAR(100)    DEFAULT NULL,
  `views`        INT UNSIGNED    NOT NULL DEFAULT 0,
  `created_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_product_category` (`category_id`),
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `sale_price`, `stock`, `sku`, `images`, `featured`, `status`, `weight`, `dimensions`, `material`, `color`, `views`, `created_at`, `updated_at`) VALUES
(1, 1, 'Luxe 3-Seater Sofa', 'luxe-3-seater-sofa', 'A premium 3-seater sofa upholstered in high-grade linen fabric. Features solid hardwood legs and high-density foam cushions for exceptional comfort.', 89900.00, 74900.00, 15, 'LVR-SF-001', '[\"luxe-3-seater-sofa-1782922437-0.jpg\", \"luxe-3-seater-sofa-1783451367-0.webp\", \"luxe-3-seater-sofa-1783451367-1.jpg\", \"luxe-3-seater-sofa-1783451367-2.jpg\", \"luxe-3-seater-sofa-1783451367-3.webp\", \"luxe-3-seater-sofa-1783451367-4.jpg\"]', 1, 'active', 55.00, '220 x 90 x 85 cm', 'Solid Wood & Linen', 'Charcoal Grey', 13, '2026-06-30 19:19:20', '2026-07-08 00:56:51'),
(2, 1, 'Nordic Coffee Table', 'nordic-coffee-table', 'Minimalist Scandinavian-inspired coffee table with a solid oak top and tapered legs. Perfect centerpiece for any modern living room.', 24500.00, NULL, 30, 'LVR-CT-001', '[\"placeholder.jpg\"]', 1, 'active', 18.00, '110 x 60 x 45 cm', 'Solid Oak', 'Natural Oak', 0, '2026-06-30 19:19:20', '2026-06-30 19:19:20'),
(3, 1, 'Modular TV Unit', 'modular-tv-unit', 'Sleek modular TV unit with cable management, open shelving and two soft-close drawers. Accommodates TVs up to 75 inches.', 38500.00, 32000.00, 20, 'LVR-TV-001', '[\"placeholder.jpg\"]', 0, 'active', 32.00, '180 x 40 x 55 cm', 'MDF & Veneer', 'Walnut Brown', 1, '2026-06-30 19:19:20', '2026-06-30 19:23:03'),
(4, 2, 'King Platform Bed', 'king-platform-bed', 'Modern platform bed with upholstered headboard and integrated under-bed storage drawers. Solid pine frame construction.', 129000.00, 109000.00, 10, 'BED-KG-001', '[\"placeholder.jpg\"]', 1, 'active', 72.00, '210 x 200 x 120 cm', 'Solid Pine & Fabric', 'Midnight Blue', 0, '2026-06-30 19:19:20', '2026-06-30 19:19:20'),
(5, 2, 'Sliding Wardrobe 3-Door', 'sliding-wardrobe-3-door', 'Spacious 3-door sliding wardrobe with mirrored centre panel, hanging rails, shelves and drawers.', 185000.00, NULL, 8, 'BED-WR-001', '[\"placeholder.jpg\"]', 1, 'active', 90.00, '210 x 60 x 220 cm', 'Engineered Wood', 'Pearl White', 0, '2026-06-30 19:19:20', '2026-06-30 19:19:20'),
(6, 2, 'Bedside Nightstand', 'bedside-nightstand', 'Compact nightstand with a drawer and open shelf. Pairs perfectly with our platform bed range.', 14500.00, 11900.00, 40, 'BED-NS-001', '[\"placeholder.jpg\"]', 0, 'active', 8.50, '45 x 38 x 55 cm', 'Solid Rubberwood', 'Natural', 2, '2026-06-30 19:19:20', '2026-07-08 00:52:35'),
(7, 3, 'Extendable Dining Table', 'extendable-dining-table', 'Seats 6–8 people. The butterfly leaf extension mechanism is smooth and takes only seconds. Solid beechwood construction.', 68000.00, 58000.00, 12, 'DIN-TBL-001', '[\"placeholder.jpg\"]', 1, 'active', 48.00, '160–220 x 90 x 76 cm', 'Solid Beech', 'Honey Oak', 1, '2026-06-30 19:19:20', '2026-07-01 21:38:39'),
(8, 3, 'Padded Dining Chair (Set of 4)', 'padded-dining-chair-set-4', 'Set of 4 upholstered dining chairs with solid wood legs and high-density foam seat pads. Ergonomically designed for long meals.', 36000.00, 29900.00, 18, 'DIN-CHR-004', '[\"placeholder.jpg\"]', 0, 'active', 6.00, '45 x 52 x 90 cm', 'Beech & Fabric', 'Cream Beige', 0, '2026-06-30 19:19:20', '2026-06-30 19:19:20'),
(9, 4, 'Executive L-Shaped Desk', 'executive-l-shaped-desk', 'L-shaped executive desk with integrated cable management tray, keyboard shelf and two filing drawers.', 52000.00, NULL, 15, 'OFC-DSK-001', '[\"placeholder.jpg\"]', 1, 'active', 45.00, '160 x 140 x 76 cm', 'MDF & Steel', 'Espresso', 0, '2026-06-30 19:19:20', '2026-06-30 19:19:20'),
(10, 4, 'Ergonomic Mesh Chair', 'ergonomic-mesh-chair', 'Full-mesh back ergonomic office chair with lumbar support, adjustable armrests and 360° swivel base.', 29500.00, 24500.00, 25, 'OFC-CHR-001', '[\"placeholder.jpg\"]', 1, 'active', 14.00, '68 x 68 x 110–120 cm', 'Mesh & Nylon', 'Jet Black', 0, '2026-06-30 19:19:20', '2026-06-30 19:19:20'),
(11, 5, 'Rattan Patio Set (4-Piece)', 'rattan-patio-set-4-piece', '4-piece all-weather wicker patio set: 1 loveseat, 2 armchairs and 1 glass-top coffee table. UV & rain resistant.', 79500.00, 65000.00, 10, 'OUT-PAT-001', '[\"placeholder.jpg\"]', 1, 'active', 35.00, 'Varies', 'PE Rattan & Aluminium', 'Mocha Brown', 1, '2026-06-30 19:19:20', '2026-07-08 00:54:07'),
(12, 6, 'Tall Bookshelf 5-Tier', 'tall-bookshelf-5-tier', 'Open 5-tier bookshelf with anti-tip wall anchor kit. Ideal for books, plants, decor and display items.', 18500.00, 15500.00, 35, 'STG-BKS-001', '[\"placeholder.jpg\"]', 0, 'active', 22.00, '80 x 30 x 180 cm', 'MDF & Metal', 'Black & Oak', 0, '2026-06-30 19:19:20', '2026-06-30 19:19:20'),
(13, 7, 'Nordic Lounge Chair', 'nordic-lounge-chair', 'Featured product', 849.00, NULL, 10, NULL, '[\"https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=650&q=90\"]', 1, 'active', NULL, NULL, NULL, NULL, 1, '2026-07-08 00:56:38', '2026-07-08 01:01:39'),
(14, 1, 'Sculptural Coffee Table', 'sculptural-coffee-table', 'Featured product', 1299.00, NULL, 10, NULL, '[\"https://images.unsplash.com/photo-1533090481720-856c6e3c1fdc?auto=format&fit=crop&w=650&q=90\"]', 1, 'active', NULL, NULL, NULL, NULL, 2, '2026-07-08 00:56:38', '2026-07-08 01:01:29'),
(15, 6, 'Linear Oak Bookshelf', 'linear-oak-bookshelf', 'Featured product', 620.00, NULL, 10, NULL, '[\"https://images.unsplash.com/photo-1594620302200-9a762244a156?auto=format&fit=crop&w=650&q=90\"]', 1, 'active', NULL, NULL, NULL, NULL, 2, '2026-07-08 00:56:38', '2026-07-08 01:01:31'),
(16, 8, 'Brass Desk Luminary', 'brass-desk-luminary', 'Featured product', 215.00, NULL, 10, NULL, '[\"https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=650&q=90\"]', 1, 'active', NULL, NULL, NULL, NULL, 4, '2026-07-08 00:56:38', '2026-07-08 00:58:51');

-- ============================================================
-- TABLE: orders
-- ============================================================
CREATE TABLE IF NOT EXISTS `orders` (
  `id`               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `user_id`          INT UNSIGNED   NOT NULL,
  `order_number`     VARCHAR(30)    NOT NULL UNIQUE,
  `status`           ENUM('pending','processing','shipped','delivered','cancelled','refunded')
                                    NOT NULL DEFAULT 'pending',
  `subtotal`         DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  `shipping_cost`    DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  `discount`         DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  `total`            DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  `payment_method`   ENUM('cash_on_delivery','bank_transfer','card') NOT NULL DEFAULT 'cash_on_delivery',
  `payment_status`   ENUM('unpaid','paid','refunded') NOT NULL DEFAULT 'unpaid',
  `shipping_name`    VARCHAR(120)   NOT NULL,
  `shipping_address` TEXT           NOT NULL,
  `shipping_city`    VARCHAR(80)    NOT NULL,
  `shipping_zip`     VARCHAR(20)    DEFAULT NULL,
  `shipping_country` VARCHAR(80)    NOT NULL DEFAULT 'Sri Lanka',
  `notes`            TEXT           DEFAULT NULL,
  `created_at`       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_order_user` (`user_id`),
  CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: order_items
-- ============================================================
CREATE TABLE IF NOT EXISTS `order_items` (
  `id`          INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `order_id`    INT UNSIGNED   NOT NULL,
  `product_id`  INT UNSIGNED   NOT NULL,
  `product_name` VARCHAR(200)  NOT NULL,
  `qty`         INT            NOT NULL DEFAULT 1,
  `unit_price`  DECIMAL(10,2)  NOT NULL,
  `total_price` DECIMAL(10,2)  NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_item_order` (`order_id`),
  KEY `fk_item_product` (`product_id`),
  CONSTRAINT `fk_item_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_item_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: cart
-- ============================================================
CREATE TABLE IF NOT EXISTS `cart` (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED  DEFAULT NULL,
  `session_id`  VARCHAR(100)  DEFAULT NULL,
  `product_id`  INT UNSIGNED  NOT NULL,
  `qty`         INT           NOT NULL DEFAULT 1,
  `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_cart_product` (`product_id`),
  KEY `fk_cart_user` (`user_id`),
  CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: wishlist
-- ============================================================
CREATE TABLE IF NOT EXISTS `wishlist` (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED  NOT NULL,
  `product_id`  INT UNSIGNED  NOT NULL,
  `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wishlist` (`user_id`, `product_id`),
  KEY `fk_wish_product` (`product_id`),
  CONSTRAINT `fk_wish_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wish_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: reviews
-- ============================================================
CREATE TABLE IF NOT EXISTS `reviews` (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED  NOT NULL,
  `product_id`  INT UNSIGNED  NOT NULL,
  `rating`      TINYINT       NOT NULL DEFAULT 5,
  `comment`     TEXT          DEFAULT NULL,
  `approved`    TINYINT(1)    NOT NULL DEFAULT 0,
  `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_review_user` (`user_id`),
  KEY `fk_review_product` (`product_id`),
  CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: messages (Contact form)
-- ============================================================
CREATE TABLE IF NOT EXISTS `messages` (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(120)  NOT NULL,
  `email`       VARCHAR(180)  NOT NULL,
  `subject`     VARCHAR(200)  NOT NULL,
  `message`     TEXT          NOT NULL,
  `is_read`     TINYINT(1)    NOT NULL DEFAULT 0,
  `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: settings
-- ============================================================
CREATE TABLE IF NOT EXISTS `settings` (
  `setting_key`   VARCHAR(100) NOT NULL,
  `setting_value` TEXT         DEFAULT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: banners
-- ============================================================
CREATE TABLE IF NOT EXISTS `banners` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(200)  NOT NULL,
  `subtitle`   VARCHAR(300)  DEFAULT NULL,
  `image`      VARCHAR(255)  NOT NULL,
  `link`       VARCHAR(255)  DEFAULT NULL,
  `sort_order` INT           NOT NULL DEFAULT 0,
  `active`     TINYINT(1)    NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: coupons
-- ============================================================
CREATE TABLE IF NOT EXISTS `coupons` (
  `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `code`           VARCHAR(50)   NOT NULL UNIQUE,
  `type`           ENUM('percent','fixed') NOT NULL DEFAULT 'percent',
  `value`          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `min_order`      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `usage_limit`    INT           DEFAULT NULL,
  `used_count`     INT           NOT NULL DEFAULT 0,
  `expires_at`     DATETIME      DEFAULT NULL,
  `active`         TINYINT(1)    NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
