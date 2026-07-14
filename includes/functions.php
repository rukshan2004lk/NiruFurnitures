<?php
/**
 * NiRu-Furnitures — Global Utility Functions
 * -------------------------------------------------------
 * Sanitisation, formatting, redirects, image uploads,
 * cart helpers, pagination, and more.
 */

// ── String / Output ───────────────────────────────────────────

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function sanitize(string $value): string
{
    return trim(strip_tags($value));
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\-]/', '-', $text);
    return preg_replace('/-+/', '-', $text);
}

// ── Redirects ─────────────────────────────────────────────────

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function redirectBack(string $fallback = '/'): never
{
    redirect($_SERVER['HTTP_REFERER'] ?? $fallback);
}

// ── Formatting ────────────────────────────────────────────────

function formatPrice(float $amount, string $symbol = null): string
{
    $symbol = $symbol ?? CURRENCY;
    return $symbol . ' ' . number_format($amount, 2);
}

function formatDate(string $datetime, string $format = 'd M Y'): string
{
    return date($format, strtotime($datetime));
}

function timeAgo(string $datetime): string
{
    $diff = time() - strtotime($datetime);
    return match (true) {
        $diff < 60 => 'just now',
        $diff < 3600 => floor($diff / 60) . ' min ago',
        $diff < 86400 => floor($diff / 3600) . ' hr ago',
        $diff < 604800 => floor($diff / 86400) . ' day(s) ago',
        default => date('d M Y', strtotime($datetime)),
    };
}

function truncate(string $text, int $length = 100): string
{
    return strlen($text) > $length ? substr($text, 0, $length) . '…' : $text;
}

// ── Image Helpers ─────────────────────────────────────────────

/**
 * uploadImage(array $file, string $folder = 'products'): string|false
 * Returns the saved filename or false on failure.
 */
function uploadImage(array $file, string $folder = 'products'): string|false
{
    if ($file['error'] !== UPLOAD_ERR_OK)
        return false;
    if ($file['size'] > MAX_FILE_SIZE)
        return false;
    if (!in_array($file['type'], ALLOWED_TYPES, true))
        return false;

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_', true) . '.' . strtolower($ext);
    $dest = UPLOADS_PATH . '/' . $folder . '/' . $filename;

    if (!is_dir(dirname($dest))) {
        mkdir(dirname($dest), 0755, true);
    }

    return move_uploaded_file($file['tmp_name'], $dest) ? $filename : false;
}

/**
 * productImageUrl(string|null $filename, string $folder = 'products'): string
 * Returns the public URL or a placeholder URL.
 */
function productImageUrl(?string $filename, string $folder = 'products'): string
{
    if ($filename && str_starts_with($filename, 'http')) {
        return $filename;
    }
    if ($filename && file_exists(UPLOADS_PATH . '/' . $folder . '/' . $filename)) {
        return UPLOADS_URL . '/' . $folder . '/' . e($filename);
    }
    // Inline SVG placeholder via picsum / placeholder service
    return 'https://placehold.co/600x450/F3EFE7/8B7355?text=NiRu+Furnitures';
}

/**
 * avatarUrl(string|null $filename): string
 */
function avatarUrl(?string $filename): string
{
    if ($filename && file_exists(UPLOADS_PATH . '/profiles/' . $filename)) {
        return UPLOADS_URL . '/profiles/' . e($filename);
    }
    return SITE_URL . '/assets/img/icons8-user-90.png';
}

// ── Cart Helpers ──────────────────────────────────────────────

function getCartCount(): int
{
    global $conn;
    if (isLoggedIn()) {
        $row = dbFetchOne(
            "SELECT SUM(qty) AS total FROM cart WHERE user_id = ?",
            'i',
            currentUserId()
        );
    } else {
        $sid = session_id();
        $row = dbFetchOne(
            "SELECT SUM(qty) AS total FROM cart WHERE session_id = ?",
            's',
            $sid
        );
    }
    return (int) ($row['total'] ?? 0);
}

