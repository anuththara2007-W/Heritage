# Heritage Marketplace - Quick Start Guide

## What is Heritage?

Heritage is a complete web-based marketplace platform that connects Sri Lankan artisans and vendors with customers interested in authentic local arts and crafts. Built with HTML, CSS, JavaScript, PHP, and MySQL.

## Quick Setup (3 Steps)

### Step 1: Database Setup
```bash
mysql -u root -p < database/heritage_db.sql
```

### Step 2: Configure Database
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'heritage_db');
```

### Step 3: Set Permissions
```bash
chmod 755 uploads/products uploads/logos
```

## Access the Platform
Open browser: `http://localhost/Heritage/`

## User Roles

### Customer Account
- Browse and search products
- Add items to cart
- Purchase products
- Write reviews
- Track orders

### Vendor Account
- Create shop profile
- Add/manage products
- Upload product images
- View orders
- Track sales

## Product Categories

1. **Handcrafts** - Traditional handcrafted items
2. **Paintings** - Art by local artists
3. **Pottery** - Ceramic and pottery items
4. **Textiles** - Traditional fabrics
5. **Jewelry** - Handmade accessories
6. **Wood Carvings** - Wooden crafts
7. **Batik** - Traditional batik art

## Key Features

✅ Secure user authentication
✅ Shopping cart with AJAX
✅ Product search & filtering
✅ Review and rating system
✅ Order management
✅ Vendor dashboard
✅ Image uploads
✅ Mobile responsive design
✅ SQL injection protection
✅ Password encryption

## Project Structure

```
Heritage/
├── index.php              # Homepage
├── products.php           # Product listing
├── product_detail.php     # Product details & reviews
├── login.php              # User login
├── register.php           # User registration
├── cart.php               # Shopping cart
├── checkout.php           # Checkout
├── orders.php             # Order history
├── profile.php            # User profile
├── about.php              # About page
│
├── vendor/                # Vendor-specific pages
│   ├── dashboard.php      # Statistics & overview
│   ├── products.php       # Manage products
│   ├── add_product.php    # Add new product
│   ├── edit_product.php   # Edit product
│   └── orders.php         # View orders
│
├── config/
│   └── database.php       # DB connection
│
├── includes/
│   ├── header.php         # Common header
│   ├── footer.php         # Common footer
│   └── auth.php           # Auth functions
│
├── css/
│   └── style.css          # Main stylesheet
│
├── js/
│   └── main.js            # JavaScript
│
├── database/
│   └── heritage_db.sql    # Database schema
│
└── uploads/               # Product images
    ├── products/
    └── logos/
```

## Testing the Platform

### As Customer:
1. Visit `register.php`
2. Create customer account
3. Browse products at `products.php`
4. Add items to cart
5. Checkout and place order
6. View order history at `orders.php`

### As Vendor:
1. Visit `register.php?type=vendor`
2. Create vendor account with shop details
3. Access dashboard at `vendor/dashboard.php`
4. Add products at `vendor/add_product.php`
5. View orders at `vendor/orders.php`

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 7.0+
- **Database**: MySQL 5.6+
- **Server**: Apache/Nginx

## Security Features

- ✅ Password hashing (bcrypt)
- ✅ Prepared SQL statements
- ✅ Input sanitization
- ✅ XSS protection
- ✅ Session security
- ✅ File upload validation

## Color Theme

- Primary: #8B4513 (Traditional brown)
- Secondary: #D2691E (Orange-brown)
- Background: #f8f9fa
- Accent: #FFD700 (Gold stars)

## Support

Need help? Check out:
- `README.md` - Full documentation
- `IMPLEMENTATION.md` - Technical details
- Database schema in `database/heritage_db.sql`

## Future Enhancements

Consider adding:
- Payment gateway integration
- Email notifications
- Admin dashboard
- Advanced analytics
- Multi-language support
- Mobile app

---

**Built for preserving Sri Lankan heritage and supporting local artisans!** 🎨
