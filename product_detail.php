<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product details
$query = "SELECT p.*, vp.shop_name, vp.vendor_id, c.category_name, u.phone, u.email 
          FROM products p 
          JOIN vendor_profiles vp ON p.vendor_id = vp.vendor_id 
          JOIN categories c ON p.category_id = c.category_id 
          JOIN users u ON vp.user_id = u.user_id
          WHERE p.product_id = ? AND p.is_active = 1";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header("Location: products.php");
    exit();
}

$pageTitle = $product['product_name'] . ' - Heritage';

// Fetch reviews
$reviews_query = "SELECT r.*, u.username 
                  FROM reviews r 
                  JOIN users u ON r.customer_id = u.user_id 
                  WHERE r.product_id = ? 
                  ORDER BY r.created_at DESC";
$reviews_stmt = mysqli_prepare($conn, $reviews_query);
mysqli_stmt_bind_param($reviews_stmt, "i", $product_id);
mysqli_stmt_execute($reviews_stmt);
$reviews_result = mysqli_stmt_get_result($reviews_stmt);
$reviews = mysqli_fetch_all($reviews_result, MYSQLI_ASSOC);

// Calculate average rating
$avg_rating = 0;
if (count($reviews) > 0) {
    $total_rating = array_sum(array_column($reviews, 'rating'));
    $avg_rating = $total_rating / count($reviews);
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isCustomer()) {
    $rating = intval($_POST['rating']);
    $review_text = sanitizeInput($_POST['review_text']);
    $customer_id = getCurrentUserId();
    
    $insert_review = "INSERT INTO reviews (product_id, customer_id, rating, review_text) VALUES (?, ?, ?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_review);
    mysqli_stmt_bind_param($insert_stmt, "iiis", $product_id, $customer_id, $rating, $review_text);
    
    if (mysqli_stmt_execute($insert_stmt)) {
        header("Location: product_detail.php?id=$product_id&review=success");
        exit();
    }
}

include 'includes/header.php';
?>

<div class="container">
    <?php if (isset($_GET['review']) && $_GET['review'] == 'success'): ?>
        <div class="alert alert-success">Thank you for your review!</div>
    <?php endif; ?>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin: 2rem 0; background: white; padding: 2rem; border-radius: 10px;">
        <!-- Product Image -->
        <div>
            <img src="<?php echo $product['image_url'] ? htmlspecialchars($product['image_url']) : 'images/placeholder.jpg'; ?>" 
                 alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                 style="width: 100%; border-radius: 10px;">
        </div>
        
        <!-- Product Details -->
        <div>
            <h1 style="color: #8B4513; margin-bottom: 1rem;"><?php echo htmlspecialchars($product['product_name']); ?></h1>
            
            <p style="color: #666; margin-bottom: 1rem;">
                Category: <strong><?php echo htmlspecialchars($product['category_name']); ?></strong>
            </p>
            
            <p style="color: #666; margin-bottom: 1rem;">
                Vendor: <strong><?php echo htmlspecialchars($product['shop_name']); ?></strong>
            </p>
            
            <div style="margin-bottom: 1rem;">
                <span class="review-rating">
                    <?php 
                    for ($i = 1; $i <= 5; $i++) {
                        echo $i <= round($avg_rating) ? '★' : '☆';
                    }
                    ?>
                </span>
                <span style="color: #666;">
                    (<?php echo number_format($avg_rating, 1); ?> - <?php echo count($reviews); ?> reviews)
                </span>
            </div>
            
            <h2 style="color: #D2691E; margin-bottom: 1rem;">Rs. <?php echo number_format($product['price'], 2); ?></h2>
            
            <p style="color: #666; margin-bottom: 1rem;">
                Stock: <strong><?php echo $product['stock_quantity']; ?> available</strong>
            </p>
            
            <p style="line-height: 1.8; margin-bottom: 2rem;">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </p>
            
            <?php if (isCustomer() && $product['stock_quantity'] > 0): ?>
                <button onclick="addToCart(<?php echo $product['product_id']; ?>)" 
                        class="btn" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                    Add to Cart
                </button>
            <?php elseif ($product['stock_quantity'] == 0): ?>
                <button class="btn" disabled style="width: 100%; background: #999;">Out of Stock</button>
            <?php elseif (!isLoggedIn()): ?>
                <a href="login.php" class="btn" style="width: 100%; text-align: center; padding: 1rem;">
                    Login to Purchase
                </a>
            <?php endif; ?>
            
            <div style="margin-top: 2rem; padding: 1rem; background: #f8f9fa; border-radius: 5px;">
                <h4>Vendor Contact</h4>
                <p>Email: <?php echo htmlspecialchars($product['email']); ?></p>
                <?php if ($product['phone']): ?>
                    <p>Phone: <?php echo htmlspecialchars($product['phone']); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Reviews Section -->
    <div class="reviews-section">
        <h2 style="color: #8B4513; margin-bottom: 2rem;">Customer Reviews</h2>
        
        <?php if (isCustomer()): ?>
            <div style="background: white; padding: 2rem; border-radius: 10px; margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1rem;">Write a Review</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Rating</label>
                        <select name="rating" required>
                            <option value="">Select rating</option>
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Very Good</option>
                            <option value="3">3 - Good</option>
                            <option value="2">2 - Fair</option>
                            <option value="1">1 - Poor</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Review</label>
                        <textarea name="review_text" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn">Submit Review</button>
                </form>
            </div>
        <?php endif; ?>
        
        <?php if (count($reviews) > 0): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                    <div class="review-rating">
                        <?php 
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= $review['rating'] ? '★' : '☆';
                        }
                        ?>
                    </div>
                    <p class="review-author"><?php echo htmlspecialchars($review['username']); ?></p>
                    <p class="review-date"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></p>
                    <p style="margin-top: 1rem;"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: #666; background: white; padding: 2rem; border-radius: 10px;">
                No reviews yet. Be the first to review this product!
            </p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
