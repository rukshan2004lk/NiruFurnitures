let googleTokenClient;

function showAlert(message, type = "info", title = "") {
  if (typeof Swal !== "undefined") {
    let iconType = "info";
    if (type === "success") iconType = "success";
    else if (type === "error" || type === "danger") iconType = "error";
    else if (type === "warning") iconType = "warning";
    
    Swal.fire({
      icon: iconType,
      title: title || (iconType === "success" ? "Success" : iconType === "error" ? "Error" : "Notice"),
      text: message,
      confirmButtonColor: "#442a22"
    });
  } else {
    alert(message);
  }
}

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

    const productContainer = document.getElementById("productContainer");
  if (productContainer) {
    productShow(0); 
  }
  }

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
    showAlert("Please fill in all required fields.", "warning");
    return;
  }

  if (password.value !== cpassword.value) {
    showAlert("Confirm password did not match.", "warning");
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
  form.append("n", number ? number.value : "");

  const request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();

      if (response === "success") {
        showAlert("Registration successful! Redirecting to login...", "success");
        setTimeout(() => {
          window.location.href = "login.php";
        }, 1200);
      } else {
        showAlert(response, "error");
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
  var rememberMe = document.getElementById("rememberMe");
  var msg = document.getElementById("msg");
  var msgdiv = document.getElementById("msgdiv");


  if (!email || !password) {
    showAlert("Please enter your Email and Password.", "warning");
    return;
  }

  var form = new FormData();
  form.append("e", email.value);
  form.append("p", password.value);
  form.append("r", rememberMe && rememberMe.checked ? "true" : "false");

  const request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();

      if (response === "success") {
        showAlert("Sign in successful! Redirecting...", "success");
        setTimeout(() => {
          window.location.href = "index.php";
        }, 1000);
      } else {
        showAlert(response, "error");
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
  var rememberMe = document.getElementById("rememberMe");
  var msg = document.getElementById("msg");
  var msgdiv = document.getElementById("msgdiv");

  if (!email || !password) {
    showAlert("Please enter your Email and Password.", "warning");
    return;
  }

  var form = new FormData();
  form.append("e", email.value);
  form.append("p", password.value);
  form.append("r", rememberMe && rememberMe.checked ? "true" : "false");

  const request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();

      if (response === "success") {
        showAlert("Admin Access Granted! Redirecting...", "success");
        setTimeout(() => {
          window.location.href = "admin/admin-dashboard.php";
        }, 1000);
      } else {
        showAlert(response, "error");
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

function updateSetting() {
  var fName = document.getElementById("fName");
  var lName = document.getElementById("lName");
  var line1 = document.getElementById("line1");
  var line2 = document.getElementById("line2");
  var city = document.getElementById("city");
  var pCode = document.getElementById("postalCode"); 
  var mobile = document.getElementById("phoneNumber");


  if (!fName || !lName || !line1 || !pCode || !mobile) {
    showAlert("Required form elements are missing.", "warning");
    return;
  }

  var form = new FormData();
  form.append("f", fName.value);
  form.append("l", lName.value);
  form.append("l1", line1.value);
  form.append("l2", line2 ? line2.value : "");
  form.append("c", city ? city.value : "");
  form.append("pc", pCode.value);
  form.append("m", mobile.value);

  const request = new XMLHttpRequest();

  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();

      if (response === "success") {
        window.location.reload();
      } else {
        showAlert(response, "error");
      }
    }
  };

  request.open("POST", "settingProcess.php", true);
  request.send(form);
}


function passwordChange(){

  var cPassword = document.getElementById("currentPassword");
  var nPassword = document.getElementById("newPassword");
  var vPassword = document.getElementById("confirmNewPassword");

 if (nPassword.value !== vPassword.value) {
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

  form.append("c",cPassword.value);
  form.append("n",nPassword.value);


  const request = new XMLHttpRequest()

   request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();

      if (response === "success") {
        showAlert("Password updated successfully!", "success");
        cPassword.value = "";
        nPassword.value = "";
        vPassword.value = "";
      } else {
        showAlert(response, "error");
      }
    }
  };

  request.open("POST","passwordChange.php",true);
  request.send(form);

}
function productShow(categoryId) {
  var productContainer = document.getElementById("productContainer");

  // Visual feedback: briefly dim the container while fetching
  if (productContainer) {
    productContainer.style.opacity = "0.4";
  }

  var request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      if (productContainer) {
        productContainer.innerHTML = request.responseText;
        productContainer.style.opacity = "1";
      }
    }
  };

  request.open("GET", "loadProductsProcess.php?category=" + encodeURIComponent(categoryId), true);
  request.send();
}

