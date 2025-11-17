<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

requireLogin();
if (!isCustomer()) {
    header("Location: index.php");
    exit();
}

$pageTitle = 'Checkout - Heritage';
$customer_id = getCurrentUserId();
$error = '';
$success = '';

// Fetch cart items
$query = "SELECT c.*, p.product_name, p.price, p.stock_quantity 
          FROM cart c 
          JOIN products p ON c.product_id = p.product_id 
          WHERE c.customer_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cart_items = mysqli_fetch_all($result, MYSQLI_ASSOC);

if (count($cart_items) == 0) {
    header("Location: cart.php");
    exit();
}

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Process order
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_address = sanitizeInput($_POST['shipping_address']);
    
    if (empty($shipping_address)) {
        $error = "Please provide a shipping address";
    } else {
        mysqli_begin_transaction($conn);
        
        try {
            // Create order
            $order_query = "INSERT INTO orders (customer_id, total_amount, shipping_address) VALUES (?, ?, ?)";
            $order_stmt = mysqli_prepare($conn, $order_query);
            mysqli_stmt_bind_param($order_stmt, "ids", $customer_id, $total, $shipping_address);
            mysqli_stmt_execute($order_stmt);
            $order_id = mysqli_insert_id($conn);
            
            // Add order items and update stock
            foreach ($cart_items as $item) {
                // Check stock
                if ($item['stock_quantity'] < $item['quantity']) {
                    throw new Exception("Insufficient stock for " . $item['product_name']);
                }
                
                // Insert order item
                $item_query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
                $item_stmt = mysqli_prepare($conn, $item_query);
                mysqli_stmt_bind_param($item_stmt, "iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                mysqli_stmt_execute($item_stmt);
                
                // Update stock
                $stock_query = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
                $stock_stmt = mysqli_prepare($conn, $stock_query);
                mysqli_stmt_bind_param($stock_stmt, "ii", $item['quantity'], $item['product_id']);
                mysqli_stmt_execute($stock_stmt);
            }
            
            // Clear cart
            $clear_query = "DELETE FROM cart WHERE customer_id = ?";
            $clear_stmt = mysqli_prepare($conn, $clear_query);
            mysqli_stmt_bind_param($clear_stmt, "i", $customer_id);
            mysqli_stmt_execute($clear_stmt);
            
            mysqli_commit($conn);
            
            header("Location: order_success.php?order_id=$order_id");
            exit();
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = $e->getMessage();
        }
    }
}

// Get user address
$user_query = "SELECT address FROM users WHERE user_id = ?";
$user_stmt = mysqli_prepare($conn, $user_query);
mysqli_stmt_bind_param($user_stmt, "i", $customer_id);
mysqli_stmt_execute($user_stmt);
$user_result = mysqli_stmt_get_result($user_stmt);
$user = mysqli_fetch_assoc($user_result);

include 'includes/header.php';
?>

<div class="container">
    <h2 style="text-align: center; margin: 2rem 0; color: #8B4513;">Checkout</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Order Form -->
        <div style="background: white; padding: 2rem; border-radius: 10px;">
            <h3 style="margin-bottom: 1.5rem; color: #8B4513;">Shipping Information</h3>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="shipping_address">Shipping Address *</label>
                    <textarea id="shipping_address" 
                              name="shipping_address" 
                              rows="5" 
                              required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    <small style="color: #666;">Please provide your complete address including street, city, and postal code</small>
                </div>
                
                <button type="submit" class="btn" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                    Place Order
                </button>
            </form>
        </div>
        
        <!-- Order Summary -->
        <div>
            <div style="background: white; padding: 2rem; border-radius: 10px;">
                <h3 style="margin-bottom: 1.5rem; color: #8B4513;">Order Summary</h3>
                
                <?php foreach ($cart_items as $item): ?>
                    <div style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #ddd;">
                        <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                        <p style="color: #666; font-size: 0.9rem;">
                            Qty: <?php echo $item['quantity']; ?> × Rs. <?php echo number_format($item['price'], 2); ?>
                        </p>
                        <p style="color: #D2691E; font-weight: bold;">
                            Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
                
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid #8B4513;">
                    <h3 style="color: #8B4513;">
                        Total: Rs. <?php echo number_format($total, 2); ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
