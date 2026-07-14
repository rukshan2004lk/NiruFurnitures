<?php
/**
 * NiRu-Furnitures — Global Configuration
 * -------------------------------------------------------
 * Central config: DB credentials, site constants, env flags.
 * Include this file FIRST in every PHP page.
 */

// ── Environment ──────────────────────────────────────────────
define('ENVIRONMENT', 'development'); // 'development' | 'production'

if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// ── Database ─────────────────────────────────────────────────
define('DB_HOST',   'localhost');
define('DB_USER',   'root');
define('DB_PASS',   '');
define('DB_NAME',   'niru_furnitures');
define('DB_CHARSET','utf8mb4');

// ── Site ─────────────────────────────────────────────────────
define('SITE_NAME',     'NiRu-Furnitures');
define('SITE_EMAIL',    'info@nirufurnitures.com');
define('CURRENCY',      'Rs.');
define('TIMEZONE',      'Asia/Colombo');

// ── Paths ─────────────────────────────────────────────────────
define('BASE_PATH',     dirname(__DIR__));       // project root
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('UPLOADS_PATH',  BASE_PATH . '/uploads');
define('SITE_URL',      'http://localhost/' . basename(BASE_PATH));
define('UPLOADS_URL',   SITE_URL  . '/uploads');

// ── Uploads ──────────────────────────────────────────────────
define('MAX_FILE_SIZE',  5 * 1024 * 1024); // 5 MB
define('ALLOWED_TYPES',  ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// ── Session ──────────────────────────────────────────────────
define('SESSION_NAME',     'niru_sess');
define('SESSION_LIFETIME', 7200); // 2 hours

// ── Timezone ─────────────────────────────────────────────────
date_default_timezone_set(TIMEZONE);

// ── Bootstrap database connection ────────────────────────────
require_once __DIR__ . '/database.php';
