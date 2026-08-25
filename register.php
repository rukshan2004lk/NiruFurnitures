<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NiRu Furnitures - Create Account</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <link rel="stylesheet" href="assets/css/style.css">

  <!-- Google Identity Services -->
  <script src="https://accounts.google.com/gsi/client" async defer></script>

  <!-- Apple Sign-In SDK (Real) -->
  <script type="text/javascript" src="https://appleid.cdn-apple.com/appleauth/static/jsapi/appleid/1/en_US/auth.js"></script>


</head>

<body>

  <header>
    <nav class="navbar navbar-expand-lg fixed-top px-3 px-lg-5">
      <div class="container-fluid max-w-1320">
        <a class="brand-logo me-4" href="index.html">NiRu</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
          <ul class="navbar-nav mx-auto text-center mb-2 mb-lg-0 gap-lg-4">
            <li class="nav-item"><a class="nav-link-custom" href="index.html">Home</a></li>
            <li class="nav-item"><a class="nav-link-custom" href="shop.html">Shop</a></li>
            <li class="nav-item"><a class="nav-link-custom" href="about.html">About Us</a></li>
            <li class="nav-item"><a class="nav-link-custom" href="contact.html">Contact</a></li>
            <li class="nav-item"><a class="nav-link-custom" href="faq.html">FAQ</a></li>
          </ul>

          <div class="d-flex align-items-center gap-3">
            <div class="search-input-wrapper">
              <i class="bi bi-search"></i>
              <input type="text" class="form-control" placeholder="Search furniture...">
            </div>

            <a href="cart.html" class="icon-btn text-decoration-none position-relative" aria-label="Cart">
              <i class="bi bi-bag"></i>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">2</span>
            </a>

            <div class="dropdown">
              <button class="icon-btn dropdown-toggle border-0 bg-transparent p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account">
                <i class="bi bi-person"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3">
                <li><a class="dropdown-item py-2" href="user/dashboard.html"><i class="bi bi-speedometer2 me-2"></i>My Dashboard</a></li>
                <li><a class="dropdown-item py-2" href="user/orders.html"><i class="bi bi-box-seam me-2"></i>My Orders</a></li>
                <li><a class="dropdown-item py-2" href="user/wishlist.html"><i class="bi bi-heart me-2"></i>Wishlist</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item py-2" href="admin/admin-dashboard.html"><i class="bi bi-shield-lock me-2"></i>Admin Panel</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item py-2 text-danger" href="login.html"><i class="bi bi-box-arrow-right me-2"></i>Sign In / Register</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </nav>
  </header>

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

  <footer>
    <div class="container-xl">
      <div class="row g-4">

        <div class="col-12 col-lg-4">
          <h3 class="fs-4 fw-bold mb-3" style="color: var(--niru-primary);">NiRu</h3>
          <p class="small mb-0" style="color: var(--niru-body-text);">
            © 2024 NiRu Furnitures. Crafted for Comfort.
          </p>
        </div>

        <div class="col-6 col-md-4 col-lg-4">
          <div class="footer-heading">COMPANY</div>
          <ul class="footer-links">
            <li><a href="#">About Us</a></li>
            <li><a href="#">Sustainability</a></li>
          </ul>
        </div>

        <div class="col-6 col-md-4 col-lg-4">
          <div class="footer-heading">SUPPORT</div>
          <ul class="footer-links">
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Shipping Info</a></li>
          </ul>
        </div>
      </div>
    </div>
  </footer>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/script.js"></script>
</body>

</html>