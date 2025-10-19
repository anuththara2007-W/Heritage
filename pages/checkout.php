<?php
$pageTitle = 'Checkout';
include __DIR__ . '/../includes/header.php';

requireLogin();

$conn = getDBConnection();
$userId = getCurrentUserId();

// Get cart items
$query = "SELECT c.cart_id, c.quantity, p.product_id, p.product_name, p.price, p.stock_quantity
          FROM cart c
          JOIN products p ON c.product_id = p.product_id
          WHERE c.user_id = ? AND p.status = 'active'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
$totalPrice = 0;

while ($item = $result->fetch_assoc()) {
    $subtotal = $item['price'] * $item['quantity'];
    $item['subtotal'] = $subtotal;
    $totalPrice += $subtotal;
    $cartItems[] = $item;
}

if (count($cartItems) === 0) {
    header("Location: /pages/cart.php");
    exit();
}

// Get user info
$userStmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userResult = $userStmt->get_result();
$user = $userResult->fetch_assoc();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitize($_POST['full_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $postalCode = sanitize($_POST['postal_code'] ?? '');
    $deliveryMethod = sanitize($_POST['delivery_method'] ?? 'delivery');
    $paymentMethod = sanitize($_POST['payment_method'] ?? 'cash_on_delivery');
    
    if (empty($fullName) || empty($phone) || empty($address)) {
        $error = 'Please fill in all required fields.';
    } else {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Create order
            $shippingAddress = "$address, $city, $postalCode";
            $orderStmt = $conn->prepare("INSERT INTO orders (user_id, order_status, total_amount, shipping_address, phone, delivery_method, payment_method) VALUES (?, 'Pending', ?, ?, ?, ?, ?)");
            $orderStmt->bind_param("idssss", $userId, $totalPrice, $shippingAddress, $phone, $deliveryMethod, $paymentMethod);
            $orderStmt->execute();
            $orderId = $conn->insert_id;
            
            // Add order items and update stock
            foreach ($cartItems as $item) {
                // Insert order item
                $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
                $itemStmt->bind_param("iiidd", $orderId, $item['product_id'], $item['quantity'], $item['price'], $item['subtotal']);
                $itemStmt->execute();
                
                // Update product stock
                $stockStmt = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
                $stockStmt->bind_param("ii", $item['quantity'], $item['product_id']);
                $stockStmt->execute();
            }
            
            // Clear cart
            $clearStmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $clearStmt->bind_param("i", $userId);
            $clearStmt->execute();
            
            // Commit transaction
            $conn->commit();
            
            setFlashMessage('Order placed successfully! Order ID: #' . $orderId, 'success');
            header("Location: /pages/account.php");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Error processing order. Please try again.';
        }
    }
}
?>

<div class="container">
    <h1>Checkout</h1>
    
    <?php if ($error): ?>
        <div class="flash-message flash-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div>
            <div class="card">
                <h2>Shipping Information</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="full_name">Full Name *</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" required 
                               value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" class="form-control" required 
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address *</label>
                        <textarea id="address" name="address" class="form-control" required 
                                  rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
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
                    
                    <div class="form-group">
                        <label for="delivery_method">Delivery Method *</label>
                        <select id="delivery_method" name="delivery_method" class="form-control" required>
                            <option value="delivery">Home Delivery</option>
                            <option value="pickup">Store Pickup</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_method">Payment Method *</label>
                        <select id="payment_method" name="payment_method" class="form-control" required>
                            <option value="cash_on_delivery">Cash on Delivery</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="card">Credit/Debit Card</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block btn-large">Place Order</button>
                </form>
            </div>
        </div>
        
        <div>
            <div class="card">
                <h2>Order Summary</h2>
                
                <?php foreach ($cartItems as $item): ?>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid var(--border-color);">
                        <div>
                            <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                            <br>
                            <small>Qty: <?php echo $item['quantity']; ?> × <?php echo formatPrice($item['price']); ?></small>
                        </div>
                        <strong><?php echo formatPrice($item['subtotal']); ?></strong>
                    </div>
                <?php endforeach; ?>
                
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 2px solid var(--border-color);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Subtotal:</span>
                        <strong><?php echo formatPrice($totalPrice); ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span>Shipping:</span>
                        <span>Free</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 1.3rem;">
                        <strong>Total:</strong>
                        <strong style="color: var(--primary-color);"><?php echo formatPrice($totalPrice); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$stmt->close();
$userStmt->close();
closeDBConnection($conn);
include __DIR__ . '/../includes/footer.php';
?>
