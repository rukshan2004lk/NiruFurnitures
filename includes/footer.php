<?php
$extraJs = $extraJs ?? [];
?>

<footer class="site-footer">
    <div class="footer-newsletter">
        <div class="container-xl">
            <div class="row align-items-center gy-3">
                <div class="col-lg-6" data-aos="fade-right">
                    <h4 class="fw-bold mb-1">Stay Inspired</h4>
                    <p class="mb-0 opacity-75">Join our community and receive exclusive updates on new arrivals,
                        sustainable design tips, and private sale access.</p>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <form class="newsletter-form d-flex" action="#" method="POST">
                        <?= csrfField() ?>
                        <input type="email" name="email" class="form-control" placeholder="Your email address" required>
                        <button type="submit" class="btn btn-primary px-4 text-nowrap">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-body">
        <div class="container-xl">
            <div class="row gy-5">
                <div class="col-lg-3 col-md-6">
                    <a href="<?= SITE_URL ?>" class="footer-brand d-inline-flex align-items-center mb-3">
                        <span class="fw-bold fs-4">NiRu</span>
                    </a>
                    <p class="footer-desc">Crafting modern legacies through timeless furniture and sustainable
                        materials.</p>
                    <div class="social-links mt-4">
                        <a href="#" class="social-link" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-link" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-link" aria-label="Email"><i class="bi bi-envelope"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-6">
                    <h6 class="footer-heading">Company</h6>
                    <ul class="footer-links">
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="about.php#sustainability">Sustainability</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 col-6">
                    <h6 class="footer-heading">Support</h6>
                    <ul class="footer-links">
                        <li><a href="#">Shipping Info</a></li>
                        <li><a href="#">Returns &amp; Exchanges</a></li>
                        <li><a href="#">Care Guides</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-heading">Visit Our Showroom</h6>
                    <p class="footer-desc mb-4">124 Design District St.<br>Stockholm, SE 111 22</p>
                    <a href="contact.php" class="footer-directions">Get Directions <i
                            class="bi bi-arrow-up-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container-xl d-flex justify-content-center align-items-center">
            <p class="mb-0 small">&copy; 2024 NiRu Furnitures. Crafted for Comfort.</p>
        </div>
    </div>
</footer>

<?php include_once __DIR__ . '/cart_panel.php'; ?>

<button id="backToTop" class="back-to-top" aria-label="Back to top">
    <i class="bi bi-chevron-up"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmMpZJKpABiyY8PGpaDV+VhE0pN/"
    crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    window.APP_CSRF = "<?= e(csrfToken()) ?>";
    window.API_BASE = "<?= SITE_URL ?>/api/products.php";
    window.USER_WISHLIST = <?= json_encode(getUserWishlistIds()) ?>;
</script>
<script src="<?= SITE_URL ?>/assets/js/app.js"></script>
<script src="<?= SITE_URL ?>/assets/js/cart_panel.js"></script>
<script src="<?= SITE_URL ?>/assets/js/navbar.js"></script>
<script src="<?= SITE_URL ?>/assets/js/search.js"></script>

<?php foreach ($extraJs as $js): ?>
    <script src="<?= e($js) ?>"></script>
<?php endforeach; ?>

</body>

</html>