<?php
$pageTitle = 'Manage Categories';
include __DIR__ . '/../includes/header.php';

requireAdmin();

$conn = getDBConnection();

$error = '';
$success = '';

// Handle category deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $categoryId = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
    $stmt->bind_param("i", $categoryId);
    if ($stmt->execute()) {
        setFlashMessage('Category deleted successfully', 'success');
    } else {
        setFlashMessage('Error deleting category', 'error');
    }
    $stmt->close();
    header("Location: /admin/categories.php");
    exit();
}

// Handle add/edit category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $categoryName = sanitize($_POST['category_name']);
    $description = sanitize($_POST['description']);
    
    if (empty($categoryName)) {
        $error = 'Please enter category name.';
    } else {
        if ($categoryId > 0) {
            // Update existing category
            $stmt = $conn->prepare("UPDATE categories SET category_name = ?, description = ? WHERE category_id = ?");
            $stmt->bind_param("ssi", $categoryName, $description, $categoryId);
            
            if ($stmt->execute()) {
                setFlashMessage('Category updated successfully', 'success');
                header("Location: /admin/categories.php");
                exit();
            } else {
                $error = 'Error updating category.';
            }
        } else {
            // Insert new category
            $stmt = $conn->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $categoryName, $description);
            
            if ($stmt->execute()) {
                setFlashMessage('Category added successfully', 'success');
                header("Location: /admin/categories.php");
                exit();
            } else {
                $error = 'Error adding category.';
            }
        }
        $stmt->close();
    }
}

// Get category for editing
$editCategory = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $categoryId = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM categories WHERE category_id = ?");
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $editCategory = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Get all categories with product counts
$categoriesQuery = "SELECT c.*, COUNT(p.product_id) as product_count 
                    FROM categories c 
                    LEFT JOIN products p ON c.category_id = p.category_id 
                    GROUP BY c.category_id 
                    ORDER BY c.category_name";
$categories = $conn->query($categoriesQuery);
?>

<div class="container">
    <h1>Manage Categories</h1>
    <a href="/admin/dashboard.php" class="btn">← Back to Dashboard</a>
    
    <?php if (isset($_GET['action']) && $_GET['action'] === 'add' || $editCategory): ?>
        <div class="card" style="margin-top: 2rem;">
            <h2><?php echo $editCategory ? 'Edit Category' : 'Add New Category'; ?></h2>
            
            <?php if ($error): ?>
                <div class="flash-message flash-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <?php if ($editCategory): ?>
                    <input type="hidden" name="category_id" value="<?php echo $editCategory['category_id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="category_name">Category Name *</label>
                    <input type="text" id="category_name" name="category_name" class="form-control" required 
                           value="<?php echo htmlspecialchars($editCategory['category_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($editCategory['description'] ?? ''); ?></textarea>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $editCategory ? 'Update Category' : 'Add Category'; ?>
                    </button>
                    <a href="/admin/categories.php" class="btn">Cancel</a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div style="margin: 2rem 0;">
            <a href="/admin/categories.php?action=add" class="btn btn-primary">➕ Add New Category</a>
        </div>
        
        <div class="card">
            <h2>All Categories</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($categories->num_rows > 0): ?>
                        <?php while ($category = $categories->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $category['category_id']; ?></td>
                                <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                <td><?php echo htmlspecialchars(substr($category['description'] ?? '', 0, 100)); ?>...</td>
                                <td><?php echo $category['product_count']; ?></td>
                                <td><span class="product-category"><?php echo ucfirst($category['status']); ?></span></td>
                                <td>
                                    <a href="/admin/categories.php?edit=<?php echo $category['category_id']; ?>" class="btn btn-small">Edit</a>
                                    <?php if ($category['product_count'] == 0): ?>
                                        <a href="/admin/categories.php?delete=<?php echo $category['category_id']; ?>" 
                                           class="btn btn-small" 
                                           onclick="return confirm('Are you sure you want to delete this category?');"
                                           style="color: var(--danger-color);">Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No categories found.</td>
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
