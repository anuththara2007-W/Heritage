<?php
$pageTitle = 'Featured Collections';
include __DIR__ . '/../includes/header.php';

$conn = getDBConnection();

// Get all collections
$query = "SELECT * FROM collections WHERE status = 'active' ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<div class="container">
    <h1 class="text-center">Featured Collections</h1>
    <p class="text-center">Discover our specially curated collections of Sri Lankan cultural items</p>
    
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($collection = $result->fetch_assoc()): ?>
            <div class="card" style="margin-top: 2rem;">
                <h2><?php echo htmlspecialchars($collection['collection_name']); ?></h2>
                <p><?php echo htmlspecialchars($collection['description'] ?? ''); ?></p>
                
                <?php
                // Get products in this collection
                $collectionId = $collection['collection_id'];
                $productsQuery = "SELECT p.*, c.category_name 
                                  FROM collection_products cp 
                                  JOIN products p ON cp.product_id = p.product_id 
                                  LEFT JOIN categories c ON p.category_id = c.category_id 
                                  WHERE cp.collection_id = ? AND p.status = 'active' 
                                  LIMIT 6";
                $productsStmt = $conn->prepare($productsQuery);
                $productsStmt->bind_param("i", $collectionId);
                $productsStmt->execute();
                $productsResult = $productsStmt->get_result();
                ?>
                
                <div class="products-grid" style="margin-top: 1rem;">
                    <?php while ($product = $productsResult->fetch_assoc()): ?>
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
                                    <?php if (isLoggedIn()): ?>
                                        <button onclick="addToCart(<?php echo $product['product_id']; ?>)" class="btn btn-primary btn-block">
                                            Add to Cart
                                        </button>
                                    <?php else: ?>
                                        <a href="/pages/login.php" class="btn btn-primary btn-block">Login to Buy</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button class="btn btn-block" disabled>Out of Stock</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <?php $productsStmt->close(); ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="card text-center" style="margin-top: 2rem;">
            <h3>No collections available yet</h3>
            <p>Check back soon for our specially curated collections!</p>
            <a href="/pages/shop.php" class="btn btn-primary">Browse All Products</a>
        </div>
    <?php endif; ?>
</div>

<?php
closeDBConnection($conn);
include __DIR__ . '/../includes/footer.php';
?>
