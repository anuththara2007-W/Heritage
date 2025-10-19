<?php
$pageTitle = 'Manage Products';
include __DIR__ . '/../includes/header.php';

requireAdmin();

$conn = getDBConnection();

$error = '';
$success = '';

// Handle product deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $productId = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $productId);
    if ($stmt->execute()) {
        setFlashMessage('Product deleted successfully', 'success');
    } else {
        setFlashMessage('Error deleting product', 'error');
    }
    $stmt->close();
    header("Location: /admin/products.php");
    exit();
}

// Handle add/edit product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $categoryId = intval($_POST['category_id']);
    $productName = sanitize($_POST['product_name']);
    $description = sanitize($_POST['description']);
    $story = sanitize($_POST['story'] ?? '');
    $price = floatval($_POST['price']);
    $stockQuantity = intval($_POST['stock_quantity']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    if (empty($productName) || $price <= 0) {
        $error = 'Please fill in all required fields with valid values.';
    } else {
        if ($productId > 0) {
            // Update existing product
            $stmt = $conn->prepare("UPDATE products SET category_id = ?, product_name = ?, description = ?, story = ?, price = ?, stock_quantity = ?, featured = ? WHERE product_id = ?");
            $stmt->bind_param("isssdiit", $categoryId, $productName, $description, $story, $price, $stockQuantity, $featured, $productId);
            
            if ($stmt->execute()) {
                setFlashMessage('Product updated successfully', 'success');
                header("Location: /admin/products.php");
                exit();
            } else {
                $error = 'Error updating product.';
            }
        } else {
            // Insert new product
            $stmt = $conn->prepare("INSERT INTO products (category_id, product_name, description, story, price, stock_quantity, featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssdii", $categoryId, $productName, $description, $story, $price, $stockQuantity, $featured);
            
            if ($stmt->execute()) {
                setFlashMessage('Product added successfully', 'success');
                header("Location: /admin/products.php");
                exit();
            } else {
                $error = 'Error adding product.';
            }
        }
        $stmt->close();
    }
}

// Get product for editing
$editProduct = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $productId = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $editProduct = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Get all products
$productsQuery = "SELECT p.*, c.category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.category_id 
                  ORDER BY p.created_at DESC";
$products = $conn->query($productsQuery);

// Get categories for dropdown
$categoriesQuery = "SELECT * FROM categories WHERE status = 'active' ORDER BY category_name";
$categories = $conn->query($categoriesQuery);
?>

<div class="container">
    <h1>Manage Products</h1>
    <a href="/admin/dashboard.php" class="btn">← Back to Dashboard</a>
    
    <?php if (isset($_GET['action']) && $_GET['action'] === 'add' || $editProduct): ?>
        <div class="card" style="margin-top: 2rem;">
            <h2><?php echo $editProduct ? 'Edit Product' : 'Add New Product'; ?></h2>
            
            <?php if ($error): ?>
                <div class="flash-message flash-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <?php if ($editProduct): ?>
                    <input type="hidden" name="product_id" value="<?php echo $editProduct['product_id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="product_name">Product Name *</label>
                    <input type="text" id="product_name" name="product_name" class="form-control" required 
                           value="<?php echo htmlspecialchars($editProduct['product_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category *</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php 
                        $categories->data_seek(0);
                        while ($cat = $categories->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $cat['category_id']; ?>" 
                                    <?php echo ($editProduct && $editProduct['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($editProduct['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="story">Cultural Story (Optional)</label>
                    <textarea id="story" name="story" class="form-control" rows="4"><?php echo htmlspecialchars($editProduct['story'] ?? ''); ?></textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="price">Price (LKR) *</label>
                        <input type="number" id="price" name="price" class="form-control" step="0.01" min="0" required 
                               value="<?php echo $editProduct['price'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="stock_quantity">Stock Quantity *</label>
                        <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" min="0" required 
                               value="<?php echo $editProduct['stock_quantity'] ?? '0'; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="featured" <?php echo ($editProduct && $editProduct['featured']) ? 'checked' : ''; ?>>
                        Feature this product on homepage
                    </label>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $editProduct ? 'Update Product' : 'Add Product'; ?>
                    </button>
                    <a href="/admin/products.php" class="btn">Cancel</a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div style="margin: 2rem 0;">
            <a href="/admin/products.php?action=add" class="btn btn-primary">➕ Add New Product</a>
        </div>
        
        <div class="card">
            <h2>All Products</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($products->num_rows > 0): ?>
                        <?php while ($product = $products->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $product['product_id']; ?></td>
                                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                                <td><?php echo formatPrice($product['price']); ?></td>
                                <td><?php echo $product['stock_quantity']; ?></td>
                                <td><?php echo $product['featured'] ? '⭐' : ''; ?></td>
                                <td>
                                    <a href="/admin/products.php?edit=<?php echo $product['product_id']; ?>" class="btn btn-small">Edit</a>
                                    <a href="/admin/products.php?delete=<?php echo $product['product_id']; ?>" 
                                       class="btn btn-small" 
                                       onclick="return confirm('Are you sure you want to delete this product?');"
                                       style="color: var(--danger-color);">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
closeDBConnection($conn);
include __DIR__ . '/../includes/footer.php';
?>
