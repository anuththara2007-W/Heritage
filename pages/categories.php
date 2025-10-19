<?php
$pageTitle = 'Categories';
include __DIR__ . '/../includes/header.php';

$conn = getDBConnection();

// Get all categories with product counts
$query = "SELECT c.*, COUNT(p.product_id) as product_count 
          FROM categories c 
          LEFT JOIN products p ON c.category_id = p.category_id AND p.status = 'active'
          WHERE c.status = 'active' 
          GROUP BY c.category_id 
          ORDER BY c.category_name";
$result = $conn->query($query);
?>

<div class="container">
    <h1 class="text-center">Product Categories</h1>
    <p class="text-center">Explore our diverse collection of authentic Sri Lankan cultural items</p>
    
    <div class="products-grid" style="margin-top: 2rem;">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($category = $result->fetch_assoc()): ?>
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
                        <p><?php echo htmlspecialchars($category['description'] ?? ''); ?></p>
                        <p><strong><?php echo $category['product_count']; ?></strong> product(s) available</p>
                        <a href="/pages/shop.php?category=<?php echo $category['category_id']; ?>" 
                           class="btn btn-primary btn-block">
                            Browse Category
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center" style="grid-column: 1 / -1;">
                <h3>No categories available</h3>
                <p>Check back soon for our product categories.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
closeDBConnection($conn);
include __DIR__ . '/../includes/footer.php';
?>
