<?php
$pageTitle = 'Manage Orders';
include __DIR__ . '/../includes/header.php';

requireAdmin();

$conn = getDBConnection();

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = intval($_POST['order_id']);
    $newStatus = sanitize($_POST['order_status']);
    
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $newStatus, $orderId);
    
    if ($stmt->execute()) {
        setFlashMessage('Order status updated successfully', 'success');
    } else {
        setFlashMessage('Error updating order status', 'error');
    }
    $stmt->close();
}

// Get filter
$statusFilter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

// Get orders
$query = "SELECT o.*, u.username, u.email 
          FROM orders o 
          JOIN users u ON o.user_id = u.user_id";

if (!empty($statusFilter)) {
    $query .= " WHERE o.order_status = '" . $conn->real_escape_string($statusFilter) . "'";
}

$query .= " ORDER BY o.order_date DESC";
$orders = $conn->query($query);

// View specific order
$viewOrder = null;
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $orderId = intval($_GET['view']);
    
    // Get order details
    $stmt = $conn->prepare("SELECT o.*, u.username, u.email, u.phone 
                           FROM orders o 
                           JOIN users u ON o.user_id = u.user_id 
                           WHERE o.order_id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $viewOrder = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    // Get order items
    if ($viewOrder) {
        $itemsStmt = $conn->prepare("SELECT oi.*, p.product_name 
                                     FROM order_items oi 
                                     JOIN products p ON oi.product_id = p.product_id 
                                     WHERE oi.order_id = ?");
        $itemsStmt->bind_param("i", $orderId);
        $itemsStmt->execute();
        $orderItems = $itemsStmt->get_result();
    }
}
?>

<div class="container">
    <h1>Manage Orders</h1>
    <a href="/admin/dashboard.php" class="btn">← Back to Dashboard</a>
    
    <?php if ($viewOrder): ?>
        <div class="card" style="margin-top: 2rem;">
            <h2>Order #<?php echo $viewOrder['order_id']; ?></h2>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div>
                    <h3>Customer Information</h3>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($viewOrder['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($viewOrder['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($viewOrder['phone']); ?></p>
                    
                    <h3 style="margin-top: 2rem;">Shipping Information</h3>
                    <p><?php echo nl2br(htmlspecialchars($viewOrder['shipping_address'])); ?></p>
                    
                    <h3 style="margin-top: 2rem;">Order Details</h3>
                    <p><strong>Date:</strong> <?php echo formatDateTime($viewOrder['order_date']); ?></p>
                    <p><strong>Delivery Method:</strong> <?php echo ucfirst($viewOrder['delivery_method']); ?></p>
                    <p><strong>Payment Method:</strong> <?php echo ucwords(str_replace('_', ' ', $viewOrder['payment_method'])); ?></p>
                </div>
                
                <div>
                    <h3>Update Status</h3>
                    <form method="POST" action="">
                        <input type="hidden" name="order_id" value="<?php echo $viewOrder['order_id']; ?>">
                        <div class="form-group">
                            <label for="order_status">Order Status</label>
                            <select id="order_status" name="order_status" class="form-control">
                                <option value="Pending" <?php echo $viewOrder['order_status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Shipped" <?php echo $viewOrder['order_status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="Delivered" <?php echo $viewOrder['order_status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="Canceled" <?php echo $viewOrder['order_status'] === 'Canceled' ? 'selected' : ''; ?>>Canceled</option>
                            </select>
                        </div>
                        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                    </form>
                </div>
            </div>
            
            <h3 style="margin-top: 2rem;">Order Items</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $orderItems->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo formatPrice($item['price']); ?></td>
                            <td><?php echo formatPrice($item['subtotal']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <tr style="font-weight: bold; border-top: 2px solid var(--border-color);">
                        <td colspan="3" class="text-right">Total:</td>
                        <td><?php echo formatPrice($viewOrder['total_amount']); ?></td>
                    </tr>
                </tbody>
            </table>
            
            <a href="/admin/orders.php" class="btn">Back to Orders List</a>
        </div>
    <?php else: ?>
        <div class="card" style="margin-top: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2>All Orders</h2>
                <div>
                    <a href="/admin/orders.php" class="btn btn-small <?php echo empty($statusFilter) ? 'btn-primary' : ''; ?>">All</a>
                    <a href="/admin/orders.php?status=Pending" class="btn btn-small <?php echo $statusFilter === 'Pending' ? 'btn-primary' : ''; ?>">Pending</a>
                    <a href="/admin/orders.php?status=Shipped" class="btn btn-small <?php echo $statusFilter === 'Shipped' ? 'btn-primary' : ''; ?>">Shipped</a>
                    <a href="/admin/orders.php?status=Delivered" class="btn btn-small <?php echo $statusFilter === 'Delivered' ? 'btn-primary' : ''; ?>">Delivered</a>
                    <a href="/admin/orders.php?status=Canceled" class="btn btn-small <?php echo $statusFilter === 'Canceled' ? 'btn-primary' : ''; ?>">Canceled</a>
                </div>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orders->num_rows > 0): ?>
                        <?php while ($order = $orders->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $order['order_id']; ?></td>
                                <td><?php echo htmlspecialchars($order['username']); ?></td>
                                <td><?php echo formatDateTime($order['order_date']); ?></td>
                                <td><?php echo formatPrice($order['total_amount']); ?></td>
                                <td><span class="product-category"><?php echo $order['order_status']; ?></span></td>
                                <td>
                                    <a href="/admin/orders.php?view=<?php echo $order['order_id']; ?>" class="btn btn-small">View</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No orders found.</td>
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
