let googleTokenClient;

// Helper to initialize Google Token Client safely
function initGoogleAuth() {
  if (typeof google !== "undefined" && google.accounts && google.accounts.oauth2) {
    if (!googleTokenClient) {
      googleTokenClient = google.accounts.oauth2.initTokenClient({
        client_id: "164813452523-n69vd85cdlc47c531cmj4aejth9ss2as.apps.googleusercontent.com",
        scope: "email profile openid",
        callback: function (tokenResponse) {
          if (tokenResponse.access_token) {
            sendOAuthData("google", tokenResponse.access_token);
          }
        },
      });
    }
  }
}

document.addEventListener("DOMContentLoaded", function () {
  // 1. Price Range Slider Display
  const priceRange = document.getElementById("priceRange");
  if (priceRange) {
    priceRange.addEventListener("input", function (e) {
      const val = e.target.value;
      const displaySpan = e.target.nextElementSibling?.querySelector("span:last-child");
      if (displaySpan) {
        displaySpan.textContent = `Rs. ${parseInt(val).toLocaleString()}+`;
      }
    });
  }

  // 2. Category Filter Buttons
  const categoryBtns = document.querySelectorAll(".category-filter-btn");
  categoryBtns.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      categoryBtns.forEach((b) => {
        b.classList.remove("active");
        b.classList.add("inactive");
      });
      this.classList.remove("inactive");
      this.classList.add("active");
    });
  });

  // 3. Product Gallery Image Switcher
  const thumbnails = document.querySelectorAll(".gallery-thumbnail");
  const mainImage = document.querySelector(".main-product-image img");
  if (thumbnails.length && mainImage) {
    thumbnails.forEach((thumb) => {
      thumb.addEventListener("click", function () {
        thumbnails.forEach((t) => t.classList.remove("active"));
        this.classList.add("active");
        const newSrc = this.getAttribute("src");
        if (newSrc) {
          mainImage.style.opacity = "0.3";
          setTimeout(() => {
            mainImage.setAttribute("src", newSrc);
            mainImage.style.opacity = "1";
          }, 150);
        }
      });
    });
  }

  // 4. Form Validation Handling
  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      let isValid = true;
      const requiredInputs = form.querySelectorAll("[required]");

      requiredInputs.forEach((input) => {
        if (!input.value.trim()) {
          isValid = false;
          input.classList.add("is-invalid");
        } else {
          input.classList.remove("is-invalid");
        }

        if (input.type === "email" && input.value) {
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailRegex.test(input.value)) {
            isValid = false;
            input.classList.add("is-invalid");
          }
        }
      });

      if (!isValid) {
        e.preventDefault();
      }
    });
  });

  // 5. Smooth Anchor Scrolling
  const anchorLinks = document.querySelectorAll('a[href^="#"]');
  anchorLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      const targetId = this.getAttribute("href");
      if (targetId && targetId !== "#") {
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          e.preventDefault();
          targetElement.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
        }
      }
    });
  });

  // 6. Wishlist Toggle Buttons
  const wishlistBtns = document.querySelectorAll(".wishlist-btn, .favorite-btn, .remove-wishlist-btn");
  wishlistBtns.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      const icon = this.querySelector("i");
      if (icon) {
        if (icon.classList.contains("bi-heart")) {
          icon.classList.remove("bi-heart");
          icon.classList.add("bi-heart-fill", "text-danger");
        } else if (icon.classList.contains("bi-heart-fill")) {
          icon.classList.remove("bi-heart-fill", "text-danger");
          icon.classList.add("bi-heart");
        }
      }
    });
  });


  const observerOptions = {
    threshold: 0.1,
  };
  const fadeInObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("fade-in-visible");
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  document.querySelectorAll(".product-card, .collection-card, .stat-bento-card, .shop-product-card").forEach((el) => {
    el.classList.add("fade-in-element");
    fadeInObserver.observe(el);
  });




function signup() {
  const fname = document.getElementById("fname");
  const lname = document.getElementById("lname");
  const email = document.getElementById("email");
  const number = document.getElementById("number");
  const password = document.getElementById("password");
  const cpassword = document.getElementById("cpassword") || document.getElementById("cPassword");
  const msg = document.getElementById("msg");
  const msgdiv = document.getElementById("msgdiv");


  if (!fname || !lname || !email || !password || !cpassword) {
    console.error("One or more input elements were not found in the HTML.");
    return;
  }

  if (password.value !== cpassword.value) {
    if (msg) {
      msg.innerHTML = "Confirm password did not match.";
      msg.className = "alert alert-danger";
    }
    if (msgdiv) {
      msgdiv.className = "d-block";
    }
    return;
  }

  const form = new FormData();
  form.append("f", fname.value);
  form.append("l", lname.value);
  form.append("e", email.value);
  form.append("p", password.value);
  form.append("n", number.value);

  const request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();

      if (response === "success") {
        setTimeout(() => {
          window.location.href = "login.php";
        }, 1200);
      } else {
        if (msg) {
          msg.innerHTML = response;
          msg.className = "alert alert-danger";
        }
        if (msgdiv) {
          msgdiv.className = "d-block";
        }
      }
    }
  };

  request.open("POST", "registerProcess.php", true);
  request.send(form);
}



function signIn() {
  var email = document.getElementById("email");
  var password = document.getElementById("password");
  var msg = document.getElementById("msg");
  var msgdiv = document.getElementById("msgdiv");


  if (!email || !password) {
    console.error("Email or Password input element not found in the HTML.");
    return;
  }

  var form = new FormData();
  form.append("e", email.value);
  form.append("p", password.value);

  const request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();

      if (response === "success") {
      
        setTimeout(() => {
          window.location.href = "index.php";
        }, 1200);
      } else {
        if (msg) {
          msg.innerHTML = response;
          msg.className = "alert alert-danger";
        }
        if (msgdiv) {
          msgdiv.className = "d-block";
        }
      }
    }
  };

  request.open("POST", "loginProcess.php", true);
  request.send(form);
}

function adminSignIn() {
  var email = document.getElementById("email");
  var password = document.getElementById("password");
  var msg = document.getElementById("msg");
  var msgdiv = document.getElementById("msgdiv");

  if (!email || !password) {
    console.error("Email or Password input element not found in the HTML.");
    return;
  }

  var form = new FormData();
  form.append("e", email.value);
  form.append("p", password.value);

  const request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();

      if (response === "success") {
      
        setTimeout(() => {
          window.location.href = "admin/admin-dashboard.php";
        }, 1200);
      } else {
        if (msg) {
          msg.innerHTML = response;
          msg.className = "alert alert-danger";
        }
        if (msgdiv) {
          msgdiv.className = "d-block";
        }
      }
    }
  };

  request.open("POST", "adminLoginProcess.php", true);
  request.send(form);
}