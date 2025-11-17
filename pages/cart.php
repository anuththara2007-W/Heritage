<?php
$pageTitle = 'Shopping Cart';
include __DIR__ . '/../includes/header.php';

requireLogin();

$conn = getDBConnection();
$userId = getCurrentUserId();

// Get cart items
$query = "SELECT c.cart_id, c.quantity, p.product_id, p.product_name, p.price, p.image_url, p.stock_quantity
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
?>

<div class="container">
    <h1>Shopping Cart</h1>
    
    <?php if (count($cartItems) > 0): ?>
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <div>
                <?php foreach ($cartItems as $item): ?>
                    <div class="card" style="display: grid; grid-template-columns: 150px 1fr auto; gap: 1rem; align-items: center;">
                        <img src="/assets/images/products/<?php echo htmlspecialchars($item['image_url'] ?? 'placeholder.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                             style="width: 100%; border-radius: 5px;">
                        
                        <div>
                            <h3><?php echo htmlspecialchars($item['product_name']); ?></h3>
                            <p>Price: <?php echo formatPrice($item['price']); ?></p>
                            <div style="display: flex; align-items: center; gap: 1rem; margin-top: 1rem;">
                                <label>Quantity:</label>
                                <input type="number" value="<?php echo $item['quantity']; ?>" 
                                       min="1" max="<?php echo $item['stock_quantity']; ?>" 
                                       onchange="updateCartQuantity(<?php echo $item['cart_id']; ?>, this.value)"
                                       style="width: 80px; padding: 5px;">
                                <button onclick="removeFromCart(<?php echo $item['cart_id']; ?>)" 
                                        class="btn btn-small" style="color: var(--danger-color);">
                                    Remove
                                </button>
                            </div>
                        </div>
                        
                        <div style="text-align: right;">
                            <strong><?php echo formatPrice($item['subtotal']); ?></strong>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div>
                <div class="card">
                    <h3>Order Summary</h3>
                    <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; margin-top: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Subtotal:</span>
                            <strong><?php echo formatPrice($totalPrice); ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span>Shipping:</span>
                            <span>Calculated at checkout</span>
                        </div>
                        <div style="border-top: 2px solid var(--border-color); padding-top: 1rem; display: flex; justify-content: space-between;">
                            <strong>Total:</strong>
                            <strong style="font-size: 1.5rem; color: var(--primary-color);">
                                <?php echo formatPrice($totalPrice); ?>
                            </strong>
                        </div>
                    </div>
                    <a href="/pages/checkout.php" class="btn btn-primary btn-block" style="margin-top: 1rem;">
                        Proceed to Checkout
                    </a>
                    <a href="/pages/shop.php" class="btn btn-block" style="margin-top: 0.5rem;">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card text-center">
            <h2>Your cart is empty</h2>
            <p>Add some products to your cart to see them here.</p>
            <a href="/pages/shop.php" class="btn btn-primary">Shop Now</a>
        </div>
    <?php endif; ?>
</div>

<?php
$stmt->close();
closeDBConnection($conn);
include __DIR__ . '/../includes/footer.php';
?>
