# NiRu Furnitures

NiRu Furnitures is a PHP/MySQL furniture e-commerce application for browsing products, managing a shopping cart, placing orders, and maintaining user and administrator accounts.

## Technology Stack

- PHP pages and server-side processes
- MySQL database accessed through `connection.php`
- Bootstrap 5 and Bootstrap Icons
- Custom CSS in `assets/css/style.css`
- Vanilla JavaScript in `assets/js/script.js`
- SweetAlert2 for client-side notifications

## Features

### Customer Storefront

- Homepage with hero content, furniture categories, and featured products
- Product catalogue and product detail pages
- Shopping cart and checkout flow
- User registration, login, logout, and account settings
- Order history and invoice viewing
- Wishlist management
- Product reviews
- Contact and FAQ pages

### Administrator Portal

- Admin authentication and dashboard
- Product creation, editing, viewing, and deletion
- User management and role updates
- Order review and order status updates
- Administrator account and password settings

## Project Structure

```text
NiruFurnitures/
├── index.php                         # Storefront homepage
├── shop.php                          # Product catalogue
├── product-detail.php                # Product details
├── cart.php                           # Shopping cart
├── checkout.php                       # Checkout form
├── about.php, contact.php, faq.php    # Informational pages
├── login.php, register.php            # Customer authentication
├── *Process.php                       # Form and AJAX request handlers
├── connection.php                     # Database connection and query helper
├── header.php, footer.php             # Shared layout components
├── assets/
│   ├── css/style.css                  # Application styles
│   └── js/script.js                   # Client-side interactions
├── Images/                            # Product and category images
├── user/                              # Customer dashboard, orders, wishlist, settings
└── admin/                             # Administrator dashboard and management pages
```

## Run Locally with XAMPP

1. Place the project in the Apache document root, for example:
   `C:\xampp\htdocs\NiruFurnitures`
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Create the project database and required tables in phpMyAdmin.
4. Update the database credentials in `connection.php` if they differ from the local setup.
5. Open the application at:
   `http://localhost/NiruFurnitures/`

The application requires a running PHP/MySQL server; PHP files should not be opened directly from the file system.

## Main Request Flows

- Product loading: `loadProductsProcess.php`
- Cart operations: `addToCartProcess.php` and `cartProcess.php`
- Checkout and order creation: `checkoutProcess.php`
- Authentication: `loginProcess.php`, `registerProcess.php`, and `logout.php`
- Wishlist operations: `toggleWishlistProcess.php` and `user/removeWishlistProcess.php`
- Reviews: `addReviewProcess.php`
- Admin product management: files in `admin/` beginning with `add`, `get`, `update`, or `delete`
   - Copy or clone the repository into your XAMPP `htdocs` folder: `c:/xampp/htdocs/nirufurniture`.
   - Start Apache via XAMPP Control Panel.
   - Open your browser and navigate to `http://localhost/nirufurniture`.

2. **Option B (Direct File Access / Live Server)**:
   - Double-click `index.html` or open it in any Web Browser (Chrome, Firefox, Edge).
