<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['u'])) {
    exit("Please login first.");
}

$user = $_SESSION['u'];
$user_id = (int)$user['user_id'];
$user_email = $user['email'];

$product_id = (int)($_POST['product_id'] ?? 0);
$qty        = max(1, (int)($_POST['qty'] ?? 1));
$color      = trim($_POST['color'] ?? '');

$raw_card_num = $_POST['cN'] ?? $_POST['card_number'] ?? '';
$card_number  = str_replace(' ', '', trim($raw_card_num));
$exp_num      = trim($_POST['eD'] ?? $_POST['exp_num'] ?? '');
$cvv          = (int)($_POST['cV'] ?? $_POST['cvv'] ?? 0);

if (empty($card_number) || empty($exp_num) || empty($cvv)) {
    exit("Please provide complete payment card details.");
}

$fname = trim($_POST['fname'] ?? $user['first_name']);
$lname = trim($_POST['lname'] ?? $user['last_name']);
$customer_name = trim($fname . ' ' . $lname);

$customer_phone   = trim($_POST['mobile'] ?? $_POST['customer_phone'] ?? ($user['phone'] ?? ''));
$line1            = trim($_POST['line1'] ?? ($user['address_line1'] ?? ''));
$line2            = trim($_POST['line2'] ?? ($user['address_line2'] ?? ''));
$shipping_address = trim($line1 . (!empty($line2) ? ', ' . $line2 : ''));
$shipping_city    = trim($_POST['city'] ?? $_POST['shipping_city'] ?? ($user['city'] ?? ''));
$postal_code      = trim($_POST['pcode'] ?? $_POST['postal_code'] ?? ($user['postal_code'] ?? ''));

$order_items = [];
$total_amount = 0.00;

if ($product_id > 0) {
    $product_rs = Database::search("SELECT * FROM `products` WHERE `product_id` = '$product_id' AND `status_id` = 1");
    if ($product_rs->num_rows == 0) {
        exit("Product is unavailable or does not exist.");
    }
    $product = $product_rs->fetch_assoc();
    $unit_price = (float)$product['price'];
    $line_total = $unit_price * $qty;
    $total_amount = $line_total;

    $p_display_name = $product['name'] . (!empty($color) ? " (" . $color . ")" : "");

    $order_items[] = [
        'product_id'   => $product['product_id'],
        'product_name' => $p_display_name,
        'quantity'     => $qty,
        'unit_price'   => $unit_price,
        'line_total'   => $line_total,
        'color'        => $color
    ];
} else {
    $cart_rs = Database::search("SELECT ci.*, p.name AS product_name, p.price 
                                 FROM `carts` c 
                                 INNER JOIN `cart_items` ci ON c.cart_id = ci.cart_id 
                                 INNER JOIN `products` p ON ci.product_id = p.product_id 
                                 WHERE c.user_id = '$user_id'");
    
    if ($cart_rs->num_rows == 0) {
        exit("Your cart is empty.");
    }

    while ($item = $cart_rs->fetch_assoc()) {
        $price = (float)$item['price'];
        $item_qty = (int)$item['quantity'];
        $item_color = trim($item['color'] ?? '');
        $line = $price * $item_qty;
        $total_amount += $line;

        $p_display_name = $item['product_name'] . (!empty($item_color) ? " (" . $item_color . ")" : "");

        $order_items[] = [
            'product_id'   => $item['product_id'],
            'product_name' => $p_display_name,
            'quantity'     => $item_qty,
            'unit_price'   => $price,
            'line_total'   => $line,
            'color'        => $item_color
        ];
    }
}

$card_rs = Database::search("SELECT * FROM `cards` 
                             WHERE REPLACE(`number`, ' ', '') = '$card_number' 
                               AND `exp_num` = '$exp_num' 
                               AND `cvv` = '$cvv'");

if ($card_rs->num_rows == 0) {
    exit("Invalid card details. Please check your card information.");
}

$card = $card_rs->fetch_assoc();
$card_balance = (float)$card['amount'];

if ($card_balance < $total_amount) {
    exit("Insufficient card balance. Available: Rs. " . number_format($card_balance, 2) . ", Required: Rs. " . number_format($total_amount, 2));
}

$card_id = (int)$card['id'];
$new_balance = $card_balance - $total_amount;
Database::iud("UPDATE `cards` SET `amount` = '$new_balance' WHERE `id` = '$card_id'");

$order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
$order_status_id = 6;

Database::iud("INSERT INTO `orders` 
  (`order_number`, `user_id`, `total_amount`, `payment_status`, `order_status_id`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `shipping_city`, `shipping_postal_code`, `placed_at`) 
  VALUES 
  ('$order_number', '$user_id', '$total_amount', 'paid', '$order_status_id', '" . addslashes($customer_name) . "', '$user_email', '$customer_phone', '" . addslashes($shipping_address) . "', '" . addslashes($shipping_city) . "', '$postal_code', NOW())");

$order_fetch = Database::search("SELECT `order_id` FROM `orders` WHERE `order_number` = '$order_number'");
$order_data = $order_fetch->fetch_assoc();
$order_id = (int)$order_data['order_id'];

try {
    Database::iud("ALTER TABLE `order_items` ADD COLUMN `color` VARCHAR(50) DEFAULT NULL");
} catch (Throwable $e) {}

foreach ($order_items as $item) {
    $p_id       = (int)$item['product_id'];
    $p_name     = addslashes($item['product_name']);
    $i_qty      = (int)$item['quantity'];
    $u_price    = (float)$item['unit_price'];
    $l_total    = (float)$item['line_total'];
    $item_color = addslashes($item['color'] ?? '');

    try {
        Database::iud("INSERT INTO `order_items` 
          (`order_id`, `product_id`, `product_name`, `quantity`, `unit_price`, `line_total`, `color`) 
          VALUES 
          ('$order_id', '$p_id', '$p_name', '$i_qty', '$u_price', '$l_total', '$item_color')");
    } catch (Throwable $e) {
        Database::iud("INSERT INTO `order_items` 
          (`order_id`, `product_id`, `product_name`, `quantity`, `unit_price`, `line_total`) 
          VALUES 
          ('$order_id', '$p_id', '$p_name', '$i_qty', '$u_price', '$l_total')");
    }

    Database::iud("UPDATE `inventory` 
                   SET `quantity` = GREATEST(0, `quantity` - $i_qty) 
                   WHERE `product_id` = '$p_id'");
}

$txn_id = 'TXN_' . strtoupper(bin2hex(random_bytes(6)));

Database::iud("INSERT INTO `payments` 
  (`order_id`, `payment_method`, `amount`, `status`, `transaction_id`, `paid_at`) 
  VALUES 
  ('$order_id', 'card', '$total_amount', 'paid', '$txn_id', NOW())");

if ($product_id <= 0) {
    Database::iud("DELETE ci FROM `cart_items` ci 
                   INNER JOIN `carts` c ON ci.cart_id = c.cart_id 
                   WHERE c.user_id = '$user_id'");
}

echo "success";
?>