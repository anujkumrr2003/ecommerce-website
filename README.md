# PHP E-commerce Website – Online Shopping Platform

**Part 2 of 2 – Customer-facing E-commerce Website**

A dynamic E-commerce Website developed using Core PHP, MySQL, HTML5, CSS3 and JavaScript.

This project is the customer-facing component of an integrated E-commerce CRM platform. It allows customers to browse products, manage their accounts, add products to their wishlist and shopping cart, place orders and track their purchases.

The website is integrated with a separate CRM and Online Store Admin Panel, which manages products, customers, orders, payments, banners, categories, coupons and website settings.

---

## Project Overview

This project consists of a customer-facing online shopping platform connected to a centralized CRM system.

**Part 1 – CRM & Admin Panel:** Manages business operations and website administration.

**Part 2 – E-commerce Website:** Provides the online shopping experience for customers.

Both applications use the same MySQL database to synchronize products, customer accounts, orders and website settings.

## Technologies Used

- Core PHP
- MySQL
- MySQLi
- HTML5
- CSS3
- JavaScript
- AJAX (where implemented)
- Apache Web Server
- Razorpay Payment Gateway
- PHPMailer / SMTP (where configured)

## Key Features

### 1. Customer Account Management

- Customer registration
- Secure customer login
- Email verification / OTP (if configured)
- Customer profile management
- Saved delivery addresses
- Password management

### 2. Product & Shopping Management

- Dynamic product listing
- Product detail pages
- Product categories
- Product search and filtering
- Shopping cart
- Wishlist
- Featured products
- Product availability and pricing

### 3. Checkout & Orders

- Checkout system
- Customer address selection
- Shipping configuration
- Order placement
- Order history
- Order tracking
- Order cancellation
- Return requests

### 4. Payment Integration

- Razorpay online payment integration
- Cash on Delivery (if enabled)
- Payment verification
- Payment status management
- Failed payment handling

### 5. Website Content

- Dynamic homepage
- Product banners
- Category sections
- Website pages
- FAQ section
- Privacy Policy
- Terms and Conditions
- Shipping and Return Policy
- SEO settings

### 6. CRM Integration

The website connects with the separate CRM application.

- Products are managed through CRM.
- Website customers are synchronized with CRM.
- Online orders are available in CRM.
- Website banners and categories are managed through CRM.
- Website settings are controlled through CRM.
- Confirmed website orders integrate with CRM billing.

## System Architecture

```text
Customer
   |
   v
E-commerce Website
   |
   v
Shared MySQL Database
   |
   v
CRM & Admin Panel
   |
   +---- Product Management
   +---- Customer Management
   +---- Order Management
   +---- Payment Management
   +---- Website Settings
```

## Project Screenshots

### Homepage

![E-commerce Homepage](screenshots/homepage.png)

### Product Listing

![Product Listing](screenshots/products.png)

### Product Details

![Product Details](screenshots/product-details.png)

### Shopping Cart

![Shopping Cart](screenshots/cart.png)

### Checkout

![Checkout](screenshots/checkout.png)

### Customer Account

![Customer Account](screenshots/account.png)

## Related Repository

This website is integrated with a separate CRM and Online Store Management System.

**Part 1 – CRM & Admin Panel:**

https://github.com/anujkumrr2003/php-ecommerce-crm-platform

**Part 2 – E-commerce Website:**

This repository.

Both applications share the same MySQL database.

## Installation Requirements

- PHP 7.4 or newer (PHP 8.x recommended)
- MySQL 5.7+ / MariaDB 10.3+
- Apache Web Server
- mysqli
- mbstring
- curl
- openssl
- iconv
- Apache mod_rewrite

## Installation Guide

### Step 1: Clone Repository

```bash
git clone https://github.com/anujkumrr2003/ecommerce-website.git
```

### Step 2: Configure Local Server

Place the website files inside your XAMPP `htdocs` directory.

```text
D:/xampp/htdocs/ecommerce-website/
```

### Step 3: Create or Connect Database

Create an empty MySQL database or connect to the database created for Part 1.

The CRM and website must use the same database for integrated operation.

### Step 4: Configure Database Connection

Configure the website's private database connection file with the appropriate database credentials.

Never upload real database credentials to GitHub.

### Step 5: Run the Website

Open the website using your local Apache server.

```text
http://localhost/ecommerce-website/
```

Complete any installation or configuration steps required by the application.

## Security

- Secure password handling
- Prepared SQL statements
- CSRF protection (where implemented)
- Image upload validation
- Private database credentials
- Payment gateway signature verification
- HTTPS recommended for production

Never publish database passwords, payment gateway secrets, SMTP passwords or private customer information.

## Developer Information

**Project:** PHP E-commerce Website

**Development:** Full-stack Web Development

**Technologies:** Core PHP, MySQL, JavaScript, HTML5, CSS3

**GitHub:** https://github.com/anujkumrr2003

---

*Part of an integrated PHP E-commerce CRM and Online Store Management Platform.*
