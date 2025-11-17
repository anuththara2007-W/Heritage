<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireVendor();

$pageTitle = 'Edit Product - Heritage';
$user_id = getCurrentUserId();
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';
$success = '';

// Get vendor profile
$vendor_query = "SELECT * FROM vendor_profiles WHERE user_id = ?";
$vendor_stmt = mysqli_prepare($conn, $vendor_query);
mysqli_stmt_bind_param($vendor_stmt, "i", $user_id);
mysqli_stmt_execute($vendor_stmt);
$vendor_result = mysqli_stmt_get_result($vendor_stmt);
$vendor = mysqli_fetch_assoc($vendor_result);
$vendor_id = $vendor['vendor_id'];

// Fetch product
$query = "SELECT * FROM products WHERE product_id = ? AND vendor_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $product_id, $vendor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header("Location: products.php");
    exit();
}

// Get categories
$categories_query = "SELECT * FROM categories";
$categories_result = mysqli_query($conn, $categories_query);
$categories = mysqli_fetch_all($categories_result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = sanitizeInput($_POST['product_name']);
    $category_id = intval($_POST['category_id']);
    $description = sanitizeInput($_POST['description']);
    $price = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (empty($product_name) || $category_id < 1 || $price <= 0) {
        $error = "Please fill in all required fields";
    } else {
        $image_url = $product['image_url'];
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = 'product_' . time() . '_' . uniqid() . '.' . $ext;
                $upload_path = '../uploads/products/' . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    // Delete old image
                    if ($image_url && file_exists('../' . $image_url)) {
                        unlink('../' . $image_url);
                    }
                    $image_url = 'uploads/products/' . $new_filename;
                }
            }
        }
        
        $update_query = "UPDATE products SET category_id = ?, product_name = ?, description = ?, price = ?, stock_quantity = ?, image_url = ?, is_active = ? WHERE product_id = ? AND vendor_id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "issdisiii", $category_id, $product_name, $description, $price, $stock_quantity, $image_url, $is_active, $product_id, $vendor_id);
        
        if (mysqli_stmt_execute($update_stmt)) {
            $success = "Product updated successfully!";
            // Refresh product data
            $product['product_name'] = $product_name;
            $product['category_id'] = $category_id;
            $product['description'] = $description;
            $product['price'] = $price;
            $product['stock_quantity'] = $stock_quantity;
            $product['image_url'] = $image_url;
            $product['is_active'] = $is_active;
        } else {
            $error = "Error updating product. Please try again.";
        }
    }
}

include '../includes/header.php';
?>

<div class="container">
    <h2 style="text-align: center; margin: 2rem 0; color: #8B4513;">Edit Product</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <div class="form-container">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="product_name">Product Name *</label>
                <input type="text" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>" <?php echo $category['category_id'] == $product['category_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="price">Price (Rs.) *</label>
                <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="stock_quantity">Stock Quantity *</label>
                <input type="number" id="stock_quantity" name="stock_quantity" min="0" value="<?php echo $product['stock_quantity']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" <?php echo $product['is_active'] ? 'checked' : ''; ?>>
                    Product is active
                </label>
            </div>
            
            <div class="form-group">
                <label for="image">Product Image</label>
                <?php if ($product['image_url']): ?>
                    <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" style="max-width: 300px; margin-bottom: 1rem; border-radius: 5px;">
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this, 'image-preview')">
                <img id="image-preview" style="display: none; margin-top: 1rem; max-width: 300px; border-radius: 5px;">
            </div>
            
            <button type="submit" class="btn" style="width: 100%;">Update Product</button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem;">
            <a href="products.php" style="color: #D2691E;">Back to My Products</a>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
