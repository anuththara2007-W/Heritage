<?php
$pageTitle = 'Home';
include __DIR__ . '/includes/header.php';

$conn = getDBConnection();

// Get featured products
$featuredQuery = "SELECT p.*, c.category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.category_id 
                  WHERE p.featured = 1 AND p.status = 'active' 
                  LIMIT 6";
$featuredResult = $conn->query($featuredQuery);

// Get categories
$categoriesQuery = "SELECT * FROM categories WHERE status = 'active' LIMIT 6";
$categoriesResult = $conn->query($categoriesQuery);
?>

<section class="hero">
    <div class="container">
        <h1>Welcome to Heritage</h1>
        <p>Discover Authentic Sri Lankan Cultural Items & Handicrafts</p>
        <p>Supporting Local Artisans • Preserving Cultural Heritage • Worldwide Delivery</p>
        <a href="/pages/shop.php" class="btn btn-large btn-primary">Shop Now</a>
    </div>
</section>

<div class="container">
    <section class="featured-products">
        <h2 class="text-center">Featured Products</h2>
        <p class="text-center mb-2">Handpicked selection of our finest Sri Lankan cultural items</p>
        
        <div class="products-grid">
            <?php if ($featuredResult && $featuredResult->num_rows > 0): ?>
                <?php while ($product = $featuredResult->fetch_assoc()): ?>
                    <div class="product-card">
                        <a href="/pages/product.php?id=<?php echo $product['product_id']; ?>">
                            <?php if ($product['image_url']): ?>
                                <img src="/assets/images/products/<?php echo htmlspecialchars($product['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                     class="product-image">
                            <?php else: ?>
                                <img src="/assets/images/placeholder.jpg" 
                                     alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                     class="product-image">
                            <?php endif; ?>
                        </a>
                        <div class="product-info">
                            <span class="product-category"><?php echo htmlspecialchars($product['category_name'] ?? 'General'); ?></span>
                            <h3 class="product-name">
                                <a href="/pages/product.php?id=<?php echo $product['product_id']; ?>">
                                    <?php echo htmlspecialchars($product['product_name']); ?>
                                </a>
                            </h3>
                            <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                            <?php if ($product['stock_quantity'] > 0): ?>
                                <button onclick="addToCart(<?php echo $product['product_id']; ?>)" class="btn btn-primary btn-block">
                                    Add to Cart
                                </button>
                            <?php else: ?>
                                <button class="btn btn-block" disabled>Out of Stock</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">No featured products available at the moment.</p>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-2">
            <a href="/pages/shop.php" class="btn btn-primary">View All Products</a>
        </div>
    </section>
    
    <section class="categories mt-2">
        <h2 class="text-center">Shop by Category</h2>
        <p class="text-center mb-2">Explore our diverse collection of Sri Lankan cultural items</p>
        
        <div class="products-grid">
            <?php if ($categoriesResult && $categoriesResult->num_rows > 0): ?>
                <?php while ($category = $categoriesResult->fetch_assoc()): ?>
                    <div class="product-card">
                        <a href="/pages/shop.php?category=<?php echo $category['category_id']; ?>">
                            <?php if ($category['image_url']): ?>
                                <img src="/assets/images/categories/<?php echo htmlspecialchars($category['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($category['category_name']); ?>" 
                                     class="product-image">
                            <?php else: ?>
                                <img src="/assets/images/placeholder.jpg" 
                                     alt="<?php echo htmlspecialchars($category['category_name']); ?>" 
                                     class="product-image">
                            <?php endif; ?>
                        </a>
                        <div class="product-info">
                            <h3 class="product-name">
                                <a href="/pages/shop.php?category=<?php echo $category['category_id']; ?>">
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </a>
                            </h3>
                            <p><?php echo htmlspecialchars(substr($category['description'] ?? '', 0, 100)); ?>...</p>
                            <a href="/pages/shop.php?category=<?php echo $category['category_id']; ?>" class="btn btn-block">
                                Browse Category
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">No categories available at the moment.</p>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-2">
            <a href="/pages/categories.php" class="btn btn-primary">View All Categories</a>
        </div>
    </section>
    
    <section class="about-preview mt-2 card">
        <h2 class="text-center">About Heritage</h2>
        <p class="text-center">
            Heritage is a web-based marketplace designed for Sri Lankan vendors and customers for buying local arts and crafts items. 
            We allow local vendors to showcase their products online, while customers can browse, purchase, and review products. 
            This platform promotes local businesses and provides a secure, user-friendly shopping experience.
        </p>
        <div class="text-center">
            <a href="/pages/about.php" class="btn btn-primary">Learn More</a>
            <a href="/pages/our-story.php" class="btn">Our Story</a>
        </div>
    </section>
</div>

<?php
closeDBConnection($conn);
include __DIR__ . '/includes/footer.php';
?>
