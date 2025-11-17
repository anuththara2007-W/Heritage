<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

requireLogin();
if (!isCustomer()) {
    header("Location: index.php");
    exit();
}

$pageTitle = 'Shopping Cart - Heritage';
$customer_id = getCurrentUserId();

// Fetch cart items
$query = "SELECT c.*, p.product_name, p.price, p.image_url, p.stock_quantity, vp.shop_name 
          FROM cart c 
          JOIN products p ON c.product_id = p.product_id 
          JOIN vendor_profiles vp ON p.vendor_id = vp.vendor_id 
          WHERE c.customer_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cart_items = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

include 'includes/header.php';
?>

<div class="container">
    <h2 style="text-align: center; margin: 2rem 0; color: #8B4513;">Shopping Cart</h2>
    
    <?php if (count($cart_items) > 0): ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <img src="<?php echo $item['image_url'] ? htmlspecialchars($item['image_url']) : 'images/placeholder.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 5px;">
                                <div>
                                    <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                    <p style="color: #666; font-size: 0.9rem;">by <?php echo htmlspecialchars($item['shop_name']); ?></p>
                                </div>
                            </div>
                        </td>
                        <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <input type="number" 
                                   value="<?php echo $item['quantity']; ?>" 
                                   min="1" 
                                   max="<?php echo $item['stock_quantity']; ?>"
                                   style="width: 80px; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;"
                                   onchange="updateCartQuantity(<?php echo $item['cart_id']; ?>, this.value)">
                        </td>
                        <td>Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        <td>
                            <button onclick="removeFromCart(<?php echo $item['cart_id']; ?>)" 
                                    class="btn btn-secondary">Remove</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="cart-total">
            <h3 style="text-align: right; color: #8B4513;">Total: Rs. <?php echo number_format($total, 2); ?></h3>
            <div style="text-align: right; margin-top: 1rem;">
                <a href="products.php" class="btn btn-secondary" style="margin-right: 1rem;">Continue Shopping</a>
                <a href="checkout.php" class="btn">Proceed to Checkout</a>
            </div>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 3rem; background: white; border-radius: 10px;">
            <h3 style="color: #666;">Your cart is empty</h3>
            <p style="margin: 1rem 0;">Add some products to your cart to continue shopping</p>
            <a href="products.php" class="btn">Browse Products</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
