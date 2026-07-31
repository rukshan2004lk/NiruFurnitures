# NiRu Furnitures - E-Commerce Web Application

**Course:** ICT 1209 – Web Technologies  
**Institution:** Department of ICT, Faculty of Applied Sciences, Rajarata University of Sri Lanka  
**Academic Batch:** 2024 Batch  
**Project Type:** Web Technologies Mini Project  

---

## 📌 Project Overview

**NiRu Furnitures** is a modern, eco-conscious luxury furniture e-commerce web application built using standard web technologies (**HTML5**, **Vanilla CSS**, **Bootstrap 5**, and **JavaScript ES6**). The application features an elegant Nordic design aesthetic, full mobile responsiveness, interactive user/admin portals, and dynamic JavaScript capabilities.

---

## 🎯 Requirements Compliance Checklist

### 1. Structure & Pages (Minimum 3 Required)
The application includes 19 fully structured pages:
- **Storefront & Shopping**:
  - `index.html` - Homepage with hero banner, categories, featured collection & testimonials
  - `shop.html` - Product catalog with live category filter and price range slider
  - `product-detail.html` - Detailed product showcase with interactive image gallery
  - `cart.html` - Interactive shopping cart summary
  - `checkout.html` - Multi-step checkout form with address & payment validation
  - `about.html` - Brand philosophy & company mission
  - `contact.html` - Contact page with location map & inquiry form
  - `faq.html` - Frequently Asked Questions accordion
- **User Portal**:
  - `user/dashboard.html` - User account dashboard & statistics
  - `user/orders.html` - Order history & order tracking
  - `user/wishlist.html` - Saved wishlist items grid
  - `user/settings.html` - Account profile & security settings
- **Admin Portal**:
  - `admin/admin-dashboard.html` - Sales overview & analytics dashboard
  - `admin/admin-products.html` - Product inventory management
  - `admin/admin-orders.html` - Customer order processing
  - `admin/admin-customers.html` - Customer user accounts table
  - `admin/admin-settings.html` - Admin configuration settings
- **Authentication**:
  - `login.html` - User sign-in interface
  - `register.html` - Account registration form

### 2. Styling & Theme
- Consistent custom CSS (`assets/css/style.css`) using CSS Variables (`:root` tokens for primary `#442a22`, secondary `#725a39`, background light `#fcf9f8`, etc.).
- Modern typography pairing (**Plus Jakarta Sans** for headings, **Inter** for body text).

### 3. Bootstrap 5 Components
- **Navbar**: Sticky responsive navigation bar with dropdown menus.
- **Cards**: Product cards, bento metrics cards, wishlist cards, and admin table cards.
- **Offcanvas**: Mobile drawer sidebars for User & Admin navigation on small screens.
- **Badges & Modals**: Stock indicators, status badges, price tags, and modal triggers.
- **Buttons & Forms**: Customized inputs, radio option blocks, and icon buttons.

### 4. Responsive Design
- Fully mobile, tablet, and desktop responsive across all screen breakpoints (`< 576px`, `< 768px`, `< 992px`, `>= 1200px`).
- Custom media queries in `assets/css/style.css` ensuring zero horizontal overflow (`overflow-x: hidden`).

### 5. Navigation Bar & Footer
- Standardized top navigation bar and footer present across every single page.

### 6. Required JavaScript Features Implemented (`assets/js/script.js`)
1. **Dynamic Content Updates & Filtering**: Live price range updates and category filter toggles on `shop.html`.
2. **Interactive Image Slider / Gallery**: Thumbnail click switcher for main product images on `product-detail.html`.
3. **Form Validation**: Real-time required field checking and regex email format validation across form submissions (`checkout.html`, `contact.html`, `register.html`, `login.html`).
4. **Smooth Scrolling**: Smooth anchor link scrolling (`a[href^="#"]`) for page sections.
5. **Event Handling**: Click event toggles for wishlist heart icons and interactive state updates.
6. **Custom Animations**: Intersection Observer fade-in scroll animations (`.fade-in-element` & `.fade-in-visible`).

---

## 📁 Directory Structure

```text
nirufurniture/
├── index.html
├── shop.html
├── product-detail.html
├── cart.html
├── checkout.html
├── about.html
├── contact.html
├── faq.html
├── login.html
├── register.html
├── README.md
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── Images/
│   ├── featured/
│   ├── products/
│   ├── collection/
│   ├── wishlist/
│   └── pairswell/
├── user/
│   ├── dashboard.html
│   ├── orders.html
│   ├── settings.html
│   └── wishlist.html
└── admin/
    ├── admin-dashboard.html
    ├── admin-products.html
    ├── admin-orders.html
    ├── admin-customers.html
    └── admin-settings.html
```

---

## 🚀 How to Run Locally

1. **Option A (XAMPP / Local Server)**:
   - Copy or clone the repository into your XAMPP `htdocs` folder: `c:/xampp/htdocs/nirufurniture`.
   - Start Apache via XAMPP Control Panel.
   - Open your browser and navigate to `http://localhost/nirufurniture`.

2. **Option B (Direct File Access / Live Server)**:
   - Double-click `index.html` or open it in any Web Browser (Chrome, Firefox, Edge).
