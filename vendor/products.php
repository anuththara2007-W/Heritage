<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireVendor();

$pageTitle = 'My Products - Heritage';
$user_id = getCurrentUserId();

// Get vendor profile
$vendor_query = "SELECT * FROM vendor_profiles WHERE user_id = ?";
$vendor_stmt = mysqli_prepare($conn, $vendor_query);
mysqli_stmt_bind_param($vendor_stmt, "i", $user_id);
mysqli_stmt_execute($vendor_stmt);
$vendor_result = mysqli_stmt_get_result($vendor_stmt);
$vendor = mysqli_fetch_assoc($vendor_result);
$vendor_id = $vendor['vendor_id'];

// Fetch vendor products
$query = "SELECT p.*, c.category_name FROM products p JOIN categories c ON p.category_id = c.category_id WHERE p.vendor_id = ? ORDER BY p.created_at DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $vendor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

include '../includes/header.php';
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin: 2rem 0;">
        <h2 style="color: #8B4513;">My Products</h2>
        <a href="add_product.php" class="btn">Add New Product</a>
    </div>
    
    <?php if (count($products) > 0): ?>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?php echo $product['image_url'] ? '../' . htmlspecialchars($product['image_url']) : '../images/placeholder.jpg'; ?>" 
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                         class="product-image">
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p style="color: #666; font-size: 0.9rem;"><?php echo htmlspecialchars($product['category_name']); ?></p>
                        <p class="product-price">Rs. <?php echo number_format($product['price'], 2); ?></p>
                        <p style="color: #666; font-size: 0.9rem;">Stock: <?php echo $product['stock_quantity']; ?></p>
                        <p style="color: #666; font-size: 0.9rem;">
                            Status: <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                        </p>
                        <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                            <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn" style="flex: 1; text-align: center;">Edit</a>
                            <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" 
                               class="btn btn-secondary" 
                               onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 3rem; background: white; border-radius: 10px;">
            <h3 style="color: #666;">No products yet</h3>
            <p style="margin: 1rem 0;">Start adding products to showcase your crafts</p>
            <a href="add_product.php" class="btn">Add Your First Product</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
