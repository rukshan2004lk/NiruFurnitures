<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../connection.php';

if (!isset($_SESSION['a']) || ((int)($_SESSION['a']['role_id'] ?? 0) !== 1)) {
    exit("Access Denied.");
}

$name        = trim($_POST['name'] ?? '');
$category_id = (int)($_POST['category_id'] ?? 0);
$status_id   = (int)($_POST['status_id'] ?? 1);
$price       = (float)($_POST['price'] ?? 0);
$quantity    = (int)($_POST['quantity'] ?? 0);
$description = trim($_POST['description'] ?? '');
$primary_idx = (int)($_POST['primary_img_idx'] ?? 0);

if (empty($name)) exit("Please enter a product name.");
if ($category_id <= 0) exit("Please select a valid category.");
if ($price <= 0) exit("Please provide a valid price.");

if (!isset($_FILES['product_images']) || empty($_FILES['product_images']['name'][0])) {
    exit("Please upload at least one image.");
}

$files = $_FILES['product_images'];
$file_count = count($files['name']);
if ($file_count > 5) exit("Maximum 5 images allowed.");

// Insert Product
Database::iud("INSERT INTO `products` (`category_id`, `name`, `description`, `price`, `status_id`, `created_at`) 
               VALUES ('$category_id', '" . addslashes($name) . "', '" . addslashes($description) . "', '$price', '$status_id', NOW())");

$pid_rs = Database::search("SELECT LAST_INSERT_ID() AS `new_id`");
$product_id = (int)($pid_rs->fetch_assoc()['new_id'] ?? 0);

if ($product_id <= 0) exit("Failed to create product record.");

// Insert Inventory
Database::iud("INSERT INTO `inventory` (`product_id`, `quantity`, `last_updated`) 
               VALUES ('$product_id', '$quantity', NOW())");

// Upload Images
$target_dir = "../Images/products/";
if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

$allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
$saved_count = 0;

for ($i = 0; $i < $file_count; $i++) {
    if ($files['error'][$i] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed_exts)) {
            $new_name = "prod_" . uniqid() . "_{$i}." . $ext;
            if (move_uploaded_file($files['tmp_name'][$i], $target_dir . $new_name)) {
                $db_path = "Images/products/" . $new_name;
                $is_primary = ($i === $primary_idx) ? 1 : 0;
                $sort_order = $i + 1;

                Database::iud("INSERT INTO `product_images` (`product_id`, `image_path`, `is_primary`, `sort_order`) 
                               VALUES ('$product_id', '$db_path', '$is_primary', '$sort_order')");
                $saved_count++;
            }
        }
    }
}

// Fallback: If chosen index was skipped or invalid, set the first saved image as primary
$p_check = Database::search("SELECT `image_id` FROM `product_images` WHERE `product_id` = '$product_id' AND `is_primary` = 1");
if ($p_check->num_rows === 0) {
    Database::iud("UPDATE `product_images` SET `is_primary` = 1 WHERE `product_id` = '$product_id' ORDER BY `image_id` ASC LIMIT 1");
}

echo "success";
?>