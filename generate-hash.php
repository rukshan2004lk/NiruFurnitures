<?php
// Run this once to get real password hashes, then delete this file.
$adminHash = password_hash('Admin@1234', PASSWORD_BCRYPT, ['cost' => 12]);
$customerHash = password_hash('Customer@123', PASSWORD_BCRYPT, ['cost' => 12]);

echo "<pre>";
echo "Admin hash:\n$adminHash\n\n";
echo "Customer hash:\n$customerHash\n";
echo "</pre>";

// Auto-update the database
require_once 'includes/config.php';

$conn->query("UPDATE users SET password_hash = '" . $conn->real_escape_string($adminHash) . "' WHERE email = 'admin@nirufurnitures.com'");
$conn->query("UPDATE users SET password_hash = '" . $conn->real_escape_string($customerHash) . "' WHERE email = 'nisala@example.com'");

echo "<p style='color:green;font-family:monospace'>✓ Passwords updated in database. <strong>Delete this file now.</strong></p>";
