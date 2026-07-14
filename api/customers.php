<?php
/**
 * NiRu-Furnitures — Customers API (api/customers.php)
 * Handles AJAX requests for: Email availability check
 */
require_once '../includes/config.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'check_email') {
    $email = sanitize($_GET['email'] ?? '');
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['exists' => false]);
        exit;
    }
    
    $exists = dbFetchOne("SELECT id FROM users WHERE email = ?", 's', $email);
    echo json_encode(['exists' => (bool)$exists]);
    exit;
}

echo json_encode(['error' => 'Unknown action.']);
