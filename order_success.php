<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

requireLogin();
if (!isCustomer()) {
    header("Location: index.php");
    exit();
}

$pageTitle = 'Order Success - Heritage';
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$customer_id = getCurrentUserId();

// Fetch order details
$query = "SELECT * FROM orders WHERE order_id = ? AND customer_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    header("Location: orders.php");
    exit();
}

include 'includes/header.php';
?>

<div class="container">
    <div style="max-width: 600px; margin: 3rem auto; text-align: center; background: white; padding: 3rem; border-radius: 10px; box-shadow: 0 2px 20px rgba(0,0,0,0.1);">
        <div style="width: 80px; height: 80px; background: #d4edda; border-radius: 50%; margin: 0 auto 2rem; display: flex; align-items: center; justify-content: center;">
            <span style="font-size: 3rem; color: #155724;">✓</span>
        </div>
        
        <h2 style="color: #8B4513; margin-bottom: 1rem;">Order Placed Successfully!</h2>
        
        <p style="color: #666; margin-bottom: 2rem; line-height: 1.8;">
            Thank you for your purchase. Your order has been placed successfully.
        </p>
        
        <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 5px; margin-bottom: 2rem;">
            <p><strong>Order ID:</strong> #<?php echo $order_id; ?></p>
            <p><strong>Total Amount:</strong> Rs. <?php echo number_format($order['total_amount'], 2); ?></p>
            <p><strong>Status:</strong> <?php echo ucfirst($order['order_status']); ?></p>
            <p><strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <a href="orders.php" class="btn">View My Orders</a>
            <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