function getWishlistCount(): int
{
    if (!isLoggedIn())
        return 0;
    $row = dbFetchOne(
        "SELECT COUNT(*) AS total FROM wishlist WHERE user_id = ?",
        'i',
        currentUserId()
    );
    return (int) ($row['total'] ?? 0);
}

/**
 * getUserWishlistIds(): array
 * Returns an array of product IDs in the current user's wishlist.
 */
function getUserWishlistIds(): array
{
    if (!isLoggedIn()) return [];
    static $wishlistIds = null;
    if ($wishlistIds === null) {
        $rows = dbFetchAll("SELECT product_id FROM wishlist WHERE user_id = ?", 'i', currentUserId());
        $wishlistIds = array_column($rows, 'product_id');
    }
    return $wishlistIds;
}

// ── Pagination ────────────────────────────────────────────────

/**
 * paginate(int $total, int $perPage, int $current): array
 * Returns ['offset' => int, 'pages' => int, 'current' => int]
 */
function paginate(int $total, int $perPage = 12, int $current = 1): array
{
    $pages = (int) ceil($total / $perPage);
    $current = max(1, min($current, $pages));
    $offset = ($current - 1) * $perPage;
    return compact('offset', 'pages', 'current', 'total', 'perPage');
}

/**
 * paginationLinks(int $pages, int $current, string $baseUrl): string
 * Renders Bootstrap pagination HTML.
 */
function paginationLinks(int $pages, int $current, string $baseUrl): string
{
    if ($pages <= 1)
        return '';
    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center flex-wrap gap-1">';
    for ($i = 1; $i <= $pages; $i++) {
        $active = $i === $current ? ' active' : '';
        $url = $baseUrl . (str_contains($baseUrl, '?') ? '&' : '?') . 'page=' . $i;
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . e($url) . '">' . $i . '</a></li>';
    }
    $html .= '</ul></nav>';
    return $html;
}

// ── Order Number ──────────────────────────────────────────────
function generateOrderNumber(): string
{
    return 'NRF-' . strtoupper(substr(uniqid(), -6)) . '-' . date('ymd');
}

// ── Star Rating HTML ──────────────────────────────────────────
function starRating(float $rating, int $max = 5): string
{
    $html = '<span class="star-rating">';
    for ($i = 1; $i <= $max; $i++) {
        if ($rating >= $i) {
            $html .= '<i class="bi bi-star-fill text-warning"></i>';
        } elseif ($rating >= $i - 0.5) {
            $html .= '<i class="bi bi-star-half text-warning"></i>';
        } else {
            $html .= '<i class="bi bi-star text-warning"></i>';
        }
    }
    $html .= '</span>';
    return $html;
}

// ── Stock Badge ───────────────────────────────────────────────
function stockBadge(int $stock): string
{
    if ($stock <= 0) {
        return '<span class="badge bg-danger">Out of Stock</span>';
    } elseif ($stock <= 5) {
        return '<span class="badge bg-warning text-dark">Only ' . $stock . ' left</span>';
    }
    return '<span class="badge bg-success">In Stock</span>';
}

// ── Order Status Badge ────────────────────────────────────────
function orderStatusBadge(string $status): string
{
    $map = [
        'pending' => ['label' => 'Pending', 'class' => 'status-pending'],
        'processing' => ['label' => 'Processing', 'class' => 'status-processing'],
        'shipped' => ['label' => 'Shipped', 'class' => 'status-shipped'],
        'delivered' => ['label' => 'Delivered', 'class' => 'status-delivered'],
        'cancelled' => ['label' => 'Cancelled', 'class' => 'status-cancelled'],
        'refunded' => ['label' => 'Refunded', 'class' => 'status-refunded'],
    ];
    $s = $map[$status] ?? ['label' => ucfirst($status), 'class' => 'status-pending'];
    return '<span class="status-badge ' . $s['class'] . '">' . $s['label'] . '</span>';
}