function searchProducts() {
  const searchInput = document.getElementById("searchTxt");
  const productContainer = document.getElementById("productContainer");
  const searchText = searchInput ? searchInput.value.trim() : "";

  const request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      if (productContainer) {
        productContainer.innerHTML = request.responseText;
      }
    }
  };

  // Send search query via GET to loadProductsProcess.php
  request.open("GET", "loadProductsProcess.php?search=" + encodeURIComponent(searchText), true);
  request.send();
}


let currentCategory = 0;
let currentSort = "newest";
let currentMaxPrice = 50000;
let currentPage = 1; // Track active page

function changePage(pageNo) {
  if (pageNo < 1) return;
  currentPage = pageNo;
  filterProducts();
  // Smooth scroll back to top of products
  document.getElementById("productContainer")?.scrollIntoView({ behavior: "smooth" });
}

function selectCategory(catId, element) {
  currentCategory = catId;
  currentPage = 1; // Reset to page 1 on filter change
  document.querySelectorAll(".category-filter-btn").forEach((btn) => {
    btn.classList.remove("active");
    btn.classList.add("inactive");
  });
  if (element) {
    element.classList.remove("inactive");
    element.classList.add("active");
  }
  filterProducts();
}

function selectSort(sortType, element) {
  currentSort = sortType;
  currentPage = 1; // Reset to page 1
  if (element) {
    document.querySelectorAll(".sort-pill").forEach((btn) => btn.classList.remove("active"));
    element.classList.add("active");
  }
  filterProducts();
}

function filterProducts() {
  const searchInput = document.getElementById("searchTxt");
  const searchText = searchInput ? searchInput.value.trim() : "";
  const productContainer = document.getElementById("productContainer");
  const paginationContainer = document.getElementById("paginationContainer");

  if (productContainer) productContainer.style.opacity = "0.4";

  const queryParams = new URLSearchParams({
    category: currentCategory,
    sort: currentSort,
    price: currentMaxPrice,
    search: searchText,
    page: currentPage,
  });

  const request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      if (productContainer) {
        productContainer.innerHTML = request.responseText;
        productContainer.style.opacity = "1";
      }
    }
  };

  request.open("GET", "loadProductsProcess.php?" + queryParams.toString(), true);
  request.send();
}


function buyNow(productId) {
  const qtyElement = document.getElementById("qtyVal");
  const qty = qtyElement ? parseInt(qtyElement.textContent) || 1 : 1;
  window.location.href = "checkout.php?id=" + productId + "&qty=" + qty;
}


function placeOrder() {
  const email = document.getElementById("email");
  const mobile = document.getElementById("mobile");
  const fname = document.getElementById("fname");
  const lname = document.getElementById("lname");
  const line1 = document.getElementById("line1");
  const line2 = document.getElementById("line2");
  const city = document.getElementById("city");
  const country = document.getElementById("country");
  const pcode = document.getElementById("pcode");

  const productId = document.getElementById("productId");
  const qty = document.getElementById("qty");

  const cardNumber = document.getElementById("cardNumber");
  const expDate = document.getElementById("expDate");
  const cvv = document.getElementById("cvv");

  if (!mobile.value.trim()) {
    showAlert("Please enter your phone number.", "warning");
    return;
  }
  if (!fname.value.trim() || !lname.value.trim()) {
    showAlert("Please enter your first and last name.", "warning");
    return;
  }
  if (!line1.value.trim() || !city.value.trim() || !pcode.value.trim()) {
    showAlert("Please fill in your complete delivery address.", "warning");
    return;
  }
  if (!cardNumber.value.trim() || !expDate.value.trim() || !cvv.value.trim()) {
    showAlert("Please enter your card details.", "warning");
    return;
  }

  const form = new FormData();
  form.append("email", email.value);
  form.append("mobile", mobile.value);
  form.append("fname", fname.value);
  form.append("lname", lname.value);
  form.append("line1", line1.value);
  form.append("line2", line2 ? line2.value : "");
  form.append("city", city.value);
  form.append("country", country ? country.value : "Sri Lanka");
  form.append("pcode", pcode.value);

  const color = document.getElementById("color");

  form.append("product_id", productId ? productId.value : 0);
  form.append("qty", qty ? qty.value : 1);
  form.append("color", color ? color.value : "");

  form.append("cN", cardNumber.value);
  form.append("eD", expDate.value);
  form.append("cV", cvv.value);

  const request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();

      if (response === "success") {
        showAlert("Order placed successfully!", "success");
        setTimeout(() => {
          window.location.href = "user/orders.php";
        }, 1200);
      } else {
        showAlert(response, "error");
      }
    }
  };

  request.open("POST", "checkoutProcess.php", true);

  request.send(form);
}

