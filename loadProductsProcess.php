<?php
require_once "connection.php";

$category_id = (int)($_GET["category"] ?? 0);
$sort        = $_GET["sort"] ?? "newest";
$max_price   = (float)($_GET["price"] ?? 50000);
$search      = trim($_GET["search"] ?? "");
$page        = (int)($_GET["page"] ?? 1);
if ($page < 1) $page = 1;

$results_per_page = 6; // Adjust items per page as needed

// 1. Where Conditions
$where_clauses = ["`price` <= " . $max_price];
if (!empty($search)) {
    $where_clauses[] = "(`name` LIKE '%" . $search . "%' OR `description` LIKE '%" . $search . "%')";
}
if ($category_id > 0) {
    $where_clauses[] = "`category_id` = " . $category_id;
}

$where_sql = " WHERE " . implode(" AND ", $where_clauses);

// 2. Count Total Records for Pagination
$total_rs = Database::search("SELECT COUNT(*) AS `total` FROM `products`" . $where_sql);
$total_data = $total_rs->fetch_assoc();
$total_records = $total_data["total"] ?? 0;
$number_of_pages = ceil($total_records / $results_per_page);

// 3. Sorting SQL
$order_by = " ORDER BY `product_id` DESC";
switch ($sort) {
    case "price_low":
    case "sales":
        $order_by = " ORDER BY `price` ASC";
        break;
    case "price_high":
        $order_by = " ORDER BY `price` DESC";
        break;
    case "popular":
    case "newest":
    default:
        $order_by = " ORDER BY `product_id` DESC";
        break;
}

// 4. Offset Calculation
$offset = ($page - 1) * $results_per_page;
$query = "SELECT * FROM `products`" . $where_sql . $order_by . " LIMIT " . $results_per_page . " OFFSET " . $offset;

$product_rs = Database::search($query);

// Render Products
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

    // Dynamic Pagination Bar
    if ($number_of_pages > 1) {
?>
        <div class="col-12 mt-4">
            <div class="d-flex align-items-center justify-content-center gap-2">
                <!-- Previous Button -->
                <?php if ($page > 1) { ?>
                    <a href="#" onclick="changePage(<?php echo $page - 1; ?>); return false;" class="pagination-btn" aria-label="Previous Page">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                <?php } ?>

                <!-- Page Number Buttons -->
                <?php for ($p = 1; $p <= $number_of_pages; $p++) { ?>
                    <a href="#" onclick="changePage(<?php echo $p; ?>); return false;" class="pagination-btn <?php echo ($p == $page) ? 'active' : ''; ?>">
                        <?php echo $p; ?>
                    </a>
                <?php } ?>

                <!-- Next Button -->
                <?php if ($page < $number_of_pages) { ?>
                    <a href="#" onclick="changePage(<?php echo $page + 1; ?>); return false;" class="pagination-btn" aria-label="Next Page">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                <?php } ?>
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