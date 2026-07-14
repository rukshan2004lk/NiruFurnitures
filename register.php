<?php
/**
 * NiRu-Furnitures — Register
 */
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$next = $_GET['next'] ?? 'dashboard.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = [];
    if (empty($name) || strlen($name) < 2)
        $errors[] = "Please enter a valid name.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = "Please enter a valid email address.";
    if (empty($password) || strlen($password) < 8)
        $errors[] = "Password must be at least 8 characters long.";

    if (empty($errors)) {
        $exists = dbFetchOne("SELECT id FROM users WHERE email = ?", 's', $email);
        if ($exists) {
            $errors[] = "An account with this email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $userId = dbInsert("INSERT INTO users (name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, 'customer')", 'ssss', $name, $email, $phone, $hash);
            if ($userId) {
                $user = dbFetchOne("SELECT * FROM users WHERE id = ?", 'i', $userId);
                $sid = session_id();
                dbExecute("UPDATE cart SET user_id = ?, session_id = NULL WHERE session_id = ? AND user_id IS NULL", 'is', $userId, $sid);
                loginUser($user);
                setFlash('Account created successfully! Welcome to NiRu-Furnitures.', 'success');
                redirect($next);
            } else {
                $errors[] = "Failed to create account. Please try again later.";
            }
        }
    }

    if (!empty($errors)) {
        setFlash(implode('<br>', $errors), 'danger');
    }
}

$pageTitle = 'Create Account — NiRu-Furnitures';
$extraJs = [SITE_URL . '/assets/js/validation.js'];

include 'includes/head.php';
?>

<style>
    .auth-page-wrap {
        min-height: 100vh;
        display: flex;
    }

    .auth-brand-panel {
        width: 400px;
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
        font-size: .93rem;
        color: rgba(255, 255, 255, .7);
        line-height: 1.6;
        max-width: 270px;
    }

    .auth-brand-panel .brand-perks {
        margin-top: 2rem;
        display: flex;
        flex-direction: column;
        gap: .8rem;
    }

    .auth-brand-panel .brand-perk {
        display: flex;
        align-items: center;
        gap: .7rem;
        font-size: .82rem;
        color: rgba(255, 255, 255, .75);
    }

    .auth-brand-panel .brand-perk i {
        color: #f5ddc8;
        font-size: .95rem;
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
        padding: 2.25rem 2.5rem;
        width: 100%;
        max-width: 480px;
    }

    .auth-card .auth-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: #3d2219;
        margin-bottom: .3rem;
    }

    .auth-card .auth-subtitle {
        font-size: .86rem;
        color: #776965;
        margin-bottom: 1.5rem;
    }

    .auth-card .form-label {
        font-size: .84rem;
        font-weight: 600;
        color: #4a3028;
    }

    .auth-card .form-control {
        border-radius: 9px;
        border: 1px solid #ddd5cc;
        font-size: .875rem;
        padding: .6rem 1rem .6rem 2.6rem;
        background: #faf8f6;
        color: #3d2219;
    }

    .auth-card .form-control:focus {
        border-color: #9b6b55;
        box-shadow: 0 0 0 3px rgba(155, 107, 85, .15);
        background: #fff;
    }

    .auth-card .form-control.no-icon {
        padding-left: 1rem;
    }

    .auth-card .btn-auth {
        background: #53362e;
        border: none;
        border-radius: 9px;
        color: #fff;
        font-weight: 700;
        font-size: .9rem;
        padding: .72rem;
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

    @media(max-width:767.98px) {
        .auth-brand-panel {
            display: none;
        }

        .auth-form-panel {
            padding: 1.5rem 1rem;
        }

        .auth-card {
            padding: 1.75rem 1.25rem;
        }
    }
</style>

<div class="auth-page-wrap">
    <div class="auth-brand-panel">
        <div class="brand-name">NiRu</div>
        <div class="brand-tagline">Join thousands of happy customers who've transformed their homes.</div>
        <div class="brand-perks">
            <div class="brand-perk"><i class="bi bi-gift"></i> Exclusive member discounts</div>
            <div class="brand-perk"><i class="bi bi-heart"></i> Save products to your wishlist</div>
            <div class="brand-perk"><i class="bi bi-clock-history"></i> Track your orders in real time</div>
            <div class="brand-perk"><i class="bi bi-headset"></i> Priority customer support</div>
        </div>
    </div>

    <div class="auth-form-panel">
        <div class="auth-card">
            <h1 class="auth-title">Create account</h1>
            <p class="auth-subtitle">It's free and only takes a minute</p>

            <?= renderFlash() ?>

            <form method="POST" action="register.php<?= $next !== 'dashboard.php' ? '?next=' . urlencode($next) : '' ?>"
                class="needs-validation" novalidate>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-person" style="color:#9b6b55"></i>
                            <input type="text" class="form-control" id="name" name="name"
                                value="<?= e($_POST['name'] ?? '') ?>" placeholder="Your name" required minlength="2">
                            <div class="invalid-feedback">Please enter your name.</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label for="phone" class="form-label">Phone <span
                                class="text-muted fw-normal">(optional)</span></label>
                        <div class="input-icon-wrap">
                            <i class="bi bi-telephone" style="color:#9b6b55"></i>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                value="<?= e($_POST['phone'] ?? '') ?>" placeholder="+94 77 …">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="registerEmail" class="form-label">Email Address <span
                            class="text-danger">*</span></label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-envelope" style="color:#9b6b55"></i>
                        <input type="email" class="form-control" id="registerEmail" name="email"
                            value="<?= e($_POST['email'] ?? '') ?>" placeholder="you@example.com" required>
                        <div class="invalid-feedback">Please enter a valid email.</div>
                    </div>
                </div>

                <div class="mb-2">
                    <label for="passwordInput" class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-icon-wrap position-relative">
                        <i class="bi bi-lock" style="color:#9b6b55"></i>
                        <input type="password" class="form-control pe-5" id="passwordInput" name="password"
                            placeholder="Min. 8 characters" required minlength="8">
                        <button type="button"
                            class="btn btn-link p-0 position-absolute end-0 top-50 translate-middle-y me-3"
                            style="color:#9b6b55" data-toggle-password="passwordInput" aria-label="Show password">
                            <i class="bi bi-eye"></i>
                        </button>
                        <div class="invalid-feedback">Password must be at least 8 characters.</div>
                    </div>
                    <div class="progress mt-2" style="height:5px;border-radius:5px;background:#ede8e3">
                        <div id="passwordStrengthBar" class="progress-bar" role="progressbar"
                            style="width:0%;border-radius:5px"></div>
                    </div>
                    <div id="passwordStrengthText" class="small mt-1 text-end"
                        style="color:#9b6b55;min-height:16px;font-size:.75rem"></div>
                </div>

                <div class="mb-3">
                    <label for="confirmPasswordInput" class="form-label">Confirm Password <span
                            class="text-danger">*</span></label>
                    <div class="input-icon-wrap position-relative">
                        <i class="bi bi-shield-lock" style="color:#9b6b55"></i>
                        <input type="password" class="form-control pe-5" id="confirmPasswordInput"
                            placeholder="Repeat password" required>
                        <div class="invalid-feedback">Passwords do not match.</div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="terms" required>
                        <label class="form-check-label" for="terms" style="font-size:.82rem;color:#776965">
                            I agree to the <a href="#" class="auth-link">Terms of Service</a> and <a href="#"
                                class="auth-link">Privacy Policy</a>
                        </label>
                        <div class="invalid-feedback">You must agree before submitting.</div>
                    </div>
                </div>

                <button type="submit" class="btn-auth mb-3">Create Account</button>

                <p class="text-center mb-0" style="font-size:.84rem;color:#776965">
                    Already have an account?
                    <a href="login.php<?= $next !== 'dashboard.php' ? '?next=' . urlencode($next) : '' ?>"
                        class="auth-link ms-1">Sign in</a>
                </p>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>