<?php
$pageTitle = 'Admin Dashboard';
include __DIR__ . '/../includes/header.php';

requireAdmin();

$conn = getDBConnection();

// Get statistics
$statsQuery = "SELECT 
    (SELECT COUNT(*) FROM users WHERE user_type = 'customer') as total_customers,
    (SELECT COUNT(*) FROM products WHERE status = 'active') as total_products,
    (SELECT COUNT(*) FROM orders) as total_orders,
    (SELECT COUNT(*) FROM orders WHERE order_status = 'Pending') as pending_orders,
    (SELECT SUM(total_amount) FROM orders WHERE order_status != 'Canceled') as total_revenue,
    (SELECT COUNT(*) FROM reviews WHERE status = 'pending') as pending_reviews";
$stats = $conn->query($statsQuery)->fetch_assoc();

// Get recent orders
$recentOrdersQuery = "SELECT o.*, u.username 
                      FROM orders o 
                      JOIN users u ON o.user_id = u.user_id 
                      ORDER BY o.order_date DESC 
                      LIMIT 10";
$recentOrders = $conn->query($recentOrdersQuery);
?>

<div class="container">
    <h1>Admin Dashboard</h1>
    
    <div class="card" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; margin-bottom: 2rem;">
        <h2 style="color: var(--accent-color);">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>Manage your Heritage marketplace from this dashboard.</p>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="background-color: #e3f2fd;">
            <h3>👥 Customers</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--primary-color);">
                <?php echo $stats['total_customers']; ?>
            </p>
            <a href="/admin/users.php" class="btn btn-small">Manage</a>
        </div>
        
        <div class="card" style="background-color: #f3e5f5;">
            <h3>📦 Products</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--primary-color);">
                <?php echo $stats['total_products']; ?>
            </p>
            <a href="/admin/products.php" class="btn btn-small">Manage</a>
        </div>
        
        <div class="card" style="background-color: #fff3e0;">
            <h3>📋 Orders</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--primary-color);">
                <?php echo $stats['total_orders']; ?>
            </p>
            <a href="/admin/orders.php" class="btn btn-small">Manage</a>
        </div>
        
        <div class="card" style="background-color: #ffebee;">
            <h3>⏳ Pending</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--primary-color);">
                <?php echo $stats['pending_orders']; ?>
            </p>
            <a href="/admin/orders.php?status=Pending" class="btn btn-small">View</a>
        </div>
        
        <div class="card" style="background-color: #e8f5e9;">
            <h3>💰 Revenue</h3>
            <p style="font-size: 1.5rem; font-weight: bold; color: var(--primary-color);">
                <?php echo formatPrice($stats['total_revenue'] ?? 0); ?>
            </p>
        </div>
        
        <div class="card" style="background-color: #fce4ec;">
            <h3>⭐ Reviews</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--primary-color);">
                <?php echo $stats['pending_reviews']; ?>
            </p>
            <a href="/admin/reviews.php" class="btn btn-small">Review</a>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem;">
        <div class="card">
            <h2>Quick Actions</h2>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <a href="/admin/products.php?action=add" class="btn btn-primary">➕ Add New Product</a>
                <a href="/admin/categories.php?action=add" class="btn btn-primary">📁 Add Category</a>
                <a href="/admin/orders.php" class="btn">📋 View All Orders</a>
                <a href="/admin/reviews.php" class="btn">⭐ Manage Reviews</a>
            </div>
        </div>
        
        <div class="card">
            <h2>Recent Orders</h2>
            <?php if ($recentOrders->num_rows > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $recentOrders->fetch_assoc()): ?>
                            <tr>
                                <td><a href="/admin/orders.php?view=<?php echo $order['order_id']; ?>">#<?php echo $order['order_id']; ?></a></td>
                                <td><?php echo htmlspecialchars($order['username']); ?></td>
                                <td><?php echo formatPrice($order['total_amount']); ?></td>
                                <td><span class="product-category"><?php echo $order['order_status']; ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No orders yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
closeDBConnection($conn);
include __DIR__ . '/../includes/footer.php';
?>
