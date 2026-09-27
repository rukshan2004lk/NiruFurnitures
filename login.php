<?php
$page_title = "NiRu Furnitures - Welcome Back";
include 'header.php';

$cookie_email = $_COOKIE["email"] ?? "";
$cookie_password = $_COOKIE["password"] ?? "";
$remember_checked = (!empty($cookie_email) && !empty($cookie_password)) ? "checked" : "";
?>

  <main style="padding-top: 120px; padding-bottom: 80px;">
    <div class="container-xl">

      <div class="auth-card">
        <div class="text-center mb-4">
          <h1 class="fs-2 fw-semibold mb-2" style="color: var(--niru-primary);">Welcome Back</h1>
          <p class="small text-muted mb-0">Sign in to manage your orders and saved items.</p>
        </div>

        <form onsubmit="event.preventDefault(); signIn();" class="d-flex flex-column gap-3 mb-4">

          <div>
            <label for="email" class="form-label-custom">Email</label>
            <div class="input-icon-group">
              <i class="bi bi-envelope"></i>
              <input type="email" class="form-control" id="email" placeholder="Enter your Email" value="<?php echo htmlspecialchars($cookie_email); ?>" required>
            </div>
          </div>

          <div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="password" class="form-label-custom mb-0">Password</label>
            </div>
            <div class="input-icon-group">
              <i class="bi bi-lock"></i>
              <input type="password" class="form-control" id="password" placeholder="Enter your Password" value="<?php echo htmlspecialchars($cookie_password); ?>" required>
            </div>
          </div>

          <div class="form-check my-2">
            <input class="form-check-input" type="checkbox" id="rememberMe" <?php echo $remember_checked; ?>>
            <label class="form-check-label small" for="rememberMe" style="color: var(--niru-body-text);">
              Remember me for 30 days
            </label>
          </div>

          <button type="button" onclick="signIn();" class="btn-auth-primary py-3">
            Sign In to Customer Dashboard <i class="bi bi-arrow-right ms-2"></i>
          </button>
        </form>
        
        <div id="msgdiv" class="d-none">
          <div id="msg" role="alert"></div>
        </div>

        <div class="text-center mb-3">
          <button type="button" onclick="adminSignIn();" class="btn btn-outline-dark w-100 py-2 rounded-3 small">
            <i class="bi bi-shield-lock me-2"></i>Sign In to Admin Panel
          </button>
        </div>

        <div class="text-center pt-2">
          <p class="small mb-0" style="color: var(--niru-body-text);">
            Don't have an account? <a href="register.php" class="fw-semibold text-decoration-none" style="color: var(--niru-primary);">Create one</a>
          </p>
        </div>

      </div>

    </div>
  </main>

<?php include 'footer.php'; ?>