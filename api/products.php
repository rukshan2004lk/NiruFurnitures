<?php
/**
 * NiRu-Furnitures — Products API (api/products.php)
 * Handles AJAX requests for: Search, Filter, Add to Cart, Update Cart, Wishlist, Reviews
 */
ob_start(); // Buffer any stray PHP warnings/notices so they don't corrupt JSON
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

ob_clean(); // Discard any warnings output during requires
header('Content-Type: application/json');
header('Cache-Control: no-store');

// Get JSON input for POST, or use $_GET for GET requests
$input = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $input['action'] ?? $_GET['action'] ?? $_POST['action'] ?? '';

// ── GET: Single Product Info (for Cart Panel) ─────────────────
if ($action === 'get_product' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $pid = (int)($_GET['id'] ?? 0);
    if (!$pid) { echo json_encode(['success' => false]); exit; }

    $p = dbFetchOne(
        "SELECT p.id, p.name, p.price, p.sale_price, p.images, p.stock, c.name AS category_name
         FROM products p
         JOIN categories c ON c.id = p.category_id
         WHERE p.id = ? AND p.status = 'active'",
        'i', $pid
    );
    if (!$p) { echo json_encode(['success' => false, 'message' => 'Product not found.']); exit; }

    $imgs = json_decode($p['images'] ?? '[]', true);
    $p['image_url'] = productImageUrl($imgs[0] ?? null);
    unset($p['images']);

    echo json_encode(['success' => true, 'product' => $p]);
    exit;
}

// ── GET: Search Dropdown ──────────────────────────────────────
if ($action === 'search' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $q = sanitize($_GET['q'] ?? '');
    $limit = (int)($_GET['limit'] ?? 6);
    
    if (empty($q)) {
        echo json_encode(['products' => []]);
        exit;
    }
    
    $products = dbFetchAll("
        SELECT p.id, p.name, p.price, p.sale_price, p.images, c.name AS category 
        FROM products p 
        LEFT JOIN categories c ON c.id = p.category_id 
        WHERE p.status = 'active' AND p.name LIKE ? 
        ORDER BY p.name ASC 
        LIMIT ?
    ", 'si', "%$q%", $limit);
    
    // Format output
    $formatted = array_map(function($p) {
        $imgs = json_decode($p['images'] ?? '[]', true);
        $p['image_url'] = productImageUrl($imgs[0] ?? null);
        $price = $p['sale_price'] ?: $p['price'];
        $p['formatted_price'] = formatPrice((float)$price);
        unset($p['images'], $p['price'], $p['sale_price']);
        return $p;
    }, $products);
    
    echo json_encode(['products' => $formatted]);
    exit;
}

// ── GET: Filter Products (Shop Grid) ──────────────────────────
if ($action === 'filter' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    // We already built this logic in shop.php, but if we wanted 
    // a pure AJAX infinite-scroll/filter, it would mirror shop.php.
    // For this MVP, filter.js calls shop.php?action=filter but we actually 
    // just return JSON here if needed, but our filter.js expects JSON from api/products.php.
    
    $q = sanitize($_GET['q'] ?? '');
    $sort = sanitize($_GET['sort'] ?? 'newest');
    $cats = $_GET['category'] ?? [];
    $priceMin = (float)($_GET['price_min'] ?? 0);
    $priceMax = (float)($_GET['price_max'] ?? 500000);
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 12;
    
    $where = ["p.status = 'active'"];
    $params = [];
    $types = '';
    
    if ($q) {
        $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
        $params[] = "%$q%"; $params[] = "%$q%"; $types .= 'ss';
    }
    
    if (!empty($cats) && is_array($cats)) {
        $placeholders = str_repeat('?,', count($cats) - 1) . '?';
        $where[] = "c.slug IN ($placeholders)";
        foreach ($cats as $c) { $params[] = sanitize($c); $types .= 's'; }
    } elseif (!empty($cats) && is_string($cats)) {
        $where[] = "c.slug = ?"; $params[] = sanitize($cats); $types .= 's';
    }
    
    if ($priceMin > 0) { $where[] = "COALESCE(p.sale_price, p.price) >= ?"; $params[] = $priceMin; $types .= 'd'; }
    if ($priceMax > 0 && $priceMax < 500000) { $where[] = "COALESCE(p.sale_price, p.price) <= ?"; $params[] = $priceMax; $types .= 'd'; }
    
    $whereSQL = implode(' AND ', $where);
    
    $orderSQL = match($sort) {
        'price_asc'  => 'ORDER BY COALESCE(p.sale_price, p.price) ASC',
        'price_desc' => 'ORDER BY COALESCE(p.sale_price, p.price) DESC',
        'name_asc'   => 'ORDER BY p.name ASC',
        'popular'    => 'ORDER BY p.views DESC',
        'sales'      => 'ORDER BY p.sale_price IS NULL ASC, p.sale_price ASC',
        default      => 'ORDER BY p.created_at DESC'
    };
    
    $total = dbFetchOne("SELECT COUNT(*) as t FROM products p JOIN categories c ON c.id=p.category_id WHERE $whereSQL", $types, ...$params)['t'] ?? 0;
    $pag = paginate($total, $perPage, $page);
    
    $products = dbFetchAll(
        "SELECT p.id, p.name, p.price, p.sale_price, p.images, p.featured, p.description,
                c.name AS category_name,
                ROUND(COALESCE((SELECT AVG(r.rating) FROM reviews r WHERE r.product_id = p.id), 4.5), 1) AS avg_rating
         FROM products p
         JOIN categories c ON c.id = p.category_id
         WHERE $whereSQL $orderSQL
         LIMIT {$perPage} OFFSET {$pag['offset']}",
        $types, ...$params
    );
    
    // Format image URLs
    foreach ($products as &$p) {
        $imgs = json_decode($p['images'] ?? '[]', true);
        $p['image_url'] = productImageUrl($imgs[0] ?? null);
        unset($p['images']);
    }
    
    echo json_encode(['products' => $products, 'total' => $total, 'page' => $page, 'per_page' => $perPage]);
    exit;
}

// ── REQUIRE POST + CSRF FOR MUTATIONS ─────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Support both form POST and JSON payload CSRF tokens
$token = $input['csrf_token'] ?? $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($token)) {
    echo json_encode(['success' => false, 'message' => 'Security token expired or invalid.']);
    exit;
}