function addToCart(productId) {
  const qtyElement = document.getElementById("qtyVal") || document.getElementById("qty");
  const qty = qtyElement ? parseInt(qtyElement.textContent || qtyElement.value) || 1 : 1;

  const colorLabel = document.getElementById("selectedColorName");
  const chosenColor = colorLabel ? colorLabel.textContent.trim() : (typeof selectedColor !== "undefined" ? selectedColor : "");

  const form = new FormData();
  form.append("id", productId);
  form.append("qty", qty);
  if (chosenColor) {
    form.append("color", chosenColor);
  }

  const endpoint = window.location.pathname.includes("/user/")
    ? "../addToCartProcess.php"
    : "addToCartProcess.php";

  const request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();

      if (response === "Please login first.") {
        showAlert("Please log in to add items to your cart.", "info");
        window.location.href = "login.php";
        return;
      }

      if (response === "success") {
        showAlert("Item added to your cart!", "success");

        const badges = document.querySelectorAll(".badge.bg-danger");
        badges.forEach((b) => {
          let currentCount = parseInt(b.textContent.trim()) || 0;
          b.textContent = currentCount + qty;
          b.classList.remove("d-none");
        });
      } else {
        showAlert(response, "error");
      }
    }
  };

  request.open("POST", endpoint, true);
  request.send(form);
}

function changeCartQty(cartItemId, newQty) {
  const form = new FormData();
  form.append("action", "update");
  form.append("item_id", cartItemId);
  form.append("qty", newQty);

  const request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();
      if (response === "success") {
        window.location.reload();
      } else {
        showAlert(response, "error");
      }
    }
  };

  request.open("POST", "cartProcess.php", true);
  request.send(form);
}

function removeCartItem(cartItemId) {
  if (typeof Swal !== "undefined") {
    Swal.fire({
      title: "Remove Item?",
      text: "Are you sure you want to remove this item from your cart?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#1b1c1c",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Yes, remove"
    }).then((result) => {
      if (result.isConfirmed) {
        executeRemoveCartItem(cartItemId);
      }
    });
  } else if (confirm("Are you sure you want to remove this item?")) {
    executeRemoveCartItem(cartItemId);
  }
}

function executeRemoveCartItem(cartItemId) {
  const form = new FormData();
  form.append("action", "remove");
  form.append("item_id", cartItemId);

  const request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();
      if (response === "success") {
        window.location.reload();
      } else {
        showAlert(response, "error");
      }
    }
  };

  request.open("POST", "cartProcess.php", true);
  request.send(form);
}

// --------------------------------------------------------
// Wishlist & Dashboard Actions (Handles root and user/ paths)
// --------------------------------------------------------

function removeFromWishlist(wishlistId) {
  if (typeof Swal !== "undefined") {
    Swal.fire({
      title: "Remove Item?",
      text: "Are you sure you want to remove this item from your wishlist?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#1b1c1c",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Yes, remove"
    }).then((result) => {
      if (result.isConfirmed) {
        executeRemoveFromWishlist(wishlistId);
      }
    });
  } else if (confirm("Are you sure you want to remove this item?")) {
    executeRemoveFromWishlist(wishlistId);
  }
}

