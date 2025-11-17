# Heritage Marketplace

Welcome to Heritage - a web-based marketplace for Sri Lankan vendors and customers to buy and sell traditional arts and crafts.

## Features

### For Customers
- Browse authentic Sri Lankan arts and crafts
- Search and filter products by category
- View detailed product information with images
- Read and write product reviews
- Add products to shopping cart
- Secure checkout process
- Track order history
- User profile management

### For Vendors
- Register and create shop profile
- Add and manage products
- Upload product images
- Track inventory and stock
- View and manage orders
- Dashboard with sales statistics
- Product categories: Handcrafts, Paintings, Pottery, Textiles, Jewelry, Wood Carvings, Batik

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **Features**: Responsive design, user authentication, shopping cart, order management, review system

## Installation

1. **Database Setup**
   ```bash
   mysql -u root -p < database/heritage_db.sql
   ```

2. **Configure Database Connection**
   Edit `config/database.php` and update the database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'heritage_db');
   ```

3. **Web Server Setup**
   - Place the project files in your web server's document root
   - Ensure PHP is installed and configured
   - Make sure the `uploads/` directory has write permissions

4. **Access the Application**
   Open your web browser and navigate to: http://localhost/Heritage/

## User Types

1. **Customer**: Can browse products, add to cart, purchase items, and write reviews
2. **Vendor**: Can create shop, add/manage products, view orders, and track sales

## Security Features

- Password hashing using PHP's password_hash()
- SQL injection prevention using prepared statements
- Input sanitization
- Session management for authentication

---

Built with ❤️ for preserving Sri Lankan heritage and supporting local artisans.
