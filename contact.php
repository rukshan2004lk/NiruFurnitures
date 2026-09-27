<?php
$page_title = "NiRu Furnitures - Contact Us";
include 'header.php';
?>

  <main style="padding-top: 100px; padding-bottom: 80px;">
    <div class="container-xl py-4">
      
      <div class="text-center mx-auto mb-5" style="max-width: 768px;">
        <h1 class="display-4 fw-bold mb-3" style="color: var(--niru-primary); line-height: 1.1;">
          Get in Touch
        </h1>
        <p class="fs-5 text-muted" style="color: var(--niru-body-text); line-height: 1.6;">
          We believe in crafting more than just furniture; we craft connections. Reach out for bespoke inquiries, shipping updates, or styling advice.
        </p>
      </div>

      <div class="row g-5 align-items-start mb-5">
        
        <div class="col-12 col-lg-7">
          <div class="contact-form-card p-4 p-md-5 bg-white rounded-4 shadow-sm">
            <form onsubmit="event.preventDefault(); showAlert('Thank you for contacting us! We will reply to your message soon.', 'success', 'Message Sent'); this.reset();">
              <div class="row g-4 mb-4">
                
                <div class="col-12 col-md-6">
                  <label for="userName" class="form-label-custom">Name</label>
                  <input type="text" class="form-control form-control-custom" id="userName" placeholder="Your Full Name" required>
                </div>
                
                <div class="col-12 col-md-6">
                  <label for="userEmail" class="form-label-custom">Email</label>
                  <input type="email" class="form-control form-control-custom" id="userEmail" placeholder="email@domain.com" required>
                </div>
              </div>

              <div class="mb-4">
                <label for="subjectSelect" class="form-label-custom">Subject</label>
                <select class="form-select form-select-custom" id="subjectSelect">
                  <option selected>Custom Commission</option>
                  <option value="1">Order Status & Shipping</option>
                  <option value="2">Product & Styling Inquiries</option>
                  <option value="3">Trade & Wholesale Partnership</option>
                </select>
              </div>

              <div class="mb-4">
                <label for="userMessage" class="form-label-custom">Message</label>
                <textarea class="form-control form-control-custom" id="userMessage" rows="5" placeholder="Tell us about your project or inquiry..." required></textarea>
              </div>

              <button type="submit" class="btn btn-niru-primary py-3 px-5">Send Message</button>
            </form>
          </div>
        </div>

        <div class="col-12 col-lg-5">
          <div class="d-flex flex-column gap-4">
            
            <div class="d-flex gap-3 align-items-start">
              <div class="info-icon-wrapper p-3 rounded-circle" style="background-color: #f4efeb; color: var(--niru-primary);">
                <i class="bi bi-geo-alt fs-4"></i>
              </div>
              <div>
                <h3 class="fs-4 fw-semibold mb-2" style="color: var(--niru-primary);">Visit Our Studio</h3>
                <p class="mb-0 text-muted" style="line-height: 1.5;">
                  153/8A Temple Rd,<br>
                  Alluthwatta pohaddramulla,<br>
                  Wadduwa, Sri Lanka
                </p>
              </div>
            </div>

            <div class="d-flex gap-3 align-items-start">
              <div class="info-icon-wrapper p-3 rounded-circle" style="background-color: #f4efeb; color: var(--niru-primary);">
                <i class="bi bi-telephone fs-4"></i>
              </div>
              <div>
                <h3 class="fs-4 fw-semibold mb-1" style="color: var(--niru-primary);">Call Us</h3>
                <p class="fs-6 mb-0" style="color: var(--niru-body-text);">076 4209970</p>
                <small class="text-muted">Mon - Fri, 9am - 6pm</small>
              </div>
            </div>

            <div class="d-flex gap-3 align-items-start">
              <div class="info-icon-wrapper p-3 rounded-circle" style="background-color: #f4efeb; color: var(--niru-primary);">
                <i class="bi bi-envelope fs-4"></i>
              </div>
              <div>
                <h3 class="fs-4 fw-semibold mb-1" style="color: var(--niru-primary);">Email Inquiries</h3>
                <p class="mb-0" style="color: var(--niru-body-text);">nirufurni@gmail.com</p>
                <p class="mb-0" style="color: var(--niru-body-text);">nirusupport@gmail.com</p>
              </div>
            </div>

            <div class="hours-chip-box p-4 rounded-4 bg-light border">
              <div class="footer-heading mb-3" style="font-size: 14px; letter-spacing: 0.7px;">SHOWROOM HOURS</div>
              <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-secondary">Mon-Fri: 10:00 - 19:00</span>
                <span class="badge bg-secondary">Sat: 11:00 - 17:00</span>
                <span class="badge bg-dark">Sun: By Appointment</span>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </main>

<?php include 'footer.php'; ?>
