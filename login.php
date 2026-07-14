<?php
/**
 * NiRu-Furnitures — Login
 */
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? 'admin/dashboard.php' : 'dashboard.php');
}

$next = $_GET['next'] ?? 'dashboard.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        setFlash('Please enter email and password.', 'danger');
    } else {
        $user = dbFetchOne("SELECT * FROM users WHERE email = ? LIMIT 1", 's', $email);

        if ($user && password_verify($password, $user['password_hash'])) {
            if (!in_array((string) $user['status'], ['1', 'active'], true)) {
                setFlash('Your account is currently disabled. Please contact support.', 'danger');
            } else {
                $sid = session_id();
                dbExecute("UPDATE cart SET user_id = ?, session_id = NULL WHERE session_id = ? AND user_id IS NULL", 'is', $user['id'], $sid);
                loginUser($user);
                setFlash('Welcome back, ' . e($user['name']) . '!', 'success');
                redirect(isAdmin() ? 'admin/dashboard.php' : $next);
            }
        } else {
            setFlash('Invalid email or password.', 'danger');
        }
    }
}

$pageTitle = 'Login — NiRu-Furnitures';
$extraJs = [SITE_URL . '/assets/js/validation.js'];

include 'includes/head.php';
?>

<style>
    .auth-page-wrap {
        min-height: 100vh;
        display: flex;
    }

    .auth-brand-panel {
        width: 420px;
        flex-shrink: 0;
        background: linear-gradient(160deg, #3d2219 0%, #6b3f2f 100%);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        padding: 3rem 3.5rem;
        color: #fff;
    }

    .auth-brand-panel .brand-name {
        font-family: 'Poppins', sans-serif;
        font-size: 2.6rem;
        font-weight: 800;
        letter-spacing: -1px;
        color: #f5ddc8;
        margin-bottom: .5rem;
    }

    .auth-brand-panel .brand-tagline {
        font-size: .95rem;
        color: rgba(255, 255, 255, .7);
        line-height: 1.6;
        max-width: 280px;
    }

    .auth-brand-panel .brand-perks {
        margin-top: 2.5rem;
        display: flex;
        flex-direction: column;
        gap: .85rem;
    }

    .auth-brand-panel .brand-perk {
        display: flex;
        align-items: center;
        gap: .75rem;
        font-size: .83rem;
        color: rgba(255, 255, 255, .75);
    }

    .auth-brand-panel .brand-perk i {
        color: #f5ddc8;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .auth-form-panel {
        flex: 1;
        background: #f4f1ef;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1.5rem;
    }

    .auth-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e0d8d0;
        box-shadow: 0 4px 24px rgba(74, 44, 36, .08);
        padding: 2.5rem 2.75rem;
        width: 100%;
        max-width: 440px;
    }

    .auth-card .auth-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.55rem;
        font-weight: 700;
        color: #3d2219;
        margin-bottom: .35rem;
    }

    .auth-card .auth-subtitle {
        font-size: .87rem;
        color: #776965;
        margin-bottom: 1.75rem;
    }

    .auth-card .form-label {
        font-size: .85rem;
        font-weight: 600;
        color: #4a3028;
    }

    .auth-card .form-control {
        border-radius: 9px;
        border: 1px solid #ddd5cc;
        font-size: .875rem;
        padding: .65rem 1rem .65rem 2.6rem;
        background: #faf8f6;
        color: #3d2219;
    }

    .auth-card .form-control:focus {
        border-color: #9b6b55;
        box-shadow: 0 0 0 3px rgba(155, 107, 85, .15);
        background: #fff;
    }

    .auth-card .btn-auth {
        background: #53362e;
        border: none;
        border-radius: 9px;
        color: #fff;
        font-weight: 700;
        font-size: .9rem;
        padding: .75rem;
        width: 100%;
        transition: background 200ms;
    }

    .auth-card .btn-auth:hover {
        background: #3d2219;
    }

    .auth-card .auth-link {
        color: #7d4f3f;
        font-weight: 600;
        text-decoration: none;
    }

    .auth-card .auth-link:hover {
        color: #3d2219;
        text-decoration: underline;
    }

    @media (max-width: 767.98px) {
        .auth-brand-panel {
            display: none;
        }

        .auth-form-panel {
            padding: 2rem 1rem;
        }

        .auth-card {
            padding: 2rem 1.5rem;
        }
    }
</style>

<div class="auth-page-wrap">
    <!-- Brand Panel -->
    <div class="auth-brand-panel">
        <div class="brand-name">NiRu</div>
        <div class="brand-tagline">Stylish furniture crafted for modern Sri Lankan living.</div>
        <div class="brand-perks">
            <div class="brand-perk"><i class="bi bi-truck"></i> Free delivery on orders over Rs. 15,000</div>
            <div class="brand-perk"><i class="bi bi-arrow-counterclockwise"></i> 14-day hassle-free returns</div>
            <div class="brand-perk"><i class="bi bi-shield-check"></i> 2-year warranty on all products</div>
            <div class="brand-perk"><i class="bi bi-bag-heart"></i> Exclusive deals for members</div>
        </div>
    </div>

    <!-- Form Panel -->
    <div class="auth-form-panel">
        <div class="auth-card">
            <h1 class="auth-title">Welcome back</h1>
            <p class="auth-subtitle">Sign in to your NiRu account</p>

            <?= renderFlash() ?>

            <form method="POST" action="login.php<?= $next !== 'dashboard.php' ? '?next=' . urlencode($next) : '' ?>"
                class="needs-validation" novalidate>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-envelope" style="color:#9b6b55"></i>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?= e($_POST['email'] ?? '') ?>" placeholder="you@example.com" required autofocus>
                        <div class="invalid-feedback">Please enter a valid email.</div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="passwordInput" class="form-label mb-0">Password</label>
                        <a href="forgot-password.php" class="auth-link" style="font-size:.8rem">Forgot password?</a>
                    </div>
                    <div class="input-icon-wrap position-relative">
                        <i class="bi bi-lock" style="color:#9b6b55"></i>
                        <input type="password" class="form-control pe-5" id="passwordInput" name="password"
                            placeholder="••••••••" required>
                        <button type="button"
                            class="btn btn-link p-0 position-absolute end-0 top-50 translate-middle-y me-3"
                            style="color:#9b6b55" data-toggle-password="passwordInput" aria-label="Show password">
                            <i class="bi bi-eye"></i>
                        </button>
                        <div class="invalid-feedback">Please enter your password.</div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label small" for="remember" style="color:#776965">Remember me for 30
                            days</label>
                    </div>
                </div>

                <button type="submit" class="btn-auth mb-3">Sign In</button>

                <p class="text-center mb-0" style="font-size:.84rem;color:#776965">
                    Don't have an account?
                    <a href="register.php<?= $next !== 'dashboard.php' ? '?next=' . urlencode($next) : '' ?>"
                        class="auth-link ms-1">Create one</a>
                </p>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>