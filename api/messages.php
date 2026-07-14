<?php
/**
 * NiRu-Furnitures — Messages API (api/messages.php)
 * Handles contact form submission
 */
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_message') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlash('Security token invalid. Please try again.', 'danger');
        redirect('../contact.php');
    }

    $name    = sanitize($_POST['name'] ?? '');
    $email   = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        setFlash('All fields are required.', 'danger');
    } else {
        dbInsert("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)", 'ssss', $name, $email, $subject, $message);
        setFlash('Your message has been sent successfully. We will get back to you soon!', 'success');
    }
    
    redirect('../contact.php');
}
