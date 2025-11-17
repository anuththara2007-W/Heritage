<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireVendor();

$pageTitle = 'Add Product - Heritage';
$user_id = getCurrentUserId();
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
    
    if (empty($product_name) || $category_id < 1 || $price <= 0) {
        $error = "Please fill in all required fields";
    } else {
        $image_url = '';
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = 'product_' . time() . '_' . uniqid() . '.' . $ext;
                $upload_path = '../uploads/products/' . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_url = 'uploads/products/' . $new_filename;
                }
            }
        }
        
        $insert_query = "INSERT INTO products (vendor_id, category_id, product_name, description, price, stock_quantity, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt, "iissdis", $vendor_id, $category_id, $product_name, $description, $price, $stock_quantity, $image_url);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Product added successfully!";
        } else {
            $error = "Error adding product. Please try again.";
        }
    }
}

include '../includes/header.php';
?>

<div class="container">
    <h2 style="text-align: center; margin: 2rem 0; color: #8B4513;">Add New Product</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
            <a href="products.php">View all products</a>
        </div>
    <?php endif; ?>
    
    <div class="form-container">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="product_name">Product Name *</label>
                <input type="text" id="product_name" name="product_name" required>
            </div>
            
            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>">
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="5"></textarea>
            </div>
            
            <div class="form-group">
                <label for="price">Price (Rs.) *</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="stock_quantity">Stock Quantity *</label>
                <input type="number" id="stock_quantity" name="stock_quantity" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this, 'image-preview')">
                <img id="image-preview" style="display: none; margin-top: 1rem; max-width: 300px; border-radius: 5px;">
            </div>
            
            <button type="submit" class="btn" style="width: 100%;">Add Product</button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem;">
            <a href="products.php" style="color: #D2691E;">Back to My Products</a>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