// ── POST: Add to Cart ─────────────────────────────────────────
if ($action === 'add_to_cart') {
    $productId = (int)($input['product_id'] ?? 0);
    $qty = max(1, (int)($input['qty'] ?? 1));
    
    $p = dbFetchOne("SELECT id, stock FROM products WHERE id = ? AND status = 'active'", 'i', $productId);
    if (!$p) {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
        exit;
    }
    if ($p['stock'] < $qty) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock available.']);
        exit;
    }
    
    $userId = isLoggedIn() ? currentUserId() : null;
    $sid    = session_id();
    
    // Check if item exists in cart
    if ($userId) {
        $existing = dbFetchOne("SELECT id, qty FROM cart WHERE user_id = ? AND product_id = ?", 'ii', $userId, $productId);
    } else {
        $existing = dbFetchOne("SELECT id, qty FROM cart WHERE session_id = ? AND product_id = ?", 'si', $sid, $productId);
    }
    
    if ($existing) {
        $newQty = $existing['qty'] + $qty;
        if ($newQty > $p['stock']) $newQty = $p['stock']; // Cap at stock
        dbExecute("UPDATE cart SET qty = ? WHERE id = ?", 'ii', $newQty, $existing['id']);
    } else {
        dbInsert("INSERT INTO cart (user_id, session_id, product_id, qty) VALUES (?, ?, ?, ?)", 'isii', $userId, $sid, $productId, $qty);
    }
    
    echo json_encode(['success' => true, 'message' => 'Added to cart!', 'cart_count' => getCartCount()]);
    exit;
}

