# Heritage Installation Guide

This guide will walk you through the complete installation process for the Heritage marketplace.

## Table of Contents
1. [System Requirements](#system-requirements)
2. [Installation Steps](#installation-steps)
3. [Configuration](#configuration)
4. [Testing](#testing)
5. [Troubleshooting](#troubleshooting)

## System Requirements

### Minimum Requirements
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher (or MariaDB 10.3+)
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Disk Space**: 500 MB free space
- **RAM**: 512 MB minimum (1 GB recommended)

### PHP Extensions Required
- mysqli
- pdo_mysql
- session
- json
- mbstring
- gd (for image processing)

### Browser Compatibility
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Installation Steps

### 1. Download/Clone the Repository

**Option A: Using Git**
```bash
git clone https://github.com/anuththara2007-W/Heritage.git
cd Heritage
```

**Option B: Download ZIP**
- Download the repository as ZIP from GitHub
- Extract to your web server directory

### 2. Set Up Web Server

#### For XAMPP (Windows/Mac/Linux)

1. Download and install XAMPP from https://www.apachefriends.org/
2. Move the Heritage folder to `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (Mac)
3. Start Apache and MySQL from XAMPP Control Panel

#### For WAMP (Windows)

1. Download and install WAMP from http://www.wampserver.com/
2. Move the Heritage folder to `C:\wamp64\www\`
3. Start WAMP server

#### For LAMP (Linux)

1. Install Apache, MySQL, and PHP:
```bash
sudo apt update
sudo apt install apache2 mysql-server php libapache2-mod-php php-mysql
```

2. Move Heritage to web directory:
```bash
sudo mv Heritage /var/www/html/
sudo chown -R www-data:www-data /var/www/html/Heritage
```

3. Enable Apache mod_rewrite:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 3. Database Setup

#### Using phpMyAdmin

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "New" to create a database
3. Database name: `heritage_db`
4. Collation: `utf8mb4_general_ci`
5. Click "Import" tab
6. Choose file: `database.sql` from Heritage folder
7. Click "Go" to import

#### Using Command Line

```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE heritage_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

# Exit MySQL
exit

# Import schema
mysql -u root -p heritage_db < database.sql
```

### 4. Configure Database Connection

1. Open `config/database.php` in a text editor
2. Update the following values:

```php
define('DB_HOST', 'localhost');      // Database host
define('DB_NAME', 'heritage_db');    // Database name
define('DB_USER', 'root');           // Database username
define('DB_PASS', '');               // Database password (empty for XAMPP/WAMP)
```

3. Save the file

### 5. Set File Permissions

#### For Linux/Mac:

```bash
cd /path/to/Heritage

# Set directory permissions
chmod 755 -R .

# Set write permissions for upload directories
chmod 777 assets/images/products
chmod 777 assets/images/categories
```

#### For Windows:

- Right-click on `assets/images/products` folder
- Properties → Security → Edit
- Give "Full Control" to Users group
- Repeat for `assets/images/categories` folder

### 6. Add Sample Data (Optional)

The database already includes:
- Default admin user
- Sample categories

To add sample products, login as admin and use the product management interface.

## Configuration

### Email Configuration (Optional)

For email notifications, create `config/email.php`:

```php
<?php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('SMTP_FROM', 'noreply@heritage.lk');
?>
```

### Security Hardening

1. **Change Admin Password**
   - Login with default credentials
   - Go to Account Settings
   - Change password immediately

2. **Update Secret Keys**
   - Generate secure session keys
   - Update in `config/session.php`

3. **Enable HTTPS**
   - Obtain SSL certificate
   - Configure web server for HTTPS
   - Update site URLs

## Testing

### 1. Test Customer Flow

1. Open `http://localhost/Heritage/`
2. Register a new account
3. Browse products
4. Add products to cart
5. Complete checkout
6. View orders in account

### 2. Test Admin Panel

1. Open `http://localhost/Heritage/admin/dashboard.php`
2. Login with:
   - Email: `admin@heritage.lk`
   - Password: `admin123`
3. Test product management
4. Test order management
5. Test user management
6. Test review moderation

### 3. Test Security

1. Try accessing admin pages without login (should redirect)
2. Try SQL injection in forms (should be prevented)
3. Test XSS attacks in text fields (should be sanitized)

## Troubleshooting

### Problem: Database Connection Failed

**Solution:**
1. Check if MySQL/MariaDB service is running
2. Verify database credentials in `config/database.php`
3. Ensure database `heritage_db` exists
4. Check MySQL user permissions

```sql
GRANT ALL PRIVILEGES ON heritage_db.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

### Problem: 404 Page Not Found

**Solution:**
1. Check web server is running
2. Verify Heritage folder is in correct directory
3. Check Apache mod_rewrite is enabled (Linux):
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Problem: Images Not Uploading

**Solution:**
1. Check directory permissions:
```bash
chmod 777 assets/images/products
chmod 777 assets/images/categories
```

2. Check PHP upload limits in `php.ini`:
```ini
upload_max_filesize = 10M
post_max_size = 10M
```

3. Restart web server after changes

### Problem: Session Errors

**Solution:**
1. Check PHP session configuration
2. Verify session directory permissions
3. Clear browser cookies
4. Check `php.ini`:
```ini
session.save_path = "/tmp"
session.gc_maxlifetime = 1440
```

### Problem: Blank White Page

**Solution:**
1. Enable PHP error reporting temporarily
2. Add to top of index.php:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

3. Check Apache/PHP error logs:
   - XAMPP: `C:\xampp\apache\logs\error.log`
   - Linux: `/var/log/apache2/error.log`

### Problem: Can't Login as Admin

**Solution:**
1. Verify admin user exists in database:
```sql
SELECT * FROM users WHERE email = 'admin@heritage.lk';
```

2. Reset admin password if needed:
```sql
UPDATE users 
SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE email = 'admin@heritage.lk';
```
(This resets password to: admin123)

## Post-Installation

### 1. Security Checklist
- [ ] Change default admin password
- [ ] Update database credentials
- [ ] Enable HTTPS
- [ ] Set proper file permissions
- [ ] Disable error display in production
- [ ] Enable error logging

### 2. Customization
- [ ] Add your products
- [ ] Upload product images
- [ ] Customize about page content
- [ ] Add your contact information
- [ ] Configure email settings

### 3. Optimization
- [ ] Enable PHP OPcache
- [ ] Configure database query cache
- [ ] Set up CDN for static assets
- [ ] Enable Gzip compression
- [ ] Configure browser caching

## Getting Help

If you encounter issues not covered in this guide:

1. Check the main [README.md](README.md)
2. Search existing GitHub issues
3. Create a new issue with:
   - Detailed description
   - Error messages
   - System information
   - Steps to reproduce

## Next Steps

After successful installation:

1. Read the [README.md](README.md) for usage guide
2. Add products through admin panel
3. Configure site settings
4. Test all functionality
5. Go live!

---

**Congratulations!** Your Heritage marketplace is now installed and ready to use.
