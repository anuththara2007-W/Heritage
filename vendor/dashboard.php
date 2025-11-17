<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireVendor();

$pageTitle = 'Vendor Dashboard - Heritage';
$user_id = getCurrentUserId();

// Get vendor profile
$vendor_query = "SELECT * FROM vendor_profiles WHERE user_id = ?";
$vendor_stmt = mysqli_prepare($conn, $vendor_query);
mysqli_stmt_bind_param($vendor_stmt, "i", $user_id);
mysqli_stmt_execute($vendor_stmt);
$vendor_result = mysqli_stmt_get_result($vendor_stmt);
$vendor = mysqli_fetch_assoc($vendor_result);

$vendor_id = $vendor['vendor_id'];

// Get statistics
$products_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products WHERE vendor_id = $vendor_id"))['count'];
$orders_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT o.order_id) as count FROM orders o JOIN order_items oi ON o.order_id = oi.order_id JOIN products p ON oi.product_id = p.product_id WHERE p.vendor_id = $vendor_id"))['count'];
$revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(oi.price * oi.quantity), 0) as total FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE p.vendor_id = $vendor_id"))['total'];

// Get recent orders
$recent_orders_query = "SELECT DISTINCT o.*, u.full_name 
                       FROM orders o 
                       JOIN order_items oi ON o.order_id = oi.order_id 
                       JOIN products p ON oi.product_id = p.product_id 
                       JOIN users u ON o.customer_id = u.user_id
                       WHERE p.vendor_id = ? 
                       ORDER BY o.created_at DESC 
                       LIMIT 5";
$orders_stmt = mysqli_prepare($conn, $recent_orders_query);
mysqli_stmt_bind_param($orders_stmt, "i", $vendor_id);
mysqli_stmt_execute($orders_stmt);
$recent_orders = mysqli_fetch_all(mysqli_stmt_get_result($orders_stmt), MYSQLI_ASSOC);

include '../includes/header.php';
?>

<div class="container">
    <h2 style="text-align: center; margin: 2rem 0; color: #8B4513;">Vendor Dashboard</h2>
    
    <div style="background: white; padding: 2rem; border-radius: 10px; margin-bottom: 2rem;">
        <h3 style="color: #8B4513; margin-bottom: 1rem;">Welcome, <?php echo htmlspecialchars($vendor['shop_name']); ?>!</h3>
        <p style="color: #666;"><?php echo htmlspecialchars($vendor['shop_description'] ?? 'No description added yet'); ?></p>
    </div>
    
    <!-- Statistics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
        <div style="background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%); color: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h4 style="margin-bottom: 0.5rem;">Total Products</h4>
            <p style="font-size: 2.5rem; font-weight: bold; margin: 0;"><?php echo $products_count; ?></p>
        </div>
        
        <div style="background: linear-gradient(135deg, #28a745 0%, #5cb85c 100%); color: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h4 style="margin-bottom: 0.5rem;">Total Orders</h4>
            <p style="font-size: 2.5rem; font-weight: bold; margin: 0;"><?php echo $orders_count; ?></p>
        </div>
        
        <div style="background: linear-gradient(135deg, #17a2b8 0%, #5bc0de 100%); color: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h4 style="margin-bottom: 0.5rem;">Total Revenue</h4>
            <p style="font-size: 2.5rem; font-weight: bold; margin: 0;">Rs. <?php echo number_format($revenue, 2); ?></p>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div style="background: white; padding: 2rem; border-radius: 10px; margin-bottom: 3rem;">
        <h3 style="color: #8B4513; margin-bottom: 1.5rem;">Quick Actions</h3>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="products.php" class="btn">Manage Products</a>
            <a href="add_product.php" class="btn btn-secondary">Add New Product</a>
            <a href="orders.php" class="btn btn-secondary">View Orders</a>
        </div>
    </div>
    
    <!-- Recent Orders -->
    <div style="background: white; padding: 2rem; border-radius: 10px;">
        <h3 style="color: #8B4513; margin-bottom: 1.5rem;">Recent Orders</h3>
        
        <?php if (count($recent_orders) > 0): ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['order_id']; ?></td>
                            <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                            <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo ucfirst($order['order_status']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: #666;">No orders yet</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