function executeRemoveFromWishlist(wishlistId) {
  const form = new FormData();
  form.append("wishlist_id", wishlistId);

  const endpoint = window.location.pathname.includes("/user/")
    ? "removeWishlistProcess.php"
    : "user/removeWishlistProcess.php";

  const req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState === 4 && req.status === 200) {
      if (req.responseText.trim() === "success") {
        window.location.reload();
      } else {
        showAlert(req.responseText, "error");
      }
    }
  };
  req.open("POST", endpoint, true);
  req.send(form);
}

function addSingleToCart(productId) {
  const form = new FormData();
  form.append("id", productId);
  form.append("qty", 1);

  const endpoint = window.location.pathname.includes("/user/")
    ? "../addToCartProcess.php"
    : "addToCartProcess.php";

  const req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState === 4 && req.status === 200) {
      if (req.responseText.trim() === "success") {
        showAlert("Added to cart successfully!", "success");
      } else {
        showAlert(req.responseText, "error");
      }
    }
  };
  req.open("POST", endpoint, true);
  req.send(form);
}

function addAllToCart() {
  const cards = document.querySelectorAll(".wishlist-card-item");
  if (cards.length === 0) return;

  const endpoint = window.location.pathname.includes("/user/")
    ? "../addToCartProcess.php"
    : "addToCartProcess.php";

  const cartRedirect = window.location.pathname.includes("/user/")
    ? "../cart.php"
    : "cart.php";

  let completed = 0;
  cards.forEach((card) => {
    const pId = card.getAttribute("data-product-id");
    const form = new FormData();
    form.append("id", pId);
    form.append("qty", 1);

    const req = new XMLHttpRequest();
    req.onreadystatechange = function () {
      if (req.readyState === 4 && req.status === 200) {
        completed++;
        if (completed === cards.length) {
          showAlert("All wishlist items added to cart!", "success");
          setTimeout(() => {
            window.location.href = cartRedirect;
          }, 1000);
        }
      }
    };
    req.open("POST", endpoint, true);
    req.send(form);
  });
}

function shareWishlist() {
  if (navigator.clipboard) {
    navigator.clipboard.writeText(window.location.href);
    showAlert("Wishlist link copied to clipboard!", "success");
  } else {
    prompt("Copy your wishlist link:", window.location.href);
  }
}

// Filter recent orders table on dashboard
function filterRecentOrders() {
  const input = document.getElementById("orderSearchInput");
  if (!input) return;

  const filter = input.value.toLowerCase().trim();
  const tbody = document.getElementById("recentOrdersTableBody");
  if (!tbody) return;

  const rows = tbody.getElementsByTagName("tr");
  for (let i = 0; i < rows.length; i++) {
    if (rows[i].id === "noOrdersRow") continue;

    const text = rows[i].textContent.toLowerCase();
    rows[i].style.display = text.indexOf(filter) > -1 ? "" : "none";
  }
}

// Remove wishlist item alias
function removeWishlistItem(wishlistId) {
  removeFromWishlist(wishlistId);
}

// --------------------------------------------------------
// Orders Page Filtering (Live Search & Status Filter)
// --------------------------------------------------------

function filterOrders() {
  const input = document.getElementById("orderSearchInput");
  if (!input) return;

  const filter = input.value.toLowerCase().trim();
  const rows = document.querySelectorAll(".order-row");
  const countLabel = document.getElementById("orderCountLabel");
  let visibleCount = 0;

  rows.forEach((row) => {
    const text = row.textContent.toLowerCase();
    if (text.indexOf(filter) > -1) {
      row.style.display = "";
      visibleCount++;
    } else {
      row.style.display = "none";
    }
  });

  if (countLabel) {
    countLabel.textContent = `Showing ${visibleCount} of ${rows.length} orders`;
  }
}

