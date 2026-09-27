<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../connection.php';

if (!isset($_SESSION['a']) || ((int)($_SESSION['a']['role_id'] ?? 0) !== 1)) {
    exit("Access Denied.");
}

$product_id  = (int)($_POST['product_id'] ?? 0);
$name        = trim($_POST['name'] ?? '');
$category_id = (int)($_POST['category_id'] ?? 0);
$status_id   = (int)($_POST['status_id'] ?? 1);
$price       = (float)($_POST['price'] ?? 0);
$quantity    = (int)($_POST['quantity'] ?? 0);
$description = trim($_POST['description'] ?? '');

$deleted_images = json_decode($_POST['deleted_images'] ?? '[]', true);
$selected_primary = $_POST['primary_image_choice'] ?? '';

if ($product_id <= 0) exit("Invalid Product.");
if (empty($name)) exit("Product name cannot be empty.");
if ($category_id <= 0) exit("Please select a valid category.");
if ($price <= 0) exit("Price must be greater than zero.");

Database::iud("UPDATE `products` SET 
               `name` = '" . addslashes($name) . "', 
               `category_id` = '$category_id', 
               `status_id` = '$status_id', 
               `price` = '$price', 
               `description` = '" . addslashes($description) . "' 
               WHERE `product_id` = '$product_id'");

$inv_check = Database::search("SELECT `inventory_id` FROM `inventory` WHERE `product_id` = '$product_id'");
if ($inv_check->num_rows > 0) {
    Database::iud("UPDATE `inventory` SET `quantity` = '$quantity', `last_updated` = NOW() WHERE `product_id` = '$product_id'");
} else {
    Database::iud("INSERT INTO `inventory` (`product_id`, `quantity`, `last_updated`) VALUES ('$product_id', '$quantity', NOW())");
}

if (!empty($deleted_images) && is_array($deleted_images)) {
    foreach ($deleted_images as $del_id) {
        $del_id = (int)$del_id;
        $img_rs = Database::search("SELECT `image_path` FROM `product_images` WHERE `image_id` = '$del_id' AND `product_id` = '$product_id'");
        if ($img_rs->num_rows > 0) {
            $row = $img_rs->fetch_assoc();
            $disk_file = "../" . $row['image_path'];
            if (file_exists($disk_file)) @unlink($disk_file);
            Database::iud("DELETE FROM `product_images` WHERE `image_id` = '$del_id'");
        }
    }
}

$curr_rs = Database::search("SELECT COUNT(*) AS `cnt` FROM `product_images` WHERE `product_id` = '$product_id'");
$curr_count = (int)($curr_rs->fetch_assoc()['cnt'] ?? 0);

$target_dir = "../Images/products/";
if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
$allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

$new_saved_ids = [];

if (isset($_FILES['new_product_images']) && !empty($_FILES['new_product_images']['name'][0])) {
    $new_files = $_FILES['new_product_images'];
    $upload_count = count($new_files['name']);

    if (($curr_count + $upload_count) > 5) {
        exit("Maximum 5 total images allowed for this product.");
    }

    for ($i = 0; $i < $upload_count; $i++) {
        if ($new_files['error'][$i] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($new_files['name'][$i], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed_exts)) {
                $new_name = "prod_" . uniqid() . "_{$i}." . $ext;
                if (move_uploaded_file($new_files['tmp_name'][$i], $target_dir . $new_name)) {
                    $db_path = "Images/products/" . $new_name;
                    Database::iud("INSERT INTO `product_images` (`product_id`, `image_path`, `is_primary`, `sort_order`) 
                                   VALUES ('$product_id', '$db_path', 0, 10)");
                    $new_id_rs = Database::search("SELECT LAST_INSERT_ID() AS `id`");
                    $new_saved_ids[$i] = (int)$new_id_rs->fetch_assoc()['id'];
                }
            }
        }
    }
}

Database::iud("UPDATE `product_images` SET `is_primary` = 0 WHERE `product_id` = '$product_id'");

$primary_applied = false;

if (strpos($selected_primary, 'existing_') === 0) {
    $img_id = (int)str_replace('existing_', '', $selected_primary);
    Database::iud("UPDATE `product_images` SET `is_primary` = 1 WHERE `image_id` = '$img_id' AND `product_id` = '$product_id'");
    $primary_applied = true;
} else if (strpos($selected_primary, 'new_') === 0) {
    $new_idx = (int)str_replace('new_', '', $selected_primary);
    if (isset($new_saved_ids[$new_idx])) {
        $assigned_id = $new_saved_ids[$new_idx];
        Database::iud("UPDATE `product_images` SET `is_primary` = 1 WHERE `image_id` = '$assigned_id' AND `product_id` = '$product_id'");
        $primary_applied = true;
    }
}

// Fallback: Ensure at least one image is marked primary
$chk_primary = Database::search("SELECT `image_id` FROM `product_images` WHERE `product_id` = '$product_id' AND `is_primary` = 1");
if ($chk_primary->num_rows === 0) {
    Database::iud("UPDATE `product_images` SET `is_primary` = 1 WHERE `product_id` = '$product_id' ORDER BY `image_id` ASC LIMIT 1");
}

echo "success";
?>