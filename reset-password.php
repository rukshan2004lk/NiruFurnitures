<?php
/**
 * NiRu-Furnitures — Reset Password (reset-password.php)
 */
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$token = $_GET['token'] ?? '';

if (empty($token)) {
    setFlash('Invalid or missing reset token.', 'danger');
    redirect('login.php');
}

// Verify token
$user = dbFetchOne("SELECT id, email, reset_expires FROM users WHERE reset_token = ? LIMIT 1", 's', $token);

if (!$user || strtotime($user['reset_expires']) < time()) {
    setFlash('The password reset link is invalid or has expired.', 'danger');
    redirect('forgot-password.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    
    if (empty($password) || strlen($password) < 8) {
        setFlash('Password must be at least 8 characters long.', 'danger');
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        dbExecute("UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?", 'si', $hash, $user['id']);
        
        setFlash('Your password has been successfully reset. You can now login.', 'success');
        redirect('login.php');
    }
}

$pageTitle = 'Reset Password — NiRu-Furnitures';
$extraJs = [SITE_URL . '/assets/js/validation.js'];

include 'includes/head.php';
include 'includes/navbar.php';
?>

<div class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 200px); background: var(--clr-light);">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4">
                <div class="card shadow-sm border-0 rounded-4 p-4 p-md-5">
                    
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock display-4 text-primary mb-2 d-inline-block"></i>
                        <h2 class="fw-bold h3">Create New Password</h2>
                        <p class="text-muted small">Please enter your new password below.</p>
                    </div>

                    <?= renderFlash() ?>

                    <form method="POST" action="reset-password.php?token=<?= urlencode($token) ?>" class="needs-validation" novalidate>
                        
                        <div class="mb-3">
                            <label for="passwordInput" class="form-label fw-medium">New Password <span class="text-danger">*</span></label>
                            <div class="input-icon-wrap position-relative mb-2">
                                <i class="bi bi-lock"></i>
                                <input type="password" class="form-control pe-5" id="passwordInput" name="password" required minlength="8" autofocus>
                                <button type="button" class="btn btn-link p-0 position-absolute end-0 top-50 translate-middle-y me-3 text-muted" 
                                        data-toggle-password="passwordInput" aria-label="Show password">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <div class="invalid-feedback">Password must be at least 8 characters.</div>
                            </div>
                            
                            <!-- Password Strength -->
                            <div class="progress mt-2" style="height: 6px;">
                                <div id="passwordStrengthBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div id="passwordStrengthText" class="small mt-1 text-end text-muted" style="min-height: 18px;"></div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="confirmPasswordInput" class="form-label fw-medium">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="input-icon-wrap position-relative">
                                <i class="bi bi-shield-lock"></i>
                                <input type="password" class="form-control pe-5" id="confirmPasswordInput" required>
                                <div class="invalid-feedback">Passwords do not match.</div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg mb-3">Reset Password</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
