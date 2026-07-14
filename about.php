<?php
/**
 * NiRu-Furnitures — About Us
 */
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$pageTitle = 'About Us — NiRu-Furnitures';
$pageDescription = 'Learn about NiRu-Furnitures — our story, mission and the team behind your favourite furniture brand.';

include 'includes/head.php';
include 'includes/navbar.php';
?>

<!-- Hero -->
<section class="page-hero" aria-label="About Us">
    <div class="container-xl" data-aos="fade-up">
        <span class="hero-label">Our Story</span>
        <h1>Crafting Comfort Since 2018</h1>
        <p>We believe your home deserves the very best — beautifully crafted, thoughtfully designed.</p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">About Us</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Story Section -->
<section class="page-section" style="background:#fff">
    <div class="container-xl">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <span class="section-label">Who We Are</span>
                <h2 class="section-title">A Passion for Beautiful Furniture</h2>
                <div class="section-divider"></div>
                <p style="color:#776965;line-height:1.75;margin-bottom:1rem">NiRu-Furnitures was founded in 2018 with a
                    simple vision: make premium quality furniture accessible to every Sri Lankan family. What started as
                    a small showroom in Colombo has grown into a nationwide brand trusted by thousands of homes and
                    offices.</p>
                <p style="color:#776965;line-height:1.75;margin-bottom:2rem">Every piece in our collection is selected
                    or crafted with intention — sourced from sustainable suppliers, tested for durability, and designed
                    to stand the test of time.</p>

                <div class="row g-3">
                    <?php
                    $stats = [['7+', 'Years in Business'], ['12,000+', 'Happy Customers'], ['500+', 'Products'], ['6', 'Categories']];
                    foreach ($stats as $s):
                        ?>
                        <div class="col-6">
                            <div class="stat-highlight">
                                <div class="stat-num"><?= $s[0] ?></div>
                                <div class="stat-lbl"><?= $s[1] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div style="border-radius:20px;overflow:hidden;aspect-ratio:4/3;background:#f4ede6">
                    <img src="https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=800&q=85"
                        alt="NiRu-Furnitures Showroom" class="w-100 h-100" style="object-fit:cover">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values / Mission -->
<section class="page-section page-section-alt" id="sustainability">
    <div class="container-xl">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-label">Our Purpose</span>
            <h2 class="section-title">Mission &amp; Values</h2>
            <div class="section-divider section-divider-center"></div>
        </div>
        <div class="row g-4">
            <?php
            $values = [
                ['bi-bullseye', 'Our Mission', 'To make premium, sustainable furniture accessible to every home in Sri Lanka through quality craftsmanship and exceptional service.'],
                ['bi-eye', 'Our Vision', 'To be the most trusted furniture brand in South Asia, where every customer leaves with a piece they\'re proud to call their own.'],
                ['bi-leaf', 'Sustainability', 'We source materials responsibly, minimise waste in production and partner with suppliers who share our commitment to the environment.'],
                ['bi-people-fill', 'Our Team', 'Our 80+ team members are passionate about furniture and customer happiness. From designers to delivery crews — every detail matters.'],
            ];
            foreach ($values as $i => $v):
                ?>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="<?= $i * 80 ?>">
                    <div class="feature-card">
                        <div class="feature-card-icon"><i class="bi <?= $v[0] ?>"></i></div>
                        <h5><?= $v[1] ?></h5>
                        <p><?= $v[2] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Team -->
<section class="page-section" style="background:#fff">
    <div class="container-xl">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-label">The People</span>
            <h2 class="section-title">Meet Our Team</h2>
            <div class="section-divider section-divider-center"></div>
        </div>
        <div class="row g-4 justify-content-center">
            <?php
            $team = [
                ['Nirasha Perera', 'Co-Founder & CEO'],
                ['Ruwan Jayasinghe', 'Co-Founder & Head of Design'],
                ['Dilumi Wickrama', 'Operations Manager'],
                ['Sahan Fernando', 'Head of Customer Service'],
            ];
            foreach ($team as $i => $member):
                ?>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="<?= $i * 80 ?>">
                    <div class="team-card">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($member[0]) ?>&size=96&background=c97b30&color=fff&rounded=true"
                            alt="<?= e($member[0]) ?>" class="rounded-circle" width="88" height="88">
                        <h6><?= e($member[0]) ?></h6>
                        <small><?= e($member[1]) ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Band -->
<section class="cta-band" aria-label="Call to action">
    <div class="container-xl" data-aos="fade-up">
        <h2>Ready to Transform Your Space?</h2>
        <p>Visit our showroom or explore our full collection online.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="shop.php" class="btn-cta-primary"><i class="bi bi-bag"></i> Shop Now</a>
            <a href="contact.php" class="btn-cta-outline"><i class="bi bi-envelope"></i> Contact Us</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>