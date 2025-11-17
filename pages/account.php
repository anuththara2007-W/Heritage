<?php
$pageTitle = 'My Account';
include __DIR__ . '/../includes/header.php';

requireLogin();

$conn = getDBConnection();
$userId = getCurrentUserId();

// Get user info
$userStmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$user = $userStmt->get_result()->fetch_assoc();

// Get user orders
$ordersStmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$ordersStmt->bind_param("i", $userId);
$ordersStmt->execute();
$ordersResult = $ordersStmt->get_result();

$error = '';
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullName = sanitize($_POST['full_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $postalCode = sanitize($_POST['postal_code'] ?? '');
    
    $updateStmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, address = ?, city = ?, postal_code = ? WHERE user_id = ?");
    $updateStmt->bind_param("sssssi", $fullName, $phone, $address, $city, $postalCode, $userId);
    
    if ($updateStmt->execute()) {
        $success = 'Profile updated successfully!';
        // Refresh user data
        $userStmt->execute();
        $user = $userStmt->get_result()->fetch_assoc();
    } else {
        $error = 'Error updating profile.';
    }
}

// Handle order cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    $orderId = intval($_POST['order_id']);
    
    $cancelStmt = $conn->prepare("UPDATE orders SET order_status = 'Canceled' WHERE order_id = ? AND user_id = ? AND order_status = 'Pending'");
    $cancelStmt->bind_param("ii", $orderId, $userId);
    
    if ($cancelStmt->execute() && $cancelStmt->affected_rows > 0) {
        $success = 'Order canceled successfully!';
        // Refresh orders
        $ordersStmt->execute();
        $ordersResult = $ordersStmt->get_result();
    } else {
        $error = 'Cannot cancel this order.';
    }
}
?>

<div class="container">
    <h1>My Account</h1>
    
    <?php if ($error): ?>
        <div class="flash-message flash-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="flash-message flash-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
        <div class="card">
            <h2>Profile Information</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" 
                           value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" class="form-control" 
                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" class="form-control" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" class="form-control" 
                           value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="postal_code">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" class="form-control" 
                           value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>">
                </div>
                
                <button type="submit" name="update_profile" class="btn btn-primary btn-block">
                    Update Profile
                </button>
            </form>
        </div>
        
        <div>
            <div class="card">
                <h2>Order History</h2>
                
                <?php if ($ordersResult->num_rows > 0): ?>
                    <?php while ($order = $ordersResult->fetch_assoc()): ?>
                        <div style="border: 1px solid var(--border-color); padding: 1rem; margin-bottom: 1rem; border-radius: 5px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <strong>Order #<?php echo $order['order_id']; ?></strong>
                                <span class="product-category" style="background-color: 
                                    <?php 
                                    echo $order['order_status'] === 'Pending' ? '#ffc107' : 
                                        ($order['order_status'] === 'Shipped' ? '#17a2b8' : 
                                        ($order['order_status'] === 'Delivered' ? '#28a745' : '#dc3545'));
                                    ?>; color: white;">
                                    <?php echo $order['order_status']; ?>
                                </span>
                            </div>
                            <p>Date: <?php echo formatDateTime($order['order_date']); ?></p>
                            <p>Total: <?php echo formatPrice($order['total_amount']); ?></p>
                            <p>Delivery: <?php echo ucfirst($order['delivery_method']); ?></p>
                            <p>Payment: <?php echo ucwords(str_replace('_', ' ', $order['payment_method'])); ?></p>
                            
                            <?php if ($order['order_status'] === 'Pending'): ?>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <button type="submit" name="cancel_order" class="btn btn-small" 
                                            onclick="return confirm('Are you sure you want to cancel this order?');">
                                        Cancel Order
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No orders yet. <a href="/pages/shop.php">Start shopping!</a></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$userStmt->close();
$ordersStmt->close();
closeDBConnection($conn);
include __DIR__ . '/../includes/footer.php';
?>
