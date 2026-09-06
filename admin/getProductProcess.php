<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../connection.php';

if (!isset($_SESSION['a']) || ((int)($_SESSION['a']['role_id'] ?? 0) !== 1)) {
    http_response_code(403);
    exit(json_encode(["error" => "Unauthorized"]));
}

$product_id = (int)($_GET['id'] ?? 0);

$rs = Database::search("SELECT p.*, COALESCE(i.quantity, 0) AS `quantity` 
                        FROM `products` p 
                        LEFT JOIN `inventory` i ON p.product_id = i.product_id 
                        WHERE p.product_id = '$product_id'");

if ($rs->num_rows > 0) {
    $product = $rs->fetch_assoc();

    // Fetch existing gallery images
    $images_rs = Database::search("SELECT * FROM `product_images` 
                                  WHERE `product_id` = '$product_id' 
                                  ORDER BY `is_primary` DESC, `sort_order` ASC");
    $images = [];
    while ($img = $images_rs->fetch_assoc()) {
        $images[] = $img;
    }
    $product['images'] = $images;

    header('Content-Type: application/json');
    echo json_encode($product);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Product not found"]);
}
?>