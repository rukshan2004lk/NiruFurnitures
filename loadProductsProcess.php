<?php
require_once "connection.php";

$category_id = (int)($_GET["category"] ?? 0);
$sort        = $_GET["sort"] ?? "newest";
$max_price   = (float)($_GET["price"] ?? 50000);
$search      = trim($_GET["search"] ?? "");

// 1. Base Query with Price Limit
$where_clauses = ["`price` <= " . $max_price];

// 2. Search Filter
if (!empty($search)) {
    $where_clauses[] = "(`name` LIKE '%" . $search . "%' OR `description` LIKE '%" . $search . "%')";
}

// 3. Category Filter
if ($category_id > 0) {
    $where_clauses[] = "`category_id` = " . $category_id;
}

$query = "SELECT * FROM `products` WHERE " . implode(" AND ", $where_clauses);

// 4. Dynamic Sorting Logic
switch ($sort) {
    case "popular":
        // Sorts by popularity/views/id desc
        $query .= " ORDER BY `product_id` DESC";
        break;
    case "sales":
        // Sorts by lowest price or promotional products
        $query .= " ORDER BY `price` ASC";
        break;
    case "price_low":
        $query .= " ORDER BY `price` ASC";
        break;
    case "price_high":
        $query .= " ORDER BY `price` DESC";
        break;
    case "newest":
    default:
        $query .= " ORDER BY `product_id` DESC";
        break;
}

$product_rs = Database::search($query);

if ($product_rs->num_rows > 0) {
    while ($product_data = $product_rs->fetch_assoc()) {

        $image_rs = Database::search("SELECT `image_path` FROM `product_images` 
                                      WHERE `product_id`='" . $product_data["product_id"] . "' 
                                      ORDER BY `is_primary` DESC, `sort_order` ASC LIMIT 1");
        
        $image_data = $image_rs->fetch_assoc();
        $image_src = $image_data["image_path"] ?? "Images/products/nordic_lounge.png";
?>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="shop-product-card">
                <div class="shop-product-img">
                    <a href="product-detail.php?id=<?php echo $product_data['product_id']; ?>">
                        <img src="<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($product_data["name"]); ?>">
                    </a>
                    <button class="favorite-btn" aria-label="Favorite">
                        <i class="bi bi-heart"></i>
                    </button>
                </div>
                <div class="p-4 d-flex flex-column justify-content-between flex-grow-1">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h3 class="fs-5 fw-semibold mb-0" style="color: var(--niru-primary);">
                                <a href="product-detail.php?id=<?php echo $product_data['product_id']; ?>" class="text-decoration-none" style="color: inherit;">
                                    <?php echo htmlspecialchars($product_data["name"]); ?>
                                </a>
                            </h3>
                            <div class="rating-badge">
                                <i class="bi bi-star-fill text-warning me-1"></i>4.8
                            </div>
                        </div>
                        <p class="small text-muted mb-3" style="color: var(--niru-body-text);">
                            <?php echo htmlspecialchars(substr($product_data["description"] ?? "", 0, 30)) . "..."; ?>
                        </p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-2">
                        <span class="fs-5 fw-semibold" style="color: var(--niru-primary);">
                            Rs. <?php echo number_format($product_data["price"]); ?>
                        </span>
                        <a href="product-detail.php?id=<?php echo $product_data['product_id']; ?>" class="btn btn-niru-sm text-decoration-none">View Details</a>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
} else {
?>
    <div class="col-12 text-center py-5">
        <h4 class="text-muted">No products found matching your filter criteria.</h4>
    </div>
<?php
}
?>