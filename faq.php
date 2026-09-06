<?php
$page_title = "NiRu Furnitures - Frequently Asked Questions";
include 'header.php';
?>

  <main style="padding-top: 100px; padding-bottom: 80px;">
    <div class="container-xl" style="max-width: 900px;">
      
      <div class="text-center mb-5">
        <h1 class="display-5 fw-bold mb-3" style="color: var(--niru-primary);">Frequently Asked Questions</h1>
        <p class="fs-5 text-muted">Everything you need to know about our products, white-glove shipping, and 10-year warranty.</p>
      </div>

      <div class="card border-0 shadow-sm p-3 mb-5 rounded-4" style="background-color: var(--niru-bg-alt);">
        <div class="input-group">
          <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-muted fs-5"></i></span>
          <input type="text" class="form-control border-0 py-3" placeholder="Search questions (e.g., shipping time, returns, fabric care)...">
        </div>
      </div>

      <div class="accordion accordion-flush shadow-sm rounded-4 overflow-hidden border mb-5" id="faqAccordion">
        
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingOne">
            <button class="accordion-button fw-bold fs-6 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
              <i class="bi bi-truck text-warning me-3 fs-5"></i> What are your shipping times and delivery options?
            </button>
          </h2>
          <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
            <div class="accordion-body text-muted lh-lg">
              Standard in-stock furniture items ship within 3–5 business days. White-glove in-home assembly and packaging removal are included for all dining tables, sofas, and bedframes. You will receive real-time SMS tracking updates prior to delivery.
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header" id="headingTwo">
            <button class="accordion-button collapsed fw-bold fs-6 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
              <i class="bi bi-shield-check text-success me-3 fs-5"></i> What is your return policy and warranty?
            </button>
          </h2>
          <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
            <div class="accordion-body text-muted lh-lg">
              We offer a 30-day risk-free in-home trial. If you are not completely satisfied with your furniture, we will arrange a complimentary pickup and full refund. Furthermore, all solid wood frames are backed by our 10-Year Craftsmanship Guarantee.
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header" id="headingThree">
            <button class="accordion-button collapsed fw-bold fs-6 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
              <i class="bi bi-droplet-half text-primary me-3 fs-5"></i> How should I care for natural wood and fabric furniture?
            </button>
          </h2>
          <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
            <div class="accordion-body text-muted lh-lg">
              For solid oak and ash wood, wipe with a damp microfiber cloth and reapply organic beeswax polish every 6–12 months. Upholstery fabrics are stain-resistant and can be spot cleaned using mild water-based detergent.
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header" id="headingFour">
            <button class="accordion-button collapsed fw-bold fs-6 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
              <i class="bi bi-sliders text-info me-3 fs-5"></i> Can I request custom fabric swatches or wood samples?
            </button>
          </h2>
          <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
            <div class="accordion-body text-muted lh-lg">
              Yes! We offer complimentary sample kits delivered directly to your door within 2 business days. Simply contact our support team or request a sample kit on any product details page.
            </div>
          </div>
        </div>

      </div>

      <div class="card border-0 rounded-4 p-4 text-center" style="background-color: var(--niru-bg-alt);">
        <h5 class="fw-bold mb-2" style="color: var(--niru-primary);">Still have questions?</h5>
        <p class="text-muted mb-3">Our customer care and interior styling specialists are here to help you 7 days a week.</p>
        <div>
          <a href="contact.php" class="btn btn-niru-primary rounded-3 px-4">Contact Support <i class="bi bi-envelope ms-2"></i></a>
        </div>
      </div>

    </div>
  </main>

<?php include 'footer.php'; ?>
