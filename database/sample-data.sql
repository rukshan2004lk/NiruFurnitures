-- ============================================================
-- NiRu-Furnitures — Sample / Seed Data
-- Run AFTER database.sql
-- ============================================================

USE `niru_furnitures`;

-- ============================================================
-- Admin User  (password: Admin@1234)
-- Hash generated with: password_hash('Admin@1234', PASSWORD_BCRYPT)
-- ============================================================
INSERT INTO `users` (`name`, `email`, `password_hash`, `role`, `phone`, `status`) VALUES
('Admin NiRu', 'admin@nirufurnitures.com', '$2y$12$7WoLm8S9QzXkPNhJvR3.2.TGcHFaZ9NxkC0T1jOxIFn0lXpBCZr7G', 'admin', '+94 77 000 0000', 1);

-- ============================================================
-- Sample Customer
-- password: Customer@123
-- ============================================================
INSERT INTO `users` (`name`, `email`, `password_hash`, `role`, `phone`, `status`) VALUES
('Nisala Perera', 'nisala@example.com', '$2y$12$Pq8WcDHn1zXlJk3mVs4.O.KgJhXr9NyZZ7LZKvHmQ5W2EcVgRpT6C', 'customer', '+94 77 123 4567', 1);

-- ============================================================
-- Categories
-- ============================================================
INSERT INTO `categories` (`name`, `slug`, `description`, `sort_order`) VALUES
('Living Room',  'living-room',  'Sofas, coffee tables, TV units and more for your living space.',   1),
('Bedroom',      'bedroom',      'Beds, wardrobes, dressers and nightstands for restful retreat.',    2),
('Dining Room',  'dining-room',  'Dining tables, chairs and sideboards for shared meals.',            3),
('Office',       'office',       'Desks, chairs, shelves and ergonomic furniture for productivity.',  4),
('Outdoor',      'outdoor',      'Weather-resistant outdoor furniture for patios and gardens.',        5),
('Storage',      'storage',      'Cabinets, shelves and storage solutions for every room.',           6);

-- ============================================================
-- Sample Products
-- ============================================================
INSERT INTO `products` (`category_id`, `name`, `slug`, `description`, `price`, `sale_price`, `stock`, `sku`, `images`, `featured`, `material`, `color`, `dimensions`, `weight`) VALUES

-- Living Room
(1, 'Luxe 3-Seater Sofa', 'luxe-3-seater-sofa',
 'A premium 3-seater sofa upholstered in high-grade linen fabric. Features solid hardwood legs and high-density foam cushions for exceptional comfort.',
 89900.00, 74900.00, 15, 'LVR-SF-001', '["placeholder.jpg"]', 1, 'Solid Wood & Linen', 'Charcoal Grey', '220 x 90 x 85 cm', 55.00),

(1, 'Nordic Coffee Table', 'nordic-coffee-table',
 'Minimalist Scandinavian-inspired coffee table with a solid oak top and tapered legs. Perfect centerpiece for any modern living room.',
 24500.00, NULL, 30, 'LVR-CT-001', '["placeholder.jpg"]', 1, 'Solid Oak', 'Natural Oak', '110 x 60 x 45 cm', 18.00),

(1, 'Modular TV Unit', 'modular-tv-unit',
 'Sleek modular TV unit with cable management, open shelving and two soft-close drawers. Accommodates TVs up to 75 inches.',
 38500.00, 32000.00, 20, 'LVR-TV-001', '["placeholder.jpg"]', 0, 'MDF & Veneer', 'Walnut Brown', '180 x 40 x 55 cm', 32.00),

-- Bedroom
(2, 'King Platform Bed', 'king-platform-bed',
 'Modern platform bed with upholstered headboard and integrated under-bed storage drawers. Solid pine frame construction.',
 129000.00, 109000.00, 10, 'BED-KG-001', '["placeholder.jpg"]', 1, 'Solid Pine & Fabric', 'Midnight Blue', '210 x 200 x 120 cm', 72.00),

(2, 'Sliding Wardrobe 3-Door', 'sliding-wardrobe-3-door',
 'Spacious 3-door sliding wardrobe with mirrored centre panel, hanging rails, shelves and drawers.',
 185000.00, NULL, 8, 'BED-WR-001', '["placeholder.jpg"]', 1, 'Engineered Wood', 'Pearl White', '210 x 60 x 220 cm', 90.00),

