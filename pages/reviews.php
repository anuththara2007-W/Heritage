<?php
$pageTitle = 'Customer Reviews';
include __DIR__ . '/../includes/header.php';

$conn = getDBConnection();

// Get all approved reviews
$query = "SELECT r.*, u.username, p.product_name, p.product_id 
          FROM reviews r 
          JOIN users u ON r.user_id = u.user_id 
          JOIN products p ON r.product_id = p.product_id 
          WHERE r.status = 'approved' 
          ORDER BY r.created_at DESC 
          LIMIT 50";
$result = $conn->query($query);
?>

<div class="container">
    <h1 class="text-center">Customer Reviews</h1>
    <p class="text-center">See what our customers have to say about our products</p>
    
    <div style="max-width: 800px; margin: 2rem auto;">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($review = $result->fetch_assoc()): ?>
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div>
                            <h3 style="margin-bottom: 0.5rem;">
                                <a href="/pages/product.php?id=<?php echo $review['product_id']; ?>">
                                    <?php echo htmlspecialchars($review['product_name']); ?>
                                </a>
                            </h3>
                            <div style="color: #FFD700; font-size: 1.2rem; margin-bottom: 0.5rem;">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php echo $i <= $review['rating'] ? '★' : '☆'; ?>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <span style="color: #999; font-size: 0.9rem;">
                            <?php echo formatDate($review['created_at']); ?>
                        </span>
                    </div>
                    
                    <p style="font-style: italic; margin-bottom: 1rem;">
                        "<?php echo nl2br(htmlspecialchars($review['review_text'] ?? '')); ?>"
                    </p>
                    
                    <p style="color: #666; font-size: 0.9rem;">
                        - <?php echo htmlspecialchars($review['username']); ?>
                    </p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="card text-center">
                <h3>No reviews yet</h3>
                <p>Be the first to share your experience with our products!</p>
                <a href="/pages/shop.php" class="btn btn-primary">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
closeDBConnection($conn);
include __DIR__ . '/../includes/footer.php';
?>
