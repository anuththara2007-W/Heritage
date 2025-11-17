<?php
$pageTitle = 'Manage Users';
include __DIR__ . '/../includes/header.php';

requireAdmin();

$conn = getDBConnection();

// Handle user status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $userId = intval($_POST['user_id']);
    $newStatus = sanitize($_POST['status']);
    
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ? AND user_type = 'customer'");
    $stmt->bind_param("si", $newStatus, $userId);
    
    if ($stmt->execute()) {
        setFlashMessage('User status updated successfully', 'success');
    } else {
        setFlashMessage('Error updating user status', 'error');
    }
    $stmt->close();
}

// Get all customer users
$usersQuery = "SELECT * FROM users WHERE user_type = 'customer' ORDER BY created_at DESC";
$users = $conn->query($usersQuery);

// View specific user
$viewUser = null;
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $userId = intval($_GET['view']);
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $viewUser = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($viewUser) {
        // Get user orders
        $ordersStmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
        $ordersStmt->bind_param("i", $userId);
        $ordersStmt->execute();
        $userOrders = $ordersStmt->get_result();
        
        // Get user reviews
        $reviewsStmt = $conn->prepare("SELECT r.*, p.product_name 
                                       FROM reviews r 
                                       JOIN products p ON r.product_id = p.product_id 
                                       WHERE r.user_id = ? 
                                       ORDER BY r.created_at DESC");
        $reviewsStmt->bind_param("i", $userId);
        $reviewsStmt->execute();
        $userReviews = $reviewsStmt->get_result();
    }
}
?>

<div class="container">
    <h1>Manage Users</h1>
    <a href="/admin/dashboard.php" class="btn">← Back to Dashboard</a>
    
    <?php if ($viewUser): ?>
        <div class="card" style="margin-top: 2rem;">
            <h2>User Details: <?php echo htmlspecialchars($viewUser['username']); ?></h2>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div>
                    <h3>Account Information</h3>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($viewUser['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($viewUser['email']); ?></p>
                    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($viewUser['full_name'] ?? 'N/A'); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($viewUser['phone'] ?? 'N/A'); ?></p>
                    <p><strong>Registered:</strong> <?php echo formatDateTime($viewUser['created_at']); ?></p>
                    <p><strong>Status:</strong> 
                        <span class="product-category" style="background-color: <?php echo $viewUser['status'] === 'active' ? 'var(--success-color)' : 'var(--danger-color)'; ?>; color: white;">
                            <?php echo ucfirst($viewUser['status']); ?>
                        </span>
                    </p>
                    
                    <h3 style="margin-top: 2rem;">Address</h3>
                    <p><?php echo nl2br(htmlspecialchars($viewUser['address'] ?? 'Not provided')); ?></p>
                    <p><?php echo htmlspecialchars($viewUser['city'] ?? ''); ?> <?php echo htmlspecialchars($viewUser['postal_code'] ?? ''); ?></p>
                </div>
                
                <div>
                    <h3>Update Status</h3>
                    <form method="POST" action="">
                        <input type="hidden" name="user_id" value="<?php echo $viewUser['user_id']; ?>">
                        <div class="form-group">
                            <label for="status">Account Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="active" <?php echo $viewUser['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo $viewUser['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                    </form>
                </div>
            </div>
            
            <h3 style="margin-top: 2rem;">Order History</h3>
            <?php if ($userOrders->num_rows > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $userOrders->fetch_assoc()): ?>
                            <tr>
                                <td><a href="/admin/orders.php?view=<?php echo $order['order_id']; ?>">#<?php echo $order['order_id']; ?></a></td>
                                <td><?php echo formatDateTime($order['order_date']); ?></td>
                                <td><?php echo formatPrice($order['total_amount']); ?></td>
                                <td><span class="product-category"><?php echo $order['order_status']; ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No orders yet.</p>
            <?php endif; ?>
            
            <h3 style="margin-top: 2rem;">Reviews</h3>
            <?php if ($userReviews->num_rows > 0): ?>
                <?php while ($review = $userReviews->fetch_assoc()): ?>
                    <div style="border-bottom: 1px solid var(--border-color); padding: 1rem 0;">
                        <strong><?php echo htmlspecialchars($review['product_name']); ?></strong>
                        <div style="color: #FFD700;">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php echo $i <= $review['rating'] ? '★' : '☆'; ?>
                            <?php endfor; ?>
                        </div>
                        <p><?php echo nl2br(htmlspecialchars($review['review_text'] ?? '')); ?></p>
                        <small>Status: <?php echo ucfirst($review['status']); ?> | <?php echo formatDate($review['created_at']); ?></small>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No reviews yet.</p>
            <?php endif; ?>
            
            <a href="/admin/users.php" class="btn" style="margin-top: 2rem;">Back to Users List</a>
        </div>
    <?php else: ?>
        <div class="card" style="margin-top: 2rem;">
            <h2>All Customers</h2>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Full Name</th>
                        <th>Registered</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users->num_rows > 0): ?>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['user_id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['full_name'] ?? 'N/A'); ?></td>
                                <td><?php echo formatDate($user['created_at']); ?></td>
                                <td>
                                    <span class="product-category" style="background-color: <?php echo $user['status'] === 'active' ? 'var(--success-color)' : 'var(--danger-color)'; ?>; color: white;">
                                        <?php echo ucfirst($user['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/admin/users.php?view=<?php echo $user['user_id']; ?>" class="btn btn-small">View</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No users found.</td>
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
