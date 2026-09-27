<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION['u'])) {
    header("Location: ../login.php");
    exit();
}

$user = $_SESSION['u'];
$user_id = (int)$user['user_id'];
$order_id = (int)($_GET['order_id'] ?? $_GET['id'] ?? 0);

if ($order_id <= 0) {
    header("Location: dashboard.php");
    exit();
}

$order_query = "SELECT o.*, s.status_name 
                FROM `orders` o 
                LEFT JOIN `status` s ON o.order_status_id = s.status_id 
                WHERE o.order_id = '$order_id' AND o.user_id = '$user_id'";
$order_rs = Database::search($order_query);

if ($order_rs->num_rows == 0) {
    header("Location: dashboard.php");
    exit();
}

$order = $order_rs->fetch_assoc();

$payment_rs = Database::search("SELECT * FROM `payments` WHERE `order_id` = '$order_id' LIMIT 1");
$payment = ($payment_rs->num_rows > 0) ? $payment_rs->fetch_assoc() : null;

$items_rs = Database::search("SELECT oi.*, 
                                     (SELECT image_path FROM product_images WHERE product_id = oi.product_id ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS image_path 
                              FROM `order_items` oi 
                              WHERE oi.order_id = '$order_id'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice - <?php echo htmlspecialchars($order['order_number']); ?></title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">

  <style>
    @media print {
      body {
        background-color: #fff !important;
      }
      .no-print {
        display: none !important;
      }
      .invoice-container {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
      }
    }
  </style>
</head>
<body class="bg-light py-5">

  <div class="container-xl max-w-1320">
    
    <!-- Action Bar (Hidden during Print) -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
      <a href="dashboard.php" class="btn btn-outline-dark rounded-3 px-3">
        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
      </a>
      <button onclick="window.print();" class="btn btn-niru-primary rounded-3 px-4 shadow-sm">
        <i class="bi bi-printer me-2"></i> Print Invoice
      </button>
    </div>

    <!-- Printable Invoice Sheet -->
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 invoice-container bg-white">
      
      <!-- Top Header -->
      <div class="d-flex flex-wrap justify-content-between align-items-start border-bottom pb-4 mb-4 gap-3">
        <div>
          <h2 class="display-6 fw-bold mb-1" style="color: var(--niru-primary);">NiRu</h2>
          <p class="text-muted small mb-0">Premium Modern Furniture & Living</p>
          <p class="text-muted small mb-0">153/8A Temple Rd, Alluthwatta, Wadduwa</p>
          <p class="text-muted small mb-0">contact@nirufurniture.com | +94 11 234 5678</p>
        </div>
        <div class="text-md-end">
          <h3 class="fs-4 fw-bold mb-1 text-uppercase">INVOICE</h3>
          <p class="text-muted small mb-1">Invoice No: <strong class="text-dark">#INV-<?php echo str_pad($order['order_id'], 5, '0', STR_PAD_LEFT); ?></strong></p>
          <p class="text-muted small mb-1">Order Ref: <strong class="text-dark"><?php echo htmlspecialchars($order['order_number']); ?></strong></p>
          <p class="text-muted small mb-0">Date: <strong class="text-dark"><?php echo date("M d, Y - h:i A", strtotime($order['placed_at'])); ?></strong></p>
        </div>
      </div>

      <!-- Billing / Shipping & Payment Details -->
      <div class="row g-4 mb-4 pb-2 border-bottom">
        <div class="col-12 col-md-6">
          <h6 class="fw-bold text-muted small text-uppercase mb-2">Billed & Shipped To:</h6>
          <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($order['customer_name']); ?></h5>
          <p class="text-muted mb-1"><?php echo htmlspecialchars($order['shipping_address']); ?></p>
          <p class="text-muted mb-1"><?php echo htmlspecialchars($order['shipping_city']); ?><?php echo !empty($order['shipping_postal_code']) ? ' - ' . htmlspecialchars($order['shipping_postal_code']) : ''; ?></p>
          <p class="text-muted mb-0"><i class="bi bi-telephone me-1"></i><?php echo htmlspecialchars($order['customer_phone']); ?></p>
          <p class="text-muted mb-0"><i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($order['customer_email']); ?></p>
        </div>

        <div class="col-12 col-md-6 text-md-end">
          <h6 class="fw-bold text-muted small text-uppercase mb-2">Order Information:</h6>
          <p class="mb-1">
            <span class="text-muted">Payment Status:</span>
            <span class="badge bg-success-subtle text-success px-2 py-1 fw-bold text-uppercase"><?php echo htmlspecialchars($order['payment_status']); ?></span>
          </p>
          <p class="mb-1">
            <span class="text-muted">Order Status:</span>
            <span class="badge bg-primary-subtle text-primary px-2 py-1 fw-bold"><?php echo htmlspecialchars($order['status_name'] ?? 'Pending'); ?></span>
          </p>
          <?php if ($payment) { ?>
            <p class="mb-1"><span class="text-muted">Payment Method:</span> <strong class="text-dark text-capitalize"><?php echo htmlspecialchars($payment['payment_method']); ?></strong></p>
            <p class="mb-0"><span class="text-muted">Transaction ID:</span> <span class="font-monospace"><?php echo htmlspecialchars($payment['transaction_id'] ?? 'N/A'); ?></span></p>
          <?php } ?>
        </div>
      </div>

      <!-- Line Items Table -->
      <div class="table-responsive mb-4">
        <table class="table align-middle">
          <thead class="table-light">
            <tr>
              <th scope="col" class="py-3">Item Description</th>
              <th scope="col" class="py-3 text-center">Unit Price</th>
              <th scope="col" class="py-3 text-center">Qty</th>
              <th scope="col" class="py-3 text-end">Line Total</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $subtotal = 0.00;
            while ($item = $items_rs->fetch_assoc()) { 
              $subtotal += (float)$item['line_total'];
              $img = !empty($item['image_path']) ? '../' . $item['image_path'] : '../Images/products/nordic_lounge.png';
            ?>
              <tr>
                <td class="py-3">
                  <div class="d-flex align-items-center gap-3">
                    <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="rounded-2" style="width: 48px; height: 48px; object-fit: cover; background: #f8f9fa;">
                    <div>
                      <div class="fw-semibold text-dark"><?php echo htmlspecialchars($item['product_name']); ?></div>
                      <?php if (!empty($item['color'])) { ?>
                        <div><span class="badge bg-secondary-subtle text-dark border px-2 py-1 my-1" style="font-size: 11px;">Color: <?php echo htmlspecialchars($item['color']); ?></span></div>
                      <?php } ?>
                      <small class="text-muted d-block" style="font-size: 11px;">Product ID: #<?php echo (int)$item['product_id']; ?></small>
                    </div>
                  </div>
                </td>
                <td class="py-3 text-center">Rs. <?php echo number_format($item['unit_price'], 2); ?></td>
                <td class="py-3 text-center"><?php echo (int)$item['quantity']; ?></td>
                <td class="py-3 text-end fw-semibold">Rs. <?php echo number_format($item['line_total'], 2); ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>

      <!-- Totals Breakdown -->
      <div class="row justify-content-end mb-4">
        <div class="col-12 col-md-5 col-lg-4">
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Subtotal:</span>
            <span class="fw-semibold">Rs. <?php echo number_format($subtotal, 2); ?></span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Shipping (Standard Insured):</span>
            <span class="text-success fw-semibold">FREE</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Tax:</span>
            <span class="fw-semibold">Rs. 0.00</span>
          </div>
          <hr class="my-2">
          <div class="d-flex justify-content-between fs-5 fw-bold" style="color: var(--niru-primary);">
            <span>Total Amount Paid:</span>
            <span>Rs. <?php echo number_format($order['total_amount'], 2); ?></span>
          </div>
        </div>
      </div>

      <!-- Bottom Thank You Note -->
      <div class="border-top pt-4 text-center text-muted small">
        <p class="mb-1 fw-semibold text-dark">Thank you for choosing NiRu Furnitures!</p>
        <p class="mb-0">All items are covered under our 30-Day Money Back Guarantee & Manufacturer Warranty.</p>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>