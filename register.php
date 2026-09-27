<?php
$page_title = "NiRu Furnitures - Create Account";
include 'header.php';
?>

  <main style="padding-top: 120px; padding-bottom: 80px;">
    <div class="container-xl">

      <div class="auth-card">
        <div class="text-center mb-4">
          <h1 class="fs-2 fw-semibold mb-2" style="color: var(--niru-primary);">Create Account</h1>
          <p class="small text-muted mb-0">Join the NiRu community for a curated living experience.</p>
        </div>

        <form class="d-flex flex-column gap-3 mb-4">

          <!-- Name Row -->
          <div class="row">
            <div class="col-md-6 mb-3 mb-md-0">
              <label for="fname" class="form-label-custom">First Name</label>
              <div class="input-icon-group">
                <i class="bi bi-person"></i>
                <input type="text" class="form-control" id="fname" name="fname" placeholder="Nisala" required>
              </div>
            </div>

            <div class="col-md-6">
              <label for="lname" class="form-label-custom">Last Name</label>
              <div class="input-icon-group">
                <i class="bi bi-person"></i>
                <input type="text" class="form-control" id="lname" name="lname" placeholder="Perera" required>
              </div>
            </div>
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="form-label-custom">Email Address</label>
            <div class="input-icon-group">
              <i class="bi bi-envelope"></i>
              <input type="email" class="form-control" id="email" name="email" placeholder="nisalaa@gmail.com" required>
            </div>
          </div>

            <!-- Phone Number -->
          <div>
            <label for="number" class="form-label-custom">Phone Number</label>
            <div class="input-icon-group">
              <i class="bi bi-phone"></i>
              <input type="text" class="form-control" id="number" name="number" placeholder="07xxxxxxxx" required>
            </div>
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="form-label-custom">Password</label>
            <div class="input-icon-group">
              <i class="bi bi-lock"></i>
              <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
            </div>
          </div>

          <!-- Confirm Password -->
          <div>
            <label for="cpassword" class="form-label-custom">Confirm Password</label>
            <div class="input-icon-group">
              <i class="bi bi-shield-lock"></i>
              <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="••••••••" required>
            </div>
          </div>

          <!-- Terms Checkbox -->
          <div class="form-check my-2">
            <input class="form-check-input" type="checkbox" id="termsCheck" required>
            <label class="form-check-label small" for="termsCheck" style="color: var(--niru-body-text);">
              I agree to the <a href="#" class="fw-semibold text-decoration-none" style="color: var(--niru-primary);">Terms of Service</a> and <a href="#" class="fw-semibold text-decoration-none" style="color: var(--niru-primary);">Privacy Policy</a>.
            </label>
          </div>

          <!-- Submit Button -->
          <button type="button" onclick="signup();" class="btn-auth-primary">
            Create Account <i class="bi bi-arrow-right"></i>
          </button>
        </form>

        <div id="msgdiv" class="d-none">
          <div id="msg" role="alert"></div>
        </div>


        <div class="text-center pt-2">
          <p class="small mb-0" style="color: var(--niru-body-text);">
            Already have an account? <a href="login.php" class="fw-semibold text-decoration-none" style="color: var(--niru-primary);">Log in</a>
          </p>
        </div>

      </div>

    </div>
  </main>

<?php include 'footer.php'; ?>