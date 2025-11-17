<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireVendor();

$pageTitle = 'Orders - Heritage';
$user_id = getCurrentUserId();

// Get vendor profile
$vendor_query = "SELECT * FROM vendor_profiles WHERE user_id = ?";
$vendor_stmt = mysqli_prepare($conn, $vendor_query);
mysqli_stmt_bind_param($vendor_stmt, "i", $user_id);
mysqli_stmt_execute($vendor_stmt);
$vendor_result = mysqli_stmt_get_result($vendor_stmt);
$vendor = mysqli_fetch_assoc($vendor_result);
$vendor_id = $vendor['vendor_id'];

// Fetch orders
$query = "SELECT DISTINCT o.*, u.full_name, u.email, u.phone 
          FROM orders o 
          JOIN order_items oi ON o.order_id = oi.order_id 
          JOIN products p ON oi.product_id = p.product_id 
          JOIN users u ON o.customer_id = u.user_id
          WHERE p.vendor_id = ? 
          ORDER BY o.created_at DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $vendor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

include '../includes/header.php';
?>

<div class="container">
    <h2 style="text-align: center; margin: 2rem 0; color: #8B4513;">Orders</h2>
    
    <?php if (count($orders) > 0): ?>
        <?php foreach ($orders as $order): ?>
            <?php
            // Fetch order items for this vendor only
            $items_query = "SELECT oi.*, p.product_name, p.image_url 
                          FROM order_items oi 
                          JOIN products p ON oi.product_id = p.product_id 
                          WHERE oi.order_id = ? AND p.vendor_id = ?";
            $items_stmt = mysqli_prepare($conn, $items_query);
            mysqli_stmt_bind_param($items_stmt, "ii", $order['order_id'], $vendor_id);
            mysqli_stmt_execute($items_stmt);
            $items_result = mysqli_stmt_get_result($items_stmt);
            $items = mysqli_fetch_all($items_result, MYSQLI_ASSOC);
            
            if (count($items) == 0) continue; // Skip if no items for this vendor
            
            $vendor_total = 0;
            foreach ($items as $item) {
                $vendor_total += $item['price'] * $item['quantity'];
            }
            ?>
            
            <div style="background: white; padding: 2rem; border-radius: 10px; margin-bottom: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #f0f0f0;">
                    <div>
                        <h3 style="color: #8B4513;">Order #<?php echo $order['order_id']; ?></h3>
                        <p style="color: #666;">
                            Placed on <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                        </p>
                        <p style="color: #666;">Customer: <?php echo htmlspecialchars($order['full_name']); ?></p>
                    </div>
                    <div>
                        <span style="padding: 0.5rem 1rem; background: #d1ecf1; color: #0c5460; border-radius: 5px; font-weight: bold;">
                            <?php echo ucfirst($order['order_status']); ?>
                        </span>
                    </div>
                </div>
                
                <?php foreach ($items as $item): ?>
                    <div style="display: flex; gap: 1rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #f0f0f0;">
                        <img src="../<?php echo $item['image_url'] ? htmlspecialchars($item['image_url']) : 'images/placeholder.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                             style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
                        <div style="flex: 1;">
                            <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                            <p style="color: #666;">Quantity: <?php echo $item['quantity']; ?></p>
                            <p style="color: #D2691E; font-weight: bold;">
                                Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div style="text-align: right; margin-top: 1rem; padding-top: 1rem; border-top: 2px solid #8B4513;">
                    <h3 style="color: #8B4513;">Vendor Total: Rs. <?php echo number_format($vendor_total, 2); ?></h3>
                </div>
                
                <div style="margin-top: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 5px;">
                    <strong>Shipping Address:</strong>
                    <p style="margin-top: 0.5rem;"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                    <strong style="display: block; margin-top: 1rem;">Customer Contact:</strong>
                    <p style="margin-top: 0.5rem;">
                        Email: <?php echo htmlspecialchars($order['email']); ?><br>
                        <?php if ($order['phone']): ?>
                            Phone: <?php echo htmlspecialchars($order['phone']); ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 3rem; background: white; border-radius: 10px;">
            <h3 style="color: #666;">No orders yet</h3>
            <p style="margin: 1rem 0;">You will see orders here when customers purchase your products</p>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
