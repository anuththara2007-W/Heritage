<?php
$pageTitle = 'Product Details';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: /pages/shop.php");
    exit();
}

include __DIR__ . '/../includes/header.php';

$conn = getDBConnection();
$productId = intval($_GET['id']);

// Get product details
$stmt = $conn->prepare("SELECT p.*, c.category_name 
                        FROM products p 
                        LEFT JOIN categories c ON p.category_id = c.category_id 
                        WHERE p.product_id = ? AND p.status = 'active'");
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='container'><h2>Product not found</h2><a href='/pages/shop.php' class='btn btn-primary'>Back to Shop</a></div>";
    include __DIR__ . '/../includes/footer.php';
    exit();
}

$product = $result->fetch_assoc();

// Get product reviews
$reviewsQuery = "SELECT r.*, u.username 
                 FROM reviews r 
                 JOIN users u ON r.user_id = u.user_id 
                 WHERE r.product_id = ? AND r.status = 'approved' 
                 ORDER BY r.created_at DESC";
$reviewsStmt = $conn->prepare($reviewsQuery);
$reviewsStmt->bind_param("i", $productId);
$reviewsStmt->execute();
$reviewsResult = $reviewsStmt->get_result();

// Calculate average rating
$ratingQuery = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                FROM reviews 
                WHERE product_id = ? AND status = 'approved'";
$ratingStmt = $conn->prepare($ratingQuery);
$ratingStmt->bind_param("i", $productId);
$ratingStmt->execute();
$ratingResult = $ratingStmt->get_result();
$ratingData = $ratingResult->fetch_assoc();
$avgRating = $ratingData['avg_rating'] ? round($ratingData['avg_rating'], 1) : 0;
$totalReviews = $ratingData['total_reviews'];
?>

<div class="container">
    <a href="/pages/shop.php" class="btn" style="margin-bottom: 1rem;">← Back to Shop</a>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
        <div>
            <?php if ($product['image_url']): ?>
                <img src="/assets/images/products/<?php echo htmlspecialchars($product['image_url']); ?>" 
                     alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                     style="width: 100%; border-radius: 10px;">
            <?php else: ?>
                <img src="/assets/images/placeholder.jpg" 
                     alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                     style="width: 100%; border-radius: 10px;">
            <?php endif; ?>
        </div>
        
        <div>
            <span class="product-category"><?php echo htmlspecialchars($product['category_name'] ?? 'General'); ?></span>
            <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
            
            <div style="margin: 1rem 0;">
                <span style="color: #FFD700; font-size: 1.2rem;">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php echo $i <= $avgRating ? '★' : '☆'; ?>
                    <?php endfor; ?>
                </span>
                <span style="margin-left: 0.5rem;"><?php echo $avgRating; ?> (<?php echo $totalReviews; ?> reviews)</span>
            </div>
            
            <div class="product-price" style="font-size: 2rem; margin: 1rem 0;">
                <?php echo formatPrice($product['price']); ?>
            </div>
            
            <div style="margin: 1rem 0;">
                <strong>Stock Status: </strong>
                <?php if ($product['stock_quantity'] > 0): ?>
                    <span style="color: var(--success-color);">In Stock (<?php echo $product['stock_quantity']; ?> available)</span>
                <?php else: ?>
                    <span style="color: var(--danger-color);">Out of Stock</span>
                <?php endif; ?>
            </div>
            
            <div style="margin: 2rem 0;">
                <h3>Description</h3>
                <p><?php echo nl2br(htmlspecialchars($product['description'] ?? '')); ?></p>
                
                <?php if ($product['story']): ?>
                    <h3 style="margin-top: 2rem;">Cultural Story</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['story'])); ?></p>
                <?php endif; ?>
            </div>
            
            <?php if ($product['stock_quantity'] > 0): ?>
                <div style="margin: 2rem 0;">
                    <label for="quantity"><strong>Quantity:</strong></label>
                    <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" 
                           style="width: 80px; padding: 5px; margin-left: 1rem;">
                </div>
                
                <?php if (isLoggedIn()): ?>
                    <button onclick="addToCart(<?php echo $product['product_id']; ?>, document.getElementById('quantity').value)" 
                            class="btn btn-primary btn-large">
                        Add to Cart
                    </button>
                <?php else: ?>
                    <a href="/pages/login.php" class="btn btn-primary btn-large">Login to Purchase</a>
                <?php endif; ?>
            <?php else: ?>
                <button class="btn btn-large" disabled>Out of Stock</button>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <h2>Customer Reviews (<?php echo $totalReviews; ?>)</h2>
        
        <?php if ($reviewsResult->num_rows > 0): ?>
            <?php while ($review = $reviewsResult->fetch_assoc()): ?>
                <div style="border-bottom: 1px solid var(--border-color); padding: 1rem 0;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <strong><?php echo htmlspecialchars($review['username']); ?></strong>
                        <span style="color: #999;"><?php echo formatDate($review['created_at']); ?></span>
                    </div>
                    <div style="color: #FFD700; margin-bottom: 0.5rem;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php echo $i <= $review['rating'] ? '★' : '☆'; ?>
                        <?php endfor; ?>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars($review['review_text'] ?? '')); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No reviews yet. Be the first to review this product!</p>
        <?php endif; ?>
        
        <?php if (isLoggedIn()): ?>
            <div style="margin-top: 2rem;">
                <h3>Write a Review</h3>
                <form method="POST" action="/pages/submit_review.php">
                    <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                    
                    <div class="form-group">
                        <label for="rating">Rating</label>
                        <select id="rating" name="rating" class="form-control" required>
                            <option value="">Select rating</option>
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Very Good</option>
                            <option value="3">3 - Good</option>
                            <option value="2">2 - Fair</option>
                            <option value="1">1 - Poor</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="review_text">Your Review</label>
                        <textarea id="review_text" name="review_text" class="form-control" rows="4"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
        <?php else: ?>
            <p style="margin-top: 1rem;"><a href="/pages/login.php">Login</a> to write a review.</p>
        <?php endif; ?>
    </div>
</div>

<?php
$stmt->close();
$reviewsStmt->close();
$ratingStmt->close();
closeDBConnection($conn);
include __DIR__ . '/../includes/footer.php';
?>
