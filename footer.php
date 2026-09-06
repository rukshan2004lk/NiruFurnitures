<?php
if (!isset($base_path)) {
    $base_path = file_exists('assets/css/style.css') ? '' : '../';
}
?>
  <footer>
    <div class="container-xl">
      <div class="row g-4">

        <div class="col-12 col-lg-4">
          <h3 class="fs-4 fw-bold mb-3" style="color: var(--niru-primary);">NiRu</h3>
          <p class="small mb-0" style="color: var(--niru-body-text);">
            © <?php echo date('Y'); ?> NiRu Furnitures. Crafted for Comfort.
          </p>
        </div>

        <div class="col-6 col-md-4 col-lg-4">
          <div class="footer-heading">COMPANY</div>
          <ul class="footer-links">
            <li><a href="<?php echo $base_path; ?>about.php">About Us</a></li>
            <li><a href="<?php echo $base_path; ?>shop.php">Our Collections</a></li>
            <li><a href="<?php echo $base_path; ?>faq.php">FAQ</a></li>
          </ul>
        </div>

        <div class="col-6 col-md-4 col-lg-4">
          <div class="footer-heading">SUPPORT</div>
          <ul class="footer-links">
            <li><a href="<?php echo $base_path; ?>contact.php">Contact Us</a></li>
            <li><a href="<?php echo $base_path; ?>faq.php">Shipping Info</a></li>
            <li><a href="<?php echo $base_path; ?>faq.php">Privacy Policy</a></li>
          </ul>
        </div>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="<?php echo $base_path; ?>assets/js/script.js"></script>
</body>
</html>
