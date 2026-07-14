<?php
/**
 * NiRu-Furnitures — Logout (logout.php)
 */
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

logoutUser();

setFlash('You have been logged out successfully.', 'info');
redirect('login.php');
