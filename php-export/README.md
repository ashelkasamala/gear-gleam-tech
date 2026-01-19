# AUTO SPARE WORKSHOP - PHP Export Package

## 📁 Project Structure

```
php-export/
├── config/
│   ├── config.php          # Main configuration
│   └── database.php         # Database connection
├── classes/
│   ├── User.php             # User authentication & management
│   ├── Product.php          # Product management
│   ├── Cart.php             # Shopping cart
│   ├── Order.php            # Order management
│   └── Chat.php             # Live chat system
├── includes/
│   ├── header.php           # Site header
│   └── footer.php           # Site footer with chat widget
├── assets/
│   ├── css/
│   │   └── style.css        # Complete stylesheet
│   ├── js/
│   │   └── main.js          # JavaScript functionality
│   └── images/              # Image assets folder
├── api/
│   ├── cart.php             # Cart AJAX API
│   ├── chat.php             # Chat AJAX API
│   └── search.php           # Search autocomplete API
├── database/
│   └── schema.sql           # Complete database schema
├── admin/                   # Admin panel (to be created)
├── index.php                # Homepage
├── login.php                # Login page
├── register.php             # Registration page (to be created)
├── shop.php                 # Shop listing (to be created)
├── product.php              # Product detail (to be created)
├── cart.php                 # Cart page (to be created)
├── checkout.php             # Checkout (to be created)
├── dashboard.php            # User dashboard (to be created)
└── README.md                # This file
```

## 🚀 Installation

### 1. Database Setup

1. Create a MySQL database called `autospare_workshop`
2. Import the schema:
   ```bash
   mysql -u root -p autospare_workshop < database/schema.sql
   ```

### 2. Configuration

Edit `config/database.php` and update the credentials:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'autospare_workshop');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 3. Web Server Setup

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 4. File Permissions

```bash
chmod -R 755 php-export/
chmod -R 777 php-export/uploads/
```

## 🔐 Demo Credentials

| Role  | Email                  | Password  |
|-------|------------------------|-----------|
| Admin | admin@autospare.com    | admin123  |
| User  | user@example.com       | user123   |

## ✨ Features Included

### Frontend
- ✅ Responsive design (mobile-first)
- ✅ Dark/Light theme toggle
- ✅ Premium CSS styling with animations
- ✅ Product search with autocomplete
- ✅ Shopping cart functionality
- ✅ Live chat widget
- ✅ Toast notifications

### Backend
- ✅ User authentication (login/register)
- ✅ Session management with timeout
- ✅ CSRF protection
- ✅ Password hashing (bcrypt)
- ✅ Audit logging
- ✅ Product management
- ✅ Order processing
- ✅ Cart system
- ✅ Chat system

### Database
- ✅ Users with roles
- ✅ Products with categories
- ✅ Orders and order items
- ✅ Shopping cart
- ✅ Wishlist
- ✅ Chat conversations and messages
- ✅ Notifications
- ✅ Coupons
- ✅ Audit logs

## 📝 Pages to Create

The following pages need to be created following the same patterns:

1. `register.php` - User registration
2. `shop.php` - Product listing with filters
3. `product.php` - Product detail page
4. `cart.php` - Shopping cart page
5. `checkout.php` - Checkout process
6. `dashboard.php` - User dashboard
7. `admin/index.php` - Admin dashboard
8. `admin/products.php` - Product management
9. `admin/orders.php` - Order management
10. `admin/users.php` - User management
11. `admin/chat.php` - Chat management

## 🎨 Design System

### CSS Variables
All colors and design tokens are defined as CSS custom properties in `style.css`:
- `--primary` - Brand orange
- `--secondary` - Steel gray
- `--success` - Green
- `--warning` - Amber
- `--destructive` - Red
- `--background`, `--foreground`, `--card`, etc.

### Components
- Buttons: `.btn`, `.btn-primary`, `.btn-gradient`, etc.
- Cards: `.card`, `.card-interactive`, `.card-glass`
- Inputs: `.input`, `.input-filled`, `.input-dark`
- Badges: `.badge`, `.badge-primary`, `.badge-success`, etc.

## 📞 Support

This is a reference implementation. For production use:
- Add proper input validation
- Implement rate limiting
- Set up SSL/HTTPS
- Configure email sending
- Add payment gateway integration
- Implement proper error handling
- Set up logging and monitoring

---

© 2024 Auto Spare Workshop. All rights reserved.