function filterByStatus(statusTerm) {
  const rows = document.querySelectorAll(".order-row");
  const countLabel = document.getElementById("orderCountLabel");
  let visibleCount = 0;

  rows.forEach((row) => {
    if (statusTerm === "all") {
      row.style.display = "";
      visibleCount++;
    } else {
      const statusCell = row.querySelector(".status-pill");
      if (
        statusCell &&
        statusCell.textContent.toLowerCase().indexOf(statusTerm.toLowerCase()) > -1
      ) {
        row.style.display = "";
        visibleCount++;
      } else {
        row.style.display = "none";
      }
    }
  });

  if (countLabel) {
    countLabel.textContent = `Showing ${visibleCount} of ${rows.length} orders`;
  }
}

function toggleWishlist(productId, btnElement) {
  const icon = btnElement.querySelector("i");
  const form = new FormData();
  form.append("product_id", productId);

  const endpoint = window.location.pathname.includes("/user/")
    ? "../toggleWishlistProcess.php"
    : "toggleWishlistProcess.php";

  const request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      const response = request.responseText.trim();

      if (response === "login_required") {
        showAlert("Please login first to save items to your wishlist.", "info");
        window.location.href = "login.php";
      } else if (response === "added") {
        if (icon) {
          icon.classList.remove("bi-heart");
          icon.classList.add("bi-heart-fill", "text-danger");
        }
      } else if (response === "removed") {
        if (icon) {
          icon.classList.remove("bi-heart-fill", "text-danger");
          icon.classList.add("bi-heart");
        }
      } else {
        showAlert(response, "error");
      }
    }
  };

  request.open("POST", endpoint, true);
  request.send(form);
}

// --------------------------------------------------------
// Admin Product Inventory, Gallery & Modal Management
// --------------------------------------------------------

let deletedImageIds = [];
let currentExistingCount = 0;

// Filter products by status (All, Active, Draft)
function filterAdminProducts(statusFilter) {
  const url = new URL(window.location.href);
  url.searchParams.set("status", statusFilter);
  url.searchParams.set("page", "1");
  window.location.href = url.toString();
}

// Deactivate / Delete product
function deleteProduct(productId) {
  if (typeof Swal !== "undefined") {
    Swal.fire({
      title: "Deactivate Product?",
      text: "Are you sure you want to deactivate/delete this product?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#b87d72",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Yes, deactivate"
    }).then((result) => {
      if (result.isConfirmed) {
        executeDeleteProduct(productId);
      }
    });
  } else if (confirm("Are you sure you want to deactivate/delete this product?")) {
    executeDeleteProduct(productId);
  }
}

function executeDeleteProduct(productId) {
  const form = new FormData();
  form.append("product_id", productId);

  const endpoint = window.location.pathname.includes("/admin/")
    ? "deleteProductProcess.php"
    : "admin/deleteProductProcess.php";

  const req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState === 4 && req.status === 200) {
      if (req.responseText.trim() === "success") {
        showAlert("Product status updated successfully!", "success");
        setTimeout(() => window.location.reload(), 1000);
      } else {
        showAlert(req.responseText, "error");
      }
    }
  };
  req.open("POST", endpoint, true);
  req.send(form);
}

// Reset Add Product Modal state
function resetAddModal() {
  const form = document.getElementById("addProductModalForm");
  const previewBox = document.getElementById("add_preview_box");
  if (form) form.reset();
  if (previewBox) previewBox.innerHTML = "";
}

// Preview selected files for Add Modal & assign primary index
function handleNewImagesPreview(input, previewContainerId, radioGroupName) {
  const container = document.getElementById(previewContainerId);
  if (!container) return;
  container.innerHTML = "";

  if (input.files.length > 5) {
    showAlert("You can select at most 5 images.", "warning");
    input.value = "";
    return;
  }

  Array.from(input.files).forEach((file, idx) => {
    const reader = new FileReader();
    reader.onload = function (e) {
      const card = document.createElement("div");
      card.className = "card p-2 text-center shadow-sm position-relative";
      card.style.width = "120px";

      card.innerHTML = `
        <img src="${e.target.result}" style="width: 100%; height: 85px; object-fit: cover; border-radius: 4px;" class="mb-2">
        <div class="form-check d-flex align-items-center justify-content-center gap-1">
          <input class="form-check-input" type="radio" name="${radioGroupName}" value="${idx}" ${idx === 0 ? "checked" : ""}>
          <label class="form-check-label small" style="font-size: 11px;">Primary</label>
        </div>
      `;
      container.appendChild(card);
    };
    reader.readAsDataURL(file);
  });
}

