<?php
/**
 * NiRu-Furnitures — Session Manager
 * -------------------------------------------------------
 * Starts and configures the PHP session securely.
 * Generates a CSRF token and stores flash messages.
 * Include AFTER config.php.
 */

// ── Configure session before start ───────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);

    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/',
        'secure'   => false,   // set true on HTTPS production
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

// ── Regenerate session ID periodically (anti-fixation) ───────
if (!isset($_SESSION['created_at'])) {
    $_SESSION['created_at'] = time();
} elseif (time() - $_SESSION['created_at'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created_at'] = time();
}

// ── CSRF Token ────────────────────────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * csrfToken(): string  — returns the current CSRF token
 */
function csrfToken(): string
{
    return $_SESSION['csrf_token'] ?? '';
}

/**
 * csrfField(): string  — returns a hidden HTML input field
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken()) . '">';
}

/**
 * verifyCsrf(): void  — aborts with 403 if token mismatch
 */
function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!verifyCsrfToken($token)) {
        http_response_code(403);
        die('Invalid CSRF token. Please go back and try again.');
    }
}

/**
 * verifyCsrfToken(string $token): bool  — returns true if token matches
 */
function verifyCsrfToken(string $token): bool
{
    return hash_equals(csrfToken(), $token);
}

// ── Flash Messages ────────────────────────────────────────────
/**
 * setFlash(string $type, string $message): void
 * Types: 'success' | 'error' | 'warning' | 'info'
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

/**
 * getFlash(): array  — returns all flashes and clears them
 */
function getFlash(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

/**
 * renderFlash(): void  — echoes Bootstrap alerts for all flashes
 */
function renderFlash(): void
{
    foreach (getFlash() as $flash) {
        $type = match($flash['type']) {
            'success' => 'success',
            'error'   => 'danger',
            'warning' => 'warning',
            default   => 'info',
        };
        $icon = match($flash['type']) {
            'success' => 'bi-check-circle-fill',
            'error'   => 'bi-x-circle-fill',
            'warning' => 'bi-exclamation-triangle-fill',
            default   => 'bi-info-circle-fill',
        };
        echo '<div class="alert alert-' . $type . ' alert-dismissible fade show d-flex align-items-center gap-2" role="alert">';
        echo '<i class="bi ' . $icon . '"></i>';
        echo '<span>' . htmlspecialchars($flash['message']) . '</span>';
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
}
