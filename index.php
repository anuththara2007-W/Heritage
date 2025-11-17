<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

$pageTitle = 'Heritage - Sri Lankan Arts & Crafts Marketplace';
include 'includes/header.php';

// Fetch featured products
$query = "SELECT p.*, vp.shop_name, c.category_name 
          FROM products p 
          JOIN vendor_profiles vp ON p.vendor_id = vp.vendor_id 
          JOIN categories c ON p.category_id = c.category_id 
          WHERE p.is_active = 1 
          ORDER BY p.created_at DESC 
          LIMIT 8";
$result = mysqli_query($conn, $query);
$featured_products = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Fetch categories
$categories_query = "SELECT * FROM categories";
$categories_result = mysqli_query($conn, $categories_query);
$categories = mysqli_fetch_all($categories_result, MYSQLI_ASSOC);
?>

<div class="hero">
    <div class="container">
        <h2>Discover Authentic Sri Lankan Arts & Crafts</h2>
        <p>Supporting local artisans and promoting traditional craftsmanship</p>
        <a href="products.php" class="btn">Browse Products</a>
        <?php if (!isLoggedIn()): ?>
            <a href="register.php?type=vendor" class="btn btn-secondary">Become a Vendor</a>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <!-- Categories Section -->
    <section class="categories-section">
        <h2 style="text-align: center; margin-bottom: 1rem; color: #8B4513;">Shop by Category</h2>
        <div class="categories-grid">
            <?php foreach ($categories as $category): ?>
                <a href="products.php?category=<?php echo $category['category_id']; ?>" class="category-card">
                    <h3><?php echo htmlspecialchars($category['category_name']); ?></h3>
                    <p><?php echo htmlspecialchars($category['description']); ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section style="margin-top: 3rem;">
        <h2 style="text-align: center; margin-bottom: 2rem; color: #8B4513;">Featured Products</h2>
        
        <?php if (count($featured_products) > 0): ?>
            <div class="products-grid">
                <?php foreach ($featured_products as $product): ?>
                    <div class="product-card">
                        <img src="<?php echo $product['image_url'] ? htmlspecialchars($product['image_url']) : 'images/placeholder.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                             class="product-image">
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                            <p style="color: #666; font-size: 0.9rem;">by <?php echo htmlspecialchars($product['shop_name']); ?></p>
                            <p class="product-price">Rs. <?php echo number_format($product['price'], 2); ?></p>
                            <p class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>...</p>
                            <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="btn" style="width: 100%; text-align: center;">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #666;">No products available at the moment. Check back soon!</p>
        <?php endif; ?>
    </section>

    <!-- About Section -->
    <section style="margin-top: 4rem; background: white; padding: 3rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; margin-bottom: 2rem; color: #8B4513;">About Heritage</h2>
        <p style="text-align: center; max-width: 800px; margin: 0 auto; line-height: 1.8;">
            Heritage is a dedicated marketplace for Sri Lankan artisans and craft makers to showcase their traditional 
            arts and crafts to a wider audience. We believe in preserving our cultural heritage while providing local 
            vendors with a platform to grow their businesses. From handcrafted pottery to intricate batik designs, 
            discover authentic Sri Lankan craftsmanship at Heritage.
        </p>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
