<?php
/**
 * NiRu-Furnitures — Admin Entry Point
 */
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();
redirect(SITE_URL . '/admin/dashboard.php');