// ── POST: Update Cart Item Qty ────────────────────────────────
if ($action === 'update_cart') {
    $cartId = (int)($input['cart_id'] ?? 0);
    $qty    = max(1, (int)($input['qty'] ?? 1));
    
    $cart = dbFetchOne("SELECT c.id, p.price, p.sale_price, p.stock FROM cart c JOIN products p ON p.id = c.product_id WHERE c.id = ?", 'i', $cartId);
    if (!$cart) {
        echo json_encode(['success' => false, 'message' => 'Cart item not found.']);
        exit;
    }
    
    $finalQty = min($qty, $cart['stock']);
    dbExecute("UPDATE cart SET qty = ? WHERE id = ?", 'ii', $finalQty, $cartId);
    
    // Recalculate totals
    $userId = isLoggedIn() ? currentUserId() : null;
    $sid    = session_id();
    $items  = $userId 
        ? dbFetchAll("SELECT c.qty, p.price, p.sale_price FROM cart c JOIN products p ON p.id = c.product_id WHERE c.user_id = ?", 'i', $userId)
        : dbFetchAll("SELECT c.qty, p.price, p.sale_price FROM cart c JOIN products p ON p.id = c.product_id WHERE c.session_id = ?", 's', $sid);
        
    $subtotal = 0;
    foreach ($items as $it) $subtotal += ($it['sale_price'] ?: $it['price']) * $it['qty'];
    
    $shipping = $subtotal >= getSetting('free_shipping_above', 15000) ? 0 : (float)getSetting('shipping_cost', 500);
    $total    = $subtotal + $shipping;
    
    $unitPrice = $cart['sale_price'] ?: $cart['price'];
    
    echo json_encode([
        'success'    => true,
        'line_total' => formatPrice($unitPrice * $finalQty),
        'subtotal'   => formatPrice($subtotal),
        'shipping'   => $shipping > 0 ? formatPrice($shipping) : '<span class="text-success">Free</span>',
        'total'      => formatPrice($total),
        'cart_count' => cartCount()
    ]);
    exit;
}

// ── POST: Remove from Cart ────────────────────────────────────
if ($action === 'remove_from_cart') {
    $cartId = (int)($input['cart_id'] ?? 0);
    dbExecute("DELETE FROM cart WHERE id = ?", 'i', $cartId);
    
    // Recalculate totals
    $userId = isLoggedIn() ? currentUserId() : null;
    $sid    = session_id();
    $items  = $userId 
        ? dbFetchAll("SELECT c.qty, p.price, p.sale_price FROM cart c JOIN products p ON p.id = c.product_id WHERE c.user_id = ?", 'i', $userId)
        : dbFetchAll("SELECT c.qty, p.price, p.sale_price FROM cart c JOIN products p ON p.id = c.product_id WHERE c.session_id = ?", 's', $sid);
        
    $subtotal = 0;
    foreach ($items as $it) $subtotal += ($it['sale_price'] ?: $it['price']) * $it['qty'];
    $shipping = $subtotal >= getSetting('free_shipping_above', 15000) ? 0 : (float)getSetting('shipping_cost', 500);
    $total    = $subtotal + $shipping;
    
    echo json_encode([
        'success'    => true,
        'subtotal'   => formatPrice($subtotal),
        'shipping'   => $shipping > 0 ? formatPrice($shipping) : '<span class="text-success">Free</span>',
        'total'      => formatPrice($total),
        'cart_count' => cartCount()
    ]);
    exit;
}

// ── POST: Clear Cart ──────────────────────────────────────────
if ($action === 'clear_cart') {
    if (isLoggedIn()) {
        dbExecute("DELETE FROM cart WHERE user_id = ?", 'i', currentUserId());
    } else {
        dbExecute("DELETE FROM cart WHERE session_id = ?", 's', session_id());
    }
    echo json_encode(['success' => true]);
    exit;
}

// ── POST: Toggle Wishlist ─────────────────────────────────────
if ($action === 'toggle_wishlist') {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Please login to use wishlist.']);
        exit;
    }
    
    $productId = (int)($input['product_id'] ?? 0);
    $userId = currentUserId();
    
    $exists = dbFetchOne("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?", 'ii', $userId, $productId);
    
    if ($exists) {
        dbExecute("DELETE FROM wishlist WHERE id = ?", 'i', $exists['id']);
        echo json_encode(['success' => true, 'wishlisted' => false, 'message' => 'Removed from wishlist.']);
    } else {
        dbInsert("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)", 'ii', $userId, $productId);
        echo json_encode(['success' => true, 'wishlisted' => true, 'message' => 'Added to wishlist.']);
    }
    exit;
}

// ── POST: Add Review (Form Submit) ────────────────────────────
if ($action === 'add_review') {
    if (!isLoggedIn()) redirect('../login.php');
    
    $productId = (int)$_POST['product_id'];
    $rating    = (int)$_POST['rating'];
    $comment   = sanitize($_POST['comment']);
    
    if ($rating < 1 || $rating > 5) {
        setFlash('Invalid rating.', 'danger');
    } else {
        dbInsert("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)", 'iiis', $productId, currentUserId(), $rating, $comment);
        setFlash('Thank you! Your review has been submitted and is pending approval.', 'success');
    }
    
    redirect("../product.php?id=$productId");
}

echo json_encode(['success' => false, 'message' => 'Unknown action.']);
