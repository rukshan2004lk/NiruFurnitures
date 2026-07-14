<?php
/**
 * NiRu-Furnitures — Authentication Helpers
 * -------------------------------------------------------
 * Role checks, login gate, user data fetching.
 * Depends on session.php and database.php.
 */

// ── Checkers ──────────────────────────────────────────────────

function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

function isAdmin(): bool
{
    return !empty($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'admin';
}

// ── Gates ─────────────────────────────────────────────────────

/**
 * requireLogin(string $redirect = 'login.php'): void
 * Redirects to login page if user is not logged in.
 */
function requireLogin(string $redirect = 'login.php'): void
{
    if (!isLoggedIn()) {
        setFlash('warning', 'Please log in to continue.');
        redirect($redirect);
    }
}

/**
 * requireAdmin(): void
 * Redirects to admin login if not an admin.
 */
function requireAdmin(): void
{
    if (!isLoggedIn()) {
        setFlash('warning', 'Please log in as an administrator to continue.');
        redirect(SITE_URL . '/login.php');
    } elseif (!isAdmin()) {
        redirect(SITE_URL . '/access-denied.php');
    }
}

/**
 * requireGuest(string $redirect = 'dashboard.php'): void
 * Redirects logged-in users away from guest-only pages (login/register).
 */
function requireGuest(string $redirect = 'dashboard.php'): void
{
    if (isLoggedIn()) {
        redirect(isAdmin() ? SITE_URL . '/admin/dashboard.php' : $redirect);
    }
}

// ── Session Helpers ───────────────────────────────────────────

/**
 * loginUser(array $user): void
 * Stores user data in session after successful authentication.
 */
function loginUser(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_avatar'] = $user['avatar'] ?? null;
    $_SESSION['created_at'] = time();
}

/**
 * logoutUser(): void
 * Destroys the session.
 */
function logoutUser(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();
}

/**
 * currentUser(): array|null
 * Returns the current user's DB row or null.
 */
function currentUser(): ?array
{
    if (!isLoggedIn())
        return null;
    static $cached = null;
    if ($cached === null) {
        $cached = dbFetchOne("SELECT * FROM users WHERE id = ? LIMIT 1", 'i', $_SESSION['user_id']);
    }
    return $cached;
}

/**
 * currentUserId(): int
 */
function currentUserId(): int
{
    return (int) ($_SESSION['user_id'] ?? 0);
}
