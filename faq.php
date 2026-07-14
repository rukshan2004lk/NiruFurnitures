<?php
/**
 * NiRu-Furnitures — FAQ
 */
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$pageTitle = 'Frequently Asked Questions — NiRu-Furnitures';
$pageDescription = 'Find answers to common questions about delivery, returns, warranties, payment and assembly.';

$faqs = [
    'Ordering & Payment' => [
        ['How do I place an order?', 'Browse our shop, add items to your cart, and proceed to checkout. You can pay via Cash on Delivery, Bank Transfer or Credit/Debit Card.'],
        ['Do you offer EMI / installment plans?', 'Yes, we partner with several banks to offer 0%-interest installment plans on orders above Rs. 50,000. Contact us for details.'],
        ['Can I modify or cancel my order?', 'Orders can be modified or cancelled within 24 hours of placement. After that, the order enters production/dispatch and cancellation may not be possible.'],
        ['Is online payment secure?', 'Absolutely. All transactions are encrypted using industry-standard SSL technology.'],
    ],
    'Delivery & Shipping' => [
        ['How long does delivery take?', 'Standard delivery takes 5–10 working days. Express delivery (2–3 days) is available in Colombo and surrounding areas for an additional fee.'],
        ['Do you deliver island-wide?', 'Yes! We deliver to all 25 districts in Sri Lanka. Delivery fees vary by location.'],
        ['What is the free delivery threshold?', 'Orders above Rs. ' . number_format((float) getSetting('free_shipping_above', 15000)) . ' qualify for free standard delivery.'],
        ['Do you assemble furniture on delivery?', 'Yes. Our trained team will deliver and fully assemble your furniture at no extra charge within the Western Province. Assembly charges apply for other provinces.'],
    ],
    'Returns & Warranty' => [
        ['What is your return policy?', 'We offer a 14-day return policy. Items must be unused, in original packaging, and undamaged. Custom-made items are non-returnable.'],
        ['How do I initiate a return?', 'Contact our support team via the Contact page or call us. We\'ll arrange a pickup at no additional cost for valid warranty claims.'],
        ['What warranty do you offer?', 'All products carry a 2-year warranty against manufacturing defects. Warranty does not cover normal wear and tear or damage from misuse.'],
    ],
    'Products & Customisation' => [
        ['Can I request custom dimensions?', 'Yes! We offer size customisation on select sofa, bed and wardrobe ranges. Contact us with your requirements and we\'ll provide a quote within 48 hours.'],
        ['Are the product colours accurate?', 'We strive for colour accuracy, but monitor calibrations may cause slight variations. You can visit our showroom to view colours in person.'],
        ['Can I get fabric/material samples?', 'Yes, we offer free fabric and finish samples for sofa and bed upholstery. Request them via our Contact page.'],
    ],
];

include 'includes/head.php';
include 'includes/navbar.php';
?>

<style>

    .faq-sidebar-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #ded8d1;
        box-shadow: 0 2px 8px rgba(74, 44, 36, .05);
        padding: 1.5rem;
        position: sticky;
        top: 80px;
    }

    .faq-sidebar-card h6 {
        font-size: .7rem;
        font-weight: 800;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: #9b8076;
        margin-bottom: 1rem;
    }

    .faq-cat-link {
        display: block;
        padding: .5rem .6rem;
        border-radius: 8px;
        font-size: .875rem;
        font-weight: 500;
        color: #776965;
        text-decoration: none;
        transition: all 180ms;
        margin-bottom: 2px;
    }

    .faq-cat-link:hover,
    .faq-cat-link.active {
        background: #f4ede6;
        color: #53362e;
        font-weight: 600;
    }

    .faq-help-btn {
        display: block;
        text-align: center;
        background: #53362e;
        border-radius: 9px;
        color: #fff;
        font-size: .82rem;
        font-weight: 700;
        padding: .65rem;
        text-decoration: none;
        margin-top: 1rem;
        transition: background 200ms;
    }

    .faq-help-btn:hover {
        background: #3d2219;
    }

    .faq-section-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #f4ede6;
        color: #7d4f3f;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .faq-section-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: #3d2219;
    }

    .accordion-item {
        border: 1px solid #ded8d1 !important;
        border-radius: 12px !important;
        overflow: hidden;
        margin-bottom: .65rem;
        background: #fff;
    }

    .accordion-button {
        font-weight: 600;
        font-size: .895rem;
        color: #3d2219;
        background: #fff;
        border-radius: 12px !important;
        padding: 1.1rem 1.25rem;
    }

    .accordion-button:not(.collapsed) {
        color: #53362e;
        background: #fdf8f5;
        box-shadow: none;
    }

    .accordion-button::after {
        filter: sepia(1) saturate(2) hue-rotate(-20deg);
    }

    .accordion-body {
        font-size: .875rem;
        color: #776965;
        line-height: 1.7;
        padding: 0 1.25rem 1.1rem;
        background: #fdf8f5;
    }

    .faq-cta-card {
        background: #f4ede6;
        border-radius: 18px;
        border: 1px solid #ddc9b8;
        padding: 3rem;
        text-align: center;
    }

    .faq-cta-card h4 {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        color: #3d2219;
        margin-bottom: .6rem;
    }

    .faq-cta-card p {
        color: #776965;
        font-size: .9rem;
        margin-bottom: 1.75rem;
    }

    .btn-faq-primary {
        background: #53362e;
        border: none;
        border-radius: 9px;
        color: #fff;
        font-weight: 700;
        font-size: .88rem;
        padding: .7rem 1.75rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        transition: background 200ms;
    }

    .btn-faq-outline {
        background: transparent;
        border: 2px solid #b89880;
        border-radius: 9px;
        color: #53362e;
        font-weight: 700;
        font-size: .88rem;
        padding: .67rem 1.75rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        transition: all 200ms;
    }

    .btn-faq-primary:hover {
        background: #3d2219;
    }

    .btn-faq-outline:hover {
        background: #53362e;
        border-color: #53362e;
        color: #fff;
    }
</style>

<!-- Hero -->
<section class="page-hero">
    <div class="container-xl" data-aos="fade-up">
        <span class="hero-label">Help Centre</span>
        <h1>Frequently Asked Questions</h1>
        <p>Find quick answers to the most common questions below.</p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="page-breadcrumb">
    <div class="container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">FAQ</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Content -->
<section class="page-section page-section-alt">
    <div class="container-xl">
        <div class="row g-5">

            <!-- Sidebar -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="faq-sidebar-card">
                    <h6>Categories</h6>
                    <?php $catIdx = 0;
                    foreach ($faqs as $catName => $qas): ?>
                        <a class="faq-cat-link <?= $catIdx === 0 ? 'active' : '' ?>"
                            href="#faqCat_<?= $catIdx ?>"><?= e($catName) ?></a>
                        <?php $catIdx++; endforeach; ?>
                    <hr style="border-color:#ded8d1">
                    <a href="contact.php" class="faq-help-btn">
                        <i class="bi bi-chat-dots me-1"></i>Still need help?
                    </a>
                </div>
            </div>

            <!-- Accordions -->
            <div class="col-lg-9">
                <?php $catIdx = 0;
                foreach ($faqs as $catName => $qas): ?>
                    <div id="faqCat_<?= $catIdx ?>" class="mb-5" data-aos="fade-up" data-aos-delay="<?= $catIdx * 60 ?>">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="faq-section-icon"><i class="bi bi-question-circle"></i></div>
                            <h3 class="faq-section-title mb-0"><?= e($catName) ?></h3>
                        </div>

                        <div class="accordion" id="accordion_<?= $catIdx ?>">
                            <?php foreach ($qas as $qaIdx => [$q, $a]): ?>
                                <div class="accordion-item">
                                    <h4 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#faq_<?= $catIdx ?>_<?= $qaIdx ?>" aria-expanded="false">
                                            <?= e($q) ?>
                                        </button>
                                    </h4>
                                    <div id="faq_<?= $catIdx ?>_<?= $qaIdx ?>" class="accordion-collapse collapse"
                                        data-bs-parent="#accordion_<?= $catIdx ?>">
                                        <div class="accordion-body"><?= e($a) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php $catIdx++; endforeach; ?>

                <!-- Bottom CTA -->
                <div class="faq-cta-card" data-aos="fade-up">
                    <i class="bi bi-headset" style="font-size:3rem;color:#9b6b55;display:block;margin-bottom:1rem"></i>
                    <h4>Didn't find your answer?</h4>
                    <p>Our friendly support team is ready to help you out.</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="contact.php" class="btn-faq-primary"><i class="bi bi-envelope"></i>Send a Message</a>
                        <a href="tel:<?= e(getSetting('site_phone', '+94770000000')) ?>" class="btn-faq-outline"><i
                                class="bi bi-telephone"></i>Call Us</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>