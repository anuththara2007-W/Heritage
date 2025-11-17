<?php
$pageTitle = 'Shop';
include __DIR__ . '/../includes/header.php';

$conn = getDBConnection();

// Get filter parameters
$categoryFilter = isset($_GET['category']) ? intval($_GET['category']) : 0;
$searchQuery = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$minPrice = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$maxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 999999;

// Build query
$query = "SELECT p.*, c.category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.category_id 
          WHERE p.status = 'active'";

if ($categoryFilter > 0) {
    $query .= " AND p.category_id = " . $categoryFilter;
}

if (!empty($searchQuery)) {
    $searchTerm = $conn->real_escape_string($searchQuery);
    $query .= " AND (p.product_name LIKE '%$searchTerm%' OR p.description LIKE '%$searchTerm%')";
}

if ($minPrice > 0 || $maxPrice < 999999) {
    $query .= " AND p.price BETWEEN $minPrice AND $maxPrice";
}

$query .= " ORDER BY p.created_at DESC";

$result = $conn->query($query);

// Get all categories for filter
$categoriesQuery = "SELECT * FROM categories WHERE status = 'active' ORDER BY category_name";
$categoriesResult = $conn->query($categoriesQuery);
?>

<div class="container">
    <h1>Shop All Products</h1>
    
    <div class="card">
        <h3>Filter Products</h3>
        <form method="GET" action="">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div class="form-group">
                    <label for="search">Search</label>
                    <input type="text" id="search" name="search" class="form-control" 
                           placeholder="Search products..." 
                           value="<?php echo htmlspecialchars($searchQuery); ?>">
                </div>
                
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" class="form-control">
                        <option value="">All Categories</option>
                        <?php while ($cat = $categoriesResult->fetch_assoc()): ?>
                            <option value="<?php echo $cat['category_id']; ?>" 
                                    <?php echo $categoryFilter == $cat['category_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="min_price">Min Price (LKR)</label>
                    <input type="number" id="min_price" name="min_price" class="form-control" 
                           min="0" step="0.01" placeholder="0" 
                           value="<?php echo $minPrice > 0 ? $minPrice : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="max_price">Max Price (LKR)</label>
                    <input type="number" id="max_price" name="max_price" class="form-control" 
                           min="0" step="0.01" placeholder="Any" 
                           value="<?php echo $maxPrice < 999999 ? $maxPrice : ''; ?>">
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="/pages/shop.php" class="btn">Clear Filters</a>
            </div>
        </form>
    </div>
    
    <div style="margin: 2rem 0;">
        <p>
            <strong>
                <?php echo $result->num_rows; ?> product(s) found
                <?php if ($searchQuery): ?>
                    for "<?php echo htmlspecialchars($searchQuery); ?>"
                <?php endif; ?>
            </strong>
        </p>
    </div>
    
    <div class="products-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($product = $result->fetch_assoc()): ?>
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
                        <p style="font-size: 0.9rem; color: #666;">
                            <?php echo htmlspecialchars(substr($product['description'] ?? '', 0, 100)); ?>...
                        </p>
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
        <?php else: ?>
            <div class="text-center" style="grid-column: 1 / -1;">
                <h3>No products found</h3>
                <p>Try adjusting your filters or search terms.</p>
                <a href="/pages/shop.php" class="btn btn-primary">View All Products</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
closeDBConnection($conn);
include __DIR__ . '/../includes/footer.php';
?>
