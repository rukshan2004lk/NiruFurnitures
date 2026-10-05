# NiRu Furnitures

NiRu Furnitures is a PHP/MySQL furniture e-commerce application designed for browsing products, managing a shopping cart, placing orders, and maintaining comprehensive user and administrator dashboards.

## Technology Stack

- **Backend**: PHP (Pages & Server-Side Processes)
- **Database**: MySQL (accessed via `connection.php`)
- **Frontend**: HTML5, Bootstrap 5, Bootstrap Icons, Custom CSS (`assets/css/style.css`)
- **JavaScript**: Vanilla JS (`assets/js/script.js`), SweetAlert2 for client-side notifications
- **Email Service**: PHPMailer for contact form submissions

## Features

### Customer Storefront
- **Store & Browsing**: Homepage with hero content, featured products, categories, and a product catalogue.
- **Product Details**: Individual product pages featuring details, images, and user reviews.
- **Shopping Flow**: Dynamic cart management and a complete checkout process.
- **Authentication**: User registration, login, and secure logout.
- **User Dashboard**: Customers can manage their account settings, change passwords, view order history, print invoices, and manage their wishlist.
- **Support Pages**: Contact Us (powered by PHPMailer) and FAQ page.

### Administrator Portal
- **Dashboard Overview**: Admin dashboard for high-level site metrics.
- **Product Management**: Create, read, update, and delete (CRUD) products and categories.
- **Order Management**: Review customer orders and update order fulfillment statuses.
- **User Management**: View registered users and update user roles.
- **Admin Settings**: Dedicated panel to update admin credentials and profile.
- **Secure Access**: Admin authentication and secure logout.

## Project Structure

```text
NiruFurnitures/
├── index.php                          # Storefront homepage
├── shop.php                           # Product catalogue
├── product-detail.php                 # Product detail & reviews
├── cart.php & checkout.php            # Shopping cart and checkout flow
├── about.php, contact.php, faq.php    # Informational pages
├── login.php, register.php            # Customer authentication
├── connection.php                     # MySQL Database connection helper
├── header.php, footer.php             # Shared layout UI components
├── *Process.php                       # Core root-level request handlers
├── .env                               # Environment configurations (e.g. Mail credentials)
├── PHPMailer/                         # Email sending library
├── Images/                            # Local product & category image storage
├── assets/                            # Static CSS and JS assets
├── user/                              # Customer dashboards (Orders, Settings, Wishlist)
└── admin/                             # Administrator panel and management scripts
```

## Run Locally with XAMPP

1. **Clone/Move Project**: Place the project folder in your Apache document root.
   `C:\xampp\htdocs\nirufurnitures`
2. **Start Server**: Open the XAMPP Control Panel and start **Apache** and **MySQL**.
3. **Database Setup**: Open phpMyAdmin, create a database named `niru_furniture` (or as configured in `connection.php`), and import the necessary tables (if you have an export).
4. **Database Configuration**: Ensure `connection.php` holds the correct MySQL credentials (e.g., user: `root`, password: `your_password`).
5. **Environment Setup**: Ensure you have an `.env` file in the root directory for features like the contact form email system:
   ```ini
   MAIL_PASSWORD="your_google_app_password"
   ```
6. **Launch Application**: Open your browser and navigate to:
   `http://localhost/nirufurnitures/`

*Note: The application requires a running PHP/MySQL server. PHP files will not render correctly if opened directly from the file system.*

## Key Request & Process Flows

- **Shopping**: `loadProductsProcess.php`, `addToCartProcess.php`, `cartProcess.php`, `checkoutProcess.php`
- **Authentication**: `loginProcess.php`, `registerProcess.php`, `adminLoginProcess.php`, `logout.php`, `admin/admin-logout.php`
- **Interactions**: `toggleWishlistProcess.php`, `addReviewProcess.php`, `contactProcess.php`
- **User Scripts (in `user/`)**: `settingProcess.php`, `removeWishlistProcess.php`
- **Admin Scripts (in `admin/`)**: Processes for adding, updating, and deleting products, updating orders, and changing admin settings.
