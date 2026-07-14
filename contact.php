<?php
/**
 * NiRu-Furnitures — Contact Page
 */
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$pageTitle = 'Contact Us — NiRu-Furnitures';
$pageDescription = 'Get in touch with the NiRu-Furnitures team — we\'d love to hear from you.';

include 'includes/head.php';
include 'includes/navbar.php';
?>

<style>
    /* Contact-specific styles that aren't shared */
    .contact-info-item {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        padding: 1.25rem;
        background: #fff;
        border-radius: 14px;
        border: 1px solid #ded8d1;
        box-shadow: 0 2px 8px rgba(74, 44, 36, .05);
        margin-bottom: 1rem;
        text-decoration: none;
        transition: box-shadow 200ms, transform 200ms;
    }
    .contact-info-item:hover {
        box-shadow: 0 6px 20px rgba(74, 44, 36, .1);
        transform: translateY(-2px);
    }
    .contact-info-icon {
        width: 46px; height: 46px;
        border-radius: 12px;
        background: #f4ede6; color: #7d4f3f;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; flex-shrink: 0;
    }
    .contact-info-label {
        font-size: .72rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .07em;
        color: #9b8076; margin-bottom: .2rem;
    }
    .contact-info-value { font-size: .9rem; font-weight: 600; color: #3d2219; }

    .contact-form-card {
        background: #fff; border-radius: 18px;
        border: 1px solid #ded8d1;
        box-shadow: 0 4px 24px rgba(74, 44, 36, .07);
        padding: 2.5rem 2.75rem;
    }
    .contact-form-card .form-control,
    .contact-form-card .form-select {
        border-radius: 9px; border: 1px solid #ddd5cc;
        background: #faf8f6; font-size: .875rem; color: #3d2219;
        padding: .65rem 1rem .65rem 2.6rem;
    }
    .contact-form-card textarea.form-control { padding-left: 1rem; }
    .contact-form-card .form-control:focus {
        border-color: #9b6b55;
        box-shadow: 0 0 0 3px rgba(155, 107, 85, .15);
        background: #fff;
    }
    .contact-form-card .form-label { font-size: .84rem; font-weight: 600; color: #4a3028; }
    .contact-form-card h2 {
        font-family: 'Poppins', sans-serif; font-size: 1.4rem;
        font-weight: 700; color: #3d2219; margin-bottom: 1.5rem;
    }

    .contact-submit-btn {
        background: #53362e; border: none; border-radius: 9px;
        color: #fff; font-weight: 700; font-size: .9rem;
        padding: .75rem 2.5rem; transition: background 200ms;
        display: inline-flex; align-items: center; gap: .5rem;
    }
    .contact-submit-btn:hover { background: #3d2219; }

    .contact-social-link {
        width: 40px; height: 40px; border-radius: 50%;
        border: 1px solid #ddd5cc; background: #faf8f6;
        color: #7d4f3f; display: inline-flex;
        align-items: center; justify-content: center;
        text-decoration: none; font-size: 1rem; transition: all 200ms;
    }
    .contact-social-link:hover {
        background: #53362e; border-color: #53362e; color: #fff;
    }

    .map-card {
        background: #f4ede6; border-radius: 18px;
        border: 1px solid #ded8d1; overflow: hidden;
        min-height: 240px; display: flex;
        align-items: center; justify-content: center;
        text-align: center; padding: 3rem;
    }
</style>

<!-- Hero -->
<section class="page-hero" aria-label="Contact Us">
    <div class="container-xl" data-aos="fade-up">
        <span class="hero-label">We're Here for You</span>
        <h1>Get in Touch</h1>
        <p>Have a question or need help? We'd love to hear from you.</p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Contact</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Content -->
<section class="page-section page-section-alt">
    <div class="container-xl">
        <?= renderFlash() ?>

        <div class="row g-5">

            <!-- Left: Info -->
            <div class="col-lg-4" data-aos="fade-right">
                <h2 style="font-family:'Poppins',sans-serif;font-size:1.5rem;font-weight:700;color:#3d2219;margin-bottom:1.75rem">Contact Details</h2>

                <?php
                $contactInfo = [
                    ['bi-geo-alt-fill', 'Visit Our Showroom', getSetting('site_address', '42 Furniture Street, Colombo 03, Sri Lanka'), null],
                    ['bi-telephone-fill', 'Call Us', getSetting('site_phone', '+94 77 000 0000'), 'tel:' . getSetting('site_phone')],
                    ['bi-envelope-fill', 'Email Us', getSetting('site_email', 'info@nirufurnitures.com'), 'mailto:' . getSetting('site_email')],
                    ['bi-clock-fill', 'Business Hours', 'Mon – Sat: 9:00 AM – 6:00 PM<br>Sunday: Closed', null],
                ];
                foreach ($contactInfo as $info):
                    $tag = $info[3] ? 'a' : 'div';
                    $attr = $info[3] ? 'href="' . $info[3] . '"' : '';
                    ?>
                    <<?= $tag ?> class="contact-info-item" <?= $attr ?>>
                        <div class="contact-info-icon"><i class="bi <?= $info[0] ?>"></i></div>
                        <div>
                            <div class="contact-info-label"><?= $info[1] ?></div>
                            <div class="contact-info-value"><?= $info[2] ?></div>
                        </div>
                    </<?= $tag ?>>
                <?php endforeach; ?>

                <div class="mt-4">
                    <p style="font-size:.8rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#9b8076;margin-bottom:.85rem">Follow Us</p>
                    <div class="d-flex gap-2">
                        <?php if ($fb = getSetting('facebook_url')): ?>
                            <a href="<?= e($fb) ?>" class="contact-social-link" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <?php endif; ?>
                        <?php if ($ig = getSetting('instagram_url')): ?>
                            <a href="<?= e($ig) ?>" class="contact-social-link" target="_blank" rel="noopener" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <?php endif; ?>
                        <a href="#" class="contact-social-link" aria-label="Pinterest"><i class="bi bi-pinterest"></i></a>
                    </div>
                </div>
            </div>

            <!-- Right: Form -->
            <div class="col-lg-8" data-aos="fade-left">
                <div class="contact-form-card">
                    <h2>Send a Message</h2>
                    <form method="POST" action="api/messages.php" class="needs-validation" novalidate id="contactForm">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="send_message">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="contactName" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-person" style="color:#9b6b55"></i>
                                    <input type="text" id="contactName" name="name" class="form-control"
                                        placeholder="Your full name"
                                        value="<?= isLoggedIn() ? e($_SESSION['user_name'] ?? '') : '' ?>" required
                                        minlength="2" maxlength="120">
                                    <div class="invalid-feedback">Please enter your name.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="contactEmail" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-envelope" style="color:#9b6b55"></i>
                                    <input type="email" id="contactEmail" name="email" class="form-control"
                                        placeholder="you@example.com"
                                        value="<?= isLoggedIn() ? e($_SESSION['user_email'] ?? '') : '' ?>" required>
                                    <div class="invalid-feedback">Please enter a valid email.</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="contactSubject" class="form-label">Subject <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="bi bi-chat-left-text" style="color:#9b6b55"></i>
                                    <input type="text" id="contactSubject" name="subject" class="form-control"
                                        placeholder="How can we help you?" required minlength="5" maxlength="200">
                                    <div class="invalid-feedback">Please enter a subject.</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="contactMessage" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea id="contactMessage" name="message" class="form-control" rows="6"
                                    maxlength="2000" placeholder="Write your message here…" style="padding-left:1rem"
                                    required minlength="20"></textarea>
                                <div class="char-counter text-muted small mt-1"></div>
                                <div class="invalid-feedback">Message must be at least 20 characters.</div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="contact-submit-btn" id="contactSubmit">
                                    <i class="bi bi-send"></i> Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Map -->
        <div class="map-card mt-5" data-aos="fade-up">
            <div>
                <i class="bi bi-geo-alt-fill" style="font-size:3rem;color:#9b6b55;display:block;margin-bottom:1rem"></i>
                <p style="font-weight:600;color:#3d2219;margin-bottom:1rem">42 Furniture Street, Colombo 03, Sri Lanka</p>
                <a href="https://maps.google.com" target="_blank" class="contact-submit-btn" style="text-decoration:none">
                    <i class="bi bi-map"></i> Open in Google Maps
                </a>
            </div>
        </div>

    </div>
</section>

<?php
$extraJs = [SITE_URL . '/assets/js/validation.js'];
include 'includes/footer.php';
?>