// Submit Add Product Form
function submitAddProductModal() {
  const form = document.getElementById("addProductModalForm");
  const filesInput = document.getElementById("add_product_images_input");

  if (!filesInput || !filesInput.files || filesInput.files.length === 0) {
    showAlert("Please upload at least one image.", "warning");
    return;
  }

  const formData = new FormData(form);
  const endpoint = window.location.pathname.includes("/admin/")
    ? "addProductProcess.php"
    : "admin/addProductProcess.php";

  const req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState === 4 && req.status === 200) {
      if (req.responseText.trim() === "success") {
        showAlert("Product added successfully!", "success");
        setTimeout(() => window.location.reload(), 1000);
      } else {
        showAlert(req.responseText, "error");
      }
    }
  };
  req.open("POST", endpoint, true);
  req.send(formData);
}

// Open Edit Modal with full gallery & primary radio options
function openEditModal(productId) {
  deletedImageIds = [];
  const delInput = document.getElementById("edit_deleted_images");
  const previewBox = document.getElementById("edit_new_preview_box");
  const newImgInput = document.getElementById("edit_new_images_input");

  if (delInput) delInput.value = "[]";
  if (previewBox) previewBox.innerHTML = "";
  if (newImgInput) newImgInput.value = "";

  const endpoint = window.location.pathname.includes("/admin/")
    ? "getProductProcess.php?id=" + productId
    : "admin/getProductProcess.php?id=" + productId;

  const req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState === 4 && req.status === 200) {
      try {
        const data = JSON.parse(req.responseText);
        document.getElementById("edit_product_id").value = data.product_id;
        document.getElementById("edit_name").value = data.name;
        document.getElementById("edit_category_id").value = data.category_id;
        document.getElementById("edit_status_id").value = data.status_id;
        document.getElementById("edit_price").value = data.price;
        document.getElementById("edit_quantity").value = data.quantity;
        document.getElementById("edit_description").value = data.description || "";

        const galleryBox = document.getElementById("edit_existing_images_box");
        galleryBox.innerHTML = "";
        currentExistingCount = data.images.length;

        if (data.images && data.images.length > 0) {
          data.images.forEach((img) => {
            const card = document.createElement("div");
            card.id = "img_card_" + img.image_id;
            card.className = "card p-2 text-center shadow-sm position-relative";
            card.style.width = "125px";

            card.innerHTML = `
              <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0 px-1 m-1" 
                      onclick="markImageDelete(${img.image_id});" title="Delete image" style="font-size: 11px;">
                <i class="bi bi-x"></i>
              </button>
              <img src="../${img.image_path}" style="width: 100%; height: 85px; object-fit: cover; border-radius: 4px;" class="mb-2">
              <div class="form-check d-flex align-items-center justify-content-center gap-1">
                <input class="form-check-input" type="radio" name="primary_image_choice" value="existing_${img.image_id}" ${img.is_primary == 1 ? "checked" : ""}>
                <label class="form-check-label small" style="font-size: 11px;">Primary</label>
              </div>
            `;
            galleryBox.appendChild(card);
          });
        } else {
          galleryBox.innerHTML = '<span class="text-muted small p-2">No images uploaded for this product yet.</span>';
        }

        updateEditUploadLimitNotice();
        const editModal = new bootstrap.Modal(document.getElementById("editProductModal"));
        editModal.show();
      } catch (e) {
        showAlert("Could not load product details.", "error");
      }
    }
  };
  req.open("GET", endpoint, true);
  req.send();
}

// Mark image for deletion in Edit Modal
function markImageDelete(imageId) {
  if (typeof Swal !== "undefined") {
    Swal.fire({
      title: "Remove Image?",
      text: "Remove this image from product gallery?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#b87d72",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Yes, remove"
    }).then((result) => {
      if (result.isConfirmed) {
        executeMarkImageDelete(imageId);
      }
    });
  } else if (confirm("Remove this image from product?")) {
    executeMarkImageDelete(imageId);
  }
}

function executeMarkImageDelete(imageId) {
  deletedImageIds.push(imageId);
  const delInput = document.getElementById("edit_deleted_images");
  if (delInput) delInput.value = JSON.stringify(deletedImageIds);

  const card = document.getElementById("img_card_" + imageId);
  if (card) card.remove();

  currentExistingCount = Math.max(0, currentExistingCount - 1);
  updateEditUploadLimitNotice();
}

function updateEditUploadLimitNotice() {
  const allowed = Math.max(0, 5 - currentExistingCount);
  const notice = document.getElementById("edit_images_count_notice");
  if (notice) {
    notice.textContent = `You currently have ${currentExistingCount} image(s). You can upload ${allowed} more (Maximum 5 total).`;
  }
}

// Handle additional new images in Edit Modal
function handleEditAdditionalImages(input) {
  const allowed = 5 - currentExistingCount;
  const previewBox = document.getElementById("edit_new_preview_box");
  if (!previewBox) return;
  previewBox.innerHTML = "";

  if (input.files.length > allowed) {
    showAlert(`You can only upload up to ${allowed} more image(s). Total cannot exceed 5.`, "warning");
    input.value = "";
    return;
  }

  Array.from(input.files).forEach((file, idx) => {
    const reader = new FileReader();
    reader.onload = function (e) {
      const card = document.createElement("div");
      card.className = "card p-2 text-center shadow-sm position-relative border-primary";
      card.style.width = "125px";

      card.innerHTML = `
        <span class="badge bg-primary position-absolute top-0 start-0 m-1" style="font-size: 9px;">New</span>
        <img src="${e.target.result}" style="width: 100%; height: 85px; object-fit: cover; border-radius: 4px;" class="mb-2">
        <div class="form-check d-flex align-items-center justify-content-center gap-1">
          <input class="form-check-input" type="radio" name="primary_image_choice" value="new_${idx}">
          <label class="form-check-label small" style="font-size: 11px;">Primary</label>
        </div>
      `;
      previewBox.appendChild(card);
    };
    reader.readAsDataURL(file);
  });
}

// Submit Edit Product
function submitEditProductModal() {
  const form = document.getElementById("editProductModalForm");
  const formData = new FormData(form);

  const endpoint = window.location.pathname.includes("/admin/")
    ? "updateProductProcess.php"
    : "admin/updateProductProcess.php";

  const req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState === 4 && req.status === 200) {
      if (req.responseText.trim() === "success") {
        showAlert("Product updated successfully!", "success");
        setTimeout(() => window.location.reload(), 1000);
      } else {
        showAlert(req.responseText, "error");
      }
    }
  };
  req.open("POST", endpoint, true);
  req.send(formData);
}

// --------------------------------------------------------
// Admin Order Management Functions
// --------------------------------------------------------

function filterAdminOrdersTable() {
  const searchInput = document.getElementById("adminOrderSearchInput");
  const filter = searchInput ? searchInput.value.toLowerCase().trim() : "";
  const rows = document.querySelectorAll(".admin-order-row");

  rows.forEach((row) => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.indexOf(filter) > -1 ? "" : "none";
  });
}

function filterOrdersByStatusPill(statusName) {
  const rows = document.querySelectorAll(".admin-order-row");

  rows.forEach((row) => {
    if (statusName === "all") {
      row.style.display = "";
    } else {
      const pill = row.querySelector(".order-status-pill");
      if (pill && pill.textContent.toLowerCase().includes(statusName.toLowerCase())) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    }
  });
}

function openOrderStatusModal(orderId, currentStatusId, orderNumber) {
  document.getElementById("status_modal_order_id").value = orderId;
  document.getElementById("status_modal_order_number").textContent = orderNumber;
  document.getElementById("status_modal_select").value = currentStatusId;

  const modal = new bootstrap.Modal(document.getElementById("changeOrderStatusModal"));
  modal.show();
}

function submitOrderStatusUpdate() {
  const form = document.getElementById("changeOrderStatusForm");
  const formData = new FormData(form);

  const endpoint = window.location.pathname.includes("/admin/")
    ? "updateOrderStatusProcess.php"
    : "admin/updateOrderStatusProcess.php";

  const req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState === 4 && req.status === 200) {
      if (req.responseText.trim() === "success") {
        showAlert("Order status updated!", "success");
        setTimeout(() => window.location.reload(), 1000);
      } else {
        showAlert(req.responseText, "error");
      }
    }
  };
  req.open("POST", endpoint, true);
  req.send(formData);
}

function filterAdminUsersTable() {
  const input = document.getElementById("adminUserSearchInput");
  const filter = input ? input.value.toLowerCase().trim() : "";
  const rows = document.querySelectorAll(".admin-user-row");

  rows.forEach((row) => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.indexOf(filter) > -1 ? "" : "none";
  });
}

function filterUsersByRolePill(roleName) {
  const rows = document.querySelectorAll(".admin-user-row");

  rows.forEach((row) => {
    if (roleName === "all") {
      row.style.display = "";
    } else {
      const pill = row.querySelector(".user-role-badge");
      if (pill && pill.textContent.toLowerCase().includes(roleName.toLowerCase())) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    }
  });
}

function openEditUserRoleModal(userId, currentRoleId, currentStatusId, userName) {
  document.getElementById("user_modal_id").value = userId;
  document.getElementById("user_modal_name").textContent = userName;
  document.getElementById("user_modal_role_select").value = currentRoleId;
  document.getElementById("user_modal_status_select").value = currentStatusId;

  const modal = new bootstrap.Modal(document.getElementById("editUserRoleModal"));
  modal.show();
}

function submitUserRoleUpdate() {
  const form = document.getElementById("editUserRoleForm");
  const formData = new FormData(form);

  const endpoint = window.location.pathname.includes("/admin/")
    ? "updateUserRoleProcess.php"
    : "admin/updateUserRoleProcess.php";

  const req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState === 4 && req.status === 200) {
      if (req.responseText.trim() === "success") {
        showAlert("User role updated successfully!", "success");
        setTimeout(() => window.location.reload(), 1000);
      } else {
        showAlert(req.responseText, "error");
      }
    }
  };
  req.open("POST", endpoint, true);
  req.send(formData);
}

// --------------------------------------------------------
// Admin Settings Handlers
// --------------------------------------------------------

function submitAdminAccountUpdate() {
  const form = document.getElementById("adminAccountForm");
  const formData = new FormData(form);

  const endpoint = window.location.pathname.includes("/admin/")
    ? "updateAdminAccountProcess.php"
    : "admin/updateAdminAccountProcess.php";

  const req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState === 4 && req.status === 200) {
      if (req.responseText.trim() === "success") {
        showAlert("Account details updated successfully!", "success");
        setTimeout(() => window.location.reload(), 1000);
      } else {
        showAlert(req.responseText, "error");
      }
    }
  };
  req.open("POST", endpoint, true);
  req.send(formData);
}

function submitAdminPasswordChange() {
  const form = document.getElementById("adminPasswordForm");
  const formData = new FormData(form);

  const endpoint = window.location.pathname.includes("/admin/")
    ? "changeAdminPasswordProcess.php"
    : "admin/changeAdminPasswordProcess.php";

  const req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState === 4 && req.status === 200) {
      if (req.responseText.trim() === "success") {
        showAlert("Password updated successfully!", "success");
        form.reset();
      } else {
        showAlert(req.responseText, "error");
      }
    }
  };
  req.open("POST", endpoint, true);
  req.send(formData);
}

// --------------------------------------------------------
// Customer Product Review Submission
// --------------------------------------------------------
function submitProductReview() {
  const form = document.getElementById("productReviewForm");
  if (!form) return;

  const formData = new FormData(form);
  const endpoint = window.location.pathname.includes("/user/")
    ? "../addReviewProcess.php"
    : "addReviewProcess.php";

  const req = new XMLHttpRequest();
  req.onreadystatechange = function () {
    if (req.readyState === 4 && req.status === 200) {
      const res = req.responseText.trim();
      if (res === "login_required") {
        showAlert("Please log in to submit a review.", "info");
        window.location.href = "login.php";
      } else if (res === "success" || res === "updated") {
        showAlert(res === "updated" ? "Your review has been updated!" : "Review submitted successfully!", "success");
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      } else {
        showAlert(res, "error");
      }
    }
  };
  req.open("POST", endpoint, true);
  req.send(formData);
}