<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/functions.php';

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Heritage - Sri Lankan Cultural Marketplace</title>
    <meta name="description" content="Heritage - Authentic Sri Lankan handicrafts, masks, batik, wood carvings, and cultural items">
    <link rel="stylesheet" href="/assets/css/style.css">
    <?php if (isset($additionalCSS)): ?>
        <link rel="stylesheet" href="<?php echo $additionalCSS; ?>">
    <?php endif; ?>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <nav class="navbar">
                <div class="logo">
                    <a href="/index.php">
                        <h1>Heritage</h1>
                        <span class="tagline">Sri Lankan Cultural Marketplace</span>
                    </a>
                </div>
                
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                
                <ul class="nav-menu" id="navMenu">
                    <li><a href="/index.php" class="<?php echo $currentPage === 'index' ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="/pages/shop.php" class="<?php echo $currentPage === 'shop' ? 'active' : ''; ?>">Shop</a></li>
                    <li><a href="/pages/categories.php" class="<?php echo $currentPage === 'categories' ? 'active' : ''; ?>">Categories</a></li>
                    <li><a href="/pages/collections.php" class="<?php echo $currentPage === 'collections' ? 'active' : ''; ?>">Collections</a></li>
                    <li><a href="/pages/about.php" class="<?php echo $currentPage === 'about' ? 'active' : ''; ?>">About Us</a></li>
                    <li><a href="/pages/contact.php" class="<?php echo $currentPage === 'contact' ? 'active' : ''; ?>">Contact</a></li>
                </ul>
                
                <div class="nav-actions">
                    <?php if (isLoggedIn()): ?>
                        <a href="/pages/account.php" class="nav-icon" title="My Account">
                            <span class="icon">👤</span>
                        </a>
                        <a href="/pages/cart.php" class="nav-icon cart-icon" title="Shopping Cart">
                            <span class="icon">🛒</span>
                            <span class="cart-count" id="cartCount">0</span>
                        </a>
                        <?php if (isAdmin()): ?>
                            <a href="/admin/dashboard.php" class="btn btn-small">Admin</a>
                        <?php endif; ?>
                        <a href="/pages/logout.php" class="btn btn-small">Logout</a>
                    <?php else: ?>
                        <a href="/pages/login.php" class="btn btn-small">Login</a>
                        <a href="/pages/register.php" class="btn btn-small btn-primary">Register</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>
    
    <?php
    $flashMessage = getFlashMessage();
    if ($flashMessage):
    ?>
    <div class="flash-message flash-<?php echo $flashMessage['type']; ?>">
        <div class="container">
            <?php echo htmlspecialchars($flashMessage['message']); ?>
            <button class="close-flash">&times;</button>
        </div>
    </div>
    <?php endif; ?>
    
    <main class="main-content">
