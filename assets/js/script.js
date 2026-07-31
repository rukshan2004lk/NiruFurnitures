document.addEventListener("DOMContentLoaded", function () {
  
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

  const categoryBtns = document.querySelectorAll(".category-filter-btn");
  categoryBtns.forEach(btn => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      categoryBtns.forEach(b => {
        b.classList.remove("active");
        b.classList.add("inactive");
      });
      this.classList.remove("inactive");
      this.classList.add("active");
    });
  });

  const thumbnails = document.querySelectorAll(".gallery-thumbnail");
  const mainImage = document.querySelector(".main-product-image img");
  if (thumbnails.length && mainImage) {
    thumbnails.forEach(thumb => {
      thumb.addEventListener("click", function () {
        thumbnails.forEach(t => t.classList.remove("active"));
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

  const forms = document.querySelectorAll("form");
  forms.forEach(form => {
    form.addEventListener("submit", function (e) {
      let isValid = true;
      const requiredInputs = form.querySelectorAll("[required]");
      
      requiredInputs.forEach(input => {
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

  const anchorLinks = document.querySelectorAll('a[href^="#"]');
  anchorLinks.forEach(link => {
    link.addEventListener("click", function (e) {
      const targetId = this.getAttribute("href");
      if (targetId && targetId !== "#") {
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          e.preventDefault();
          targetElement.scrollIntoView({
            behavior: "smooth",
            block: "start"
          });
        }
      }
    });
  });

  const wishlistBtns = document.querySelectorAll(".wishlist-btn, .favorite-btn, .remove-wishlist-btn");
  wishlistBtns.forEach(btn => {
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
    threshold: 0.1
  };
  const fadeInObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add("fade-in-visible");
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  document.querySelectorAll(".product-card, .collection-card, .stat-bento-card, .shop-product-card").forEach(el => {
    el.classList.add("fade-in-element");
    fadeInObserver.observe(el);
  });
});
