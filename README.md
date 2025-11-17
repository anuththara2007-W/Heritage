# Heritage - Sri Lankan Cultural Marketplace

"Heritage" is a web-based marketplace designed for Sri Lankan vendors and customers for buying local arts and crafts items. It allows local vendors to showcase their products online, while customers can browse, purchase, and review products. This platform promotes local businesses and provides secure, user-friendly shopping experience.

## Features

### Customer Features
- ✅ User registration and login system
- ✅ Browse products with search and filtering
- ✅ View detailed product information with cultural stories
- ✅ Shopping cart functionality
- ✅ Secure checkout process
- ✅ Order tracking and management
- ✅ Product reviews and ratings
- ✅ User account management
- ✅ Contact and support system

### Admin Features
- ✅ Admin dashboard with statistics
- ✅ Product management (add, edit, delete)
- ✅ Category management
- ✅ Order management with status updates
- ✅ User management
- ✅ Review moderation
- ✅ Featured products and collections

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP
- **Database**: MySQL/MariaDB
- **Architecture**: MVC-inspired structure

## Project Structure

```
Heritage/
├── admin/                  # Admin panel pages
│   ├── dashboard.php
│   ├── products.php
│   ├── orders.php
│   ├── users.php
│   ├── categories.php
│   └── reviews.php
├── assets/                 # Static assets
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── images/
│       ├── products/
│       └── categories/
├── config/                 # Configuration files
│   ├── database.php
│   ├── session.php
│   └── functions.php
├── includes/               # Reusable components
│   ├── header.php
│   └── footer.php
├── pages/                  # Customer-facing pages
│   ├── login.php
│   ├── register.php
│   ├── shop.php
│   ├── product.php
│   ├── cart.php
│   ├── checkout.php
│   ├── account.php
│   ├── categories.php
│   ├── collections.php
│   ├── about.php
│   ├── our-story.php
│   ├── contact.php
│   └── reviews.php
├── database.sql            # Database schema
├── index.php              # Homepage
└── README.md              # This file
```

## Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher / MariaDB 10.3 or higher
- Apache/Nginx web server
- Web browser (Chrome, Firefox, Safari, Edge)

### Step 1: Clone/Download the Repository

```bash
git clone https://github.com/anuththara2007-W/Heritage.git
cd Heritage
```

### Step 2: Database Setup

1. Create a new database:
```sql
CREATE DATABASE heritage_db;
```

2. Import the database schema:
```bash
mysql -u your_username -p heritage_db < database.sql
```

Or use phpMyAdmin to import `database.sql`

### Step 3: Configure Database Connection

Edit `config/database.php` and update with your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'heritage_db');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### Step 4: Configure Web Server

#### For Apache:

1. Place the project in your web server directory (e.g., `/var/www/html/` or `htdocs/`)
2. Ensure mod_rewrite is enabled
3. Make sure PHP is configured properly

#### For Nginx:

Add this to your nginx configuration:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/Heritage;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

### Step 5: Set Permissions

```bash
chmod -R 755 /path/to/Heritage
chmod -R 777 /path/to/Heritage/assets/images/products
chmod -R 777 /path/to/Heritage/assets/images/categories
```

### Step 6: Access the Application

Open your browser and navigate to:
- Homepage: `http://localhost/Heritage/` or `http://yourdomain.com/`
- Admin Panel: `http://localhost/Heritage/admin/dashboard.php`

### Default Admin Credentials

```
Email: admin@heritage.lk
Password: admin123
```

**IMPORTANT**: Change the admin password immediately after first login!

## Usage Guide

### For Customers

1. **Register an Account**: Click "Register" and fill in your details
2. **Browse Products**: Navigate to "Shop" to view all products
3. **Filter Products**: Use category filters and search to find specific items
4. **View Product Details**: Click on any product to see full details
5. **Add to Cart**: Click "Add to Cart" on product pages
6. **Checkout**: Review your cart and proceed to checkout
7. **Track Orders**: View order history in "My Account"
8. **Leave Reviews**: Rate and review products you've purchased

### For Administrators

1. **Login**: Access `/admin/dashboard.php` with admin credentials
2. **Manage Products**: Add, edit, or delete products
3. **Manage Categories**: Organize products into categories
4. **Process Orders**: Update order statuses (Pending, Shipped, Delivered, Canceled)
5. **Manage Users**: View and manage customer accounts
6. **Moderate Reviews**: Approve or reject customer reviews

## Security Features

- Password hashing using PHP's password_hash()
- SQL injection prevention using prepared statements
- XSS protection with input sanitization
- Session management for user authentication
- Admin-only access controls
- HTTPS-ready architecture

## Non-Functional Requirements

### Performance
- Pages load within 3 seconds on stable connections
- Handles 100+ concurrent users efficiently
- Optimized database queries

### Security
- Secure password storage
- Protected against common vulnerabilities
- Admin functionality restricted to authorized users

### Usability
- Clean, responsive user interface
- Compatible with all screen sizes (mobile, tablet, desktop)
- Clear navigation and readable fonts

### Reliability
- 24/7 availability design
- Error handling and logging
- Data integrity through transactions

### Maintainability
- Modular code structure
- Well-commented code
- Separation of concerns

### Portability
- Compatible with major browsers (Chrome, Edge, Firefox, Safari)
- Cross-platform (Windows, Linux, macOS)
- Mobile-responsive design

## Future Enhancements

- [ ] Payment gateway integration (PayPal, Stripe)
- [ ] Email notifications for orders
- [ ] Advanced analytics dashboard
- [ ] Multi-language support
- [ ] Social media integration
- [ ] Live chat support with AI bot
- [ ] Wishlist functionality
- [ ] Product recommendations
- [ ] Inventory alerts
- [ ] Export/Import functionality

## Troubleshooting

### Database Connection Issues
- Verify database credentials in `config/database.php`
- Ensure MySQL/MariaDB service is running
- Check that database `heritage_db` exists

### Image Upload Issues
- Verify write permissions on `assets/images/products/` directory
- Check PHP upload limits in `php.ini`
- Ensure proper file extensions (jpg, png, gif)

### Session Issues
- Check PHP session configuration
- Verify session directory permissions
- Clear browser cookies and cache

## Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and inquiries:
- Email: info@heritage.lk
- GitHub Issues: [Create an issue](https://github.com/anuththara2007-W/Heritage/issues)

## Acknowledgments

- Sri Lankan artisans and craftspeople
- Cultural heritage preservation organizations
- Open-source community

---

**Heritage** - Preserving Sri Lankan Culture, One Artifact at a Time 🇱🇰