(2, 'Bedside Nightstand', 'bedside-nightstand',
 'Compact nightstand with a drawer and open shelf. Pairs perfectly with our platform bed range.',
 14500.00, 11900.00, 40, 'BED-NS-001', '["placeholder.jpg"]', 0, 'Solid Rubberwood', 'Natural', '45 x 38 x 55 cm', 8.50),

-- Dining Room
(3, 'Extendable Dining Table', 'extendable-dining-table',
 'Seats 6–8 people. The butterfly leaf extension mechanism is smooth and takes only seconds. Solid beechwood construction.',
 68000.00, 58000.00, 12, 'DIN-TBL-001', '["placeholder.jpg"]', 1, 'Solid Beech', 'Honey Oak', '160–220 x 90 x 76 cm', 48.00),

(3, 'Padded Dining Chair (Set of 4)', 'padded-dining-chair-set-4',
 'Set of 4 upholstered dining chairs with solid wood legs and high-density foam seat pads. Ergonomically designed for long meals.',
 36000.00, 29900.00, 18, 'DIN-CHR-004', '["placeholder.jpg"]', 0, 'Beech & Fabric', 'Cream Beige', '45 x 52 x 90 cm', 6.00),

-- Office
(4, 'Executive L-Shaped Desk', 'executive-l-shaped-desk',
 'L-shaped executive desk with integrated cable management tray, keyboard shelf and two filing drawers.',
 52000.00, NULL, 15, 'OFC-DSK-001', '["placeholder.jpg"]', 1, 'MDF & Steel', 'Espresso', '160 x 140 x 76 cm', 45.00),

(4, 'Ergonomic Mesh Chair', 'ergonomic-mesh-chair',
 'Full-mesh back ergonomic office chair with lumbar support, adjustable armrests and 360° swivel base.',
 29500.00, 24500.00, 25, 'OFC-CHR-001', '["placeholder.jpg"]', 1, 'Mesh & Nylon', 'Jet Black', '68 x 68 x 110–120 cm', 14.00),

-- Outdoor
(5, 'Rattan Patio Set (4-Piece)', 'rattan-patio-set-4-piece',
 '4-piece all-weather wicker patio set: 1 loveseat, 2 armchairs and 1 glass-top coffee table. UV & rain resistant.',
 79500.00, 65000.00, 10, 'OUT-PAT-001', '["placeholder.jpg"]', 1, 'PE Rattan & Aluminium', 'Mocha Brown', 'Varies', 35.00),

-- Storage
(6, 'Tall Bookshelf 5-Tier', 'tall-bookshelf-5-tier',
 'Open 5-tier bookshelf with anti-tip wall anchor kit. Ideal for books, plants, decor and display items.',
 18500.00, 15500.00, 35, 'STG-BKS-001', '["placeholder.jpg"]', 0, 'MDF & Metal', 'Black & Oak', '80 x 30 x 180 cm', 22.00);

-- ============================================================
-- Site Settings
-- ============================================================
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name',           'NiRu-Furnitures'),
('site_email',          'info@nirufurnitures.com'),
('site_phone',          '+94 77 000 0000'),
('site_address',        '42 Furniture Street, Colombo 03, Sri Lanka'),
('site_currency',       'LKR'),
('currency_symbol',     'Rs.'),
('shipping_cost',       '500'),
('free_shipping_above', '15000'),
('meta_description',    'NiRu-Furnitures — Premium quality furniture for every room. Shop beds, sofas, dining sets and more.'),
('facebook_url',        'https://facebook.com/nirufurnitures'),
('instagram_url',       'https://instagram.com/nirufurnitures'),
('twitter_url',         ''),
('google_analytics',    '');

-- ============================================================
-- Sample Banner Rows (images are placeholders)
-- ============================================================
INSERT INTO `banners` (`title`, `subtitle`, `image`, `link`, `sort_order`, `active`) VALUES
('Elevate Your Living Space',  'Discover handcrafted furniture that blends comfort with elegance.', 'banner-1.jpg', 'shop.php', 1, 1),
('Bedroom Collection 2025',    'Sleep better with our premium beds and storage solutions.',          'banner-2.jpg', 'shop.php?category=bedroom', 2, 1),
('Up to 20% Off — Office Sets','Productivity starts with the right workspace.',                      'banner-3.jpg', 'shop.php?category=office', 3, 1);
