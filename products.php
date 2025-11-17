<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

$pageTitle = 'Products - Heritage';

// Get filter parameters
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;

// Build query
$query = "SELECT p.*, vp.shop_name, c.category_name 
          FROM products p 
          JOIN vendor_profiles vp ON p.vendor_id = vp.vendor_id 
          JOIN categories c ON p.category_id = c.category_id 
          WHERE p.is_active = 1";

if ($search) {
    $query .= " AND (p.product_name LIKE '%$search%' OR p.description LIKE '%$search%')";
}

if ($category_id > 0) {
    $query .= " AND p.category_id = $category_id";
}

$query .= " ORDER BY p.created_at DESC";

$result = mysqli_query($conn, $query);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get categories for filter
$categories_query = "SELECT * FROM categories";
$categories_result = mysqli_query($conn, $categories_query);
$categories = mysqli_fetch_all($categories_result, MYSQLI_ASSOC);

include 'includes/header.php';
?>

<div class="container">
    <h2 style="text-align: center; margin: 2rem 0; color: #8B4513;">Browse Products</h2>
    
    <!-- Search and Filter -->
    <div class="search-filter">
        <form method="GET" action="products.php">
            <input type="text" 
                   name="search" 
                   placeholder="Search products..." 
                   value="<?php echo htmlspecialchars($search); ?>">
            
            <select name="category" id="category-filter">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['category_id']; ?>" 
                            <?php echo $category_id == $category['category_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['category_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" class="btn">Search</button>
        </form>
    </div>
    
    <!-- Products Grid -->
    <?php if (count($products) > 0): ?>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?php echo $product['image_url'] ? htmlspecialchars($product['image_url']) : 'images/placeholder.jpg'; ?>" 
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                         class="product-image">
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p style="color: #666; font-size: 0.9rem;">
                            <?php echo htmlspecialchars($category['category_name'] ?? ''); ?> | 
                            by <?php echo htmlspecialchars($product['shop_name']); ?>
                        </p>
                        <p class="product-price">Rs. <?php echo number_format($product['price'], 2); ?></p>
                        <p class="product-description">
                            <?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>...
                        </p>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" 
                               class="btn" style="flex: 1; text-align: center;">View Details</a>
                            <?php if (isCustomer()): ?>
                                <button onclick="addToCart(<?php echo $product['product_id']; ?>)" 
                                        class="btn btn-secondary">Add to Cart</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 3rem; background: white; border-radius: 10px;">
            <h3 style="color: #666;">No products found</h3>
            <p>Try adjusting your search or filter criteria</p>
            <a href="products.php" class="btn" style="margin-top: 1rem;">View All Products</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
