<?php
/**
 * NiRu-Furnitures — Forgot Password (forgot-password.php)
 */
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlash('Please enter a valid email address.', 'danger');
    } else {
        $user = dbFetchOne("SELECT id, name FROM users WHERE email = ? LIMIT 1", 's', $email);
        
        if ($user) {
            // Generate token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            dbExecute("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?", 'ssi', $token, $expires, $user['id']);
            
            // In a real app, send an email here.
            // For demo purposes, we will just show the link in the flash message.
            $resetLink = SITE_URL . "/reset-password.php?token=" . $token;
            
            setFlash("If that email is in our database, we have sent a password reset link. <br><br><strong>(Demo mode link: <a href='$resetLink'>$resetLink</a>)</strong>", 'success');
        } else {
            // To prevent email enumeration, show the same success message
            setFlash("If that email is in our database, we have sent a password reset link.", 'success');
        }
    }
}

$pageTitle = 'Forgot Password — NiRu-Furnitures';
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
                        <i class="bi bi-key display-4 text-primary mb-2 d-inline-block"></i>
                        <h2 class="fw-bold h3">Reset Password</h2>
                        <p class="text-muted small">Enter your email and we'll send you a link to reset your password.</p>
                    </div>

                    <?= renderFlash() ?>

                    <form method="POST" action="forgot-password.php" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="email" class="form-label fw-medium">Email Address</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-envelope"></i>
                                <input type="email" class="form-control" id="email" name="email" required autofocus>
                                <div class="invalid-feedback">Please enter a valid email.</div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg mb-3">Send Reset Link</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="login.php" class="text-muted small text-decoration-none fw-medium">
                            <i class="bi bi-arrow-left me-1"></i>Back to Login
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
