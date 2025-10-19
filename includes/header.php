<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Heritage - Sri Lankan Arts & Crafts Marketplace'; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="nav-brand">
                    <a href="index.php">
                        <h1>Heritage</h1>
                        <p class="tagline">Sri Lankan Arts & Crafts</p>
                    </a>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="about.php">About</a></li>
                    <?php if (isLoggedIn()): ?>
                        <?php if (isVendor()): ?>
                            <li><a href="vendor/dashboard.php">Dashboard</a></li>
                            <li><a href="vendor/products.php">My Products</a></li>
                        <?php else: ?>
                            <li><a href="cart.php">Cart</a></li>
                            <li><a href="orders.php">Orders</a></li>
                        <?php endif; ?>
                        <li><a href="profile.php">Profile</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>
    <main>
