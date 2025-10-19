<?php
$pageTitle = 'Manage Reviews';
include __DIR__ . '/../includes/header.php';

requireAdmin();

$conn = getDBConnection();

// Handle review status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $reviewId = intval($_POST['review_id']);
    $newStatus = sanitize($_POST['status']);
    
    $stmt = $conn->prepare("UPDATE reviews SET status = ? WHERE review_id = ?");
    $stmt->bind_param("si", $newStatus, $reviewId);
    
    if ($stmt->execute()) {
        setFlashMessage('Review status updated successfully', 'success');
    } else {
        setFlashMessage('Error updating review status', 'error');
    }
    $stmt->close();
}

// Handle review deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $reviewId = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM reviews WHERE review_id = ?");
    $stmt->bind_param("i", $reviewId);
    if ($stmt->execute()) {
        setFlashMessage('Review deleted successfully', 'success');
    } else {
        setFlashMessage('Error deleting review', 'error');
    }
    $stmt->close();
    header("Location: /admin/reviews.php");
    exit();
}

// Get filter
$statusFilter = isset($_GET['status']) ? sanitize($_GET['status']) : 'pending';

// Get reviews
$query = "SELECT r.*, u.username, p.product_name, p.product_id 
          FROM reviews r 
          JOIN users u ON r.user_id = u.user_id 
          JOIN products p ON r.product_id = p.product_id";

if (!empty($statusFilter)) {
    $query .= " WHERE r.status = '" . $conn->real_escape_string($statusFilter) . "'";
}

$query .= " ORDER BY r.created_at DESC";
$reviews = $conn->query($query);
?>

<div class="container">
    <h1>Manage Reviews</h1>
    <a href="/admin/dashboard.php" class="btn">← Back to Dashboard</a>
    
    <div class="card" style="margin-top: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2>All Reviews</h2>
            <div>
                <a href="/admin/reviews.php?status=pending" class="btn btn-small <?php echo $statusFilter === 'pending' ? 'btn-primary' : ''; ?>">
                    Pending
                </a>
                <a href="/admin/reviews.php?status=approved" class="btn btn-small <?php echo $statusFilter === 'approved' ? 'btn-primary' : ''; ?>">
                    Approved
                </a>
                <a href="/admin/reviews.php?status=rejected" class="btn btn-small <?php echo $statusFilter === 'rejected' ? 'btn-primary' : ''; ?>">
                    Rejected
                </a>
            </div>
        </div>
        
        <?php if ($reviews->num_rows > 0): ?>
            <?php while ($review = $reviews->fetch_assoc()): ?>
                <div style="border: 1px solid var(--border-color); padding: 1.5rem; margin-bottom: 1rem; border-radius: 5px;">
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                        <div>
                            <h3>
                                <a href="/pages/product.php?id=<?php echo $review['product_id']; ?>" target="_blank">
                                    <?php echo htmlspecialchars($review['product_name']); ?>
                                </a>
                            </h3>
                            <p><strong>By:</strong> <?php echo htmlspecialchars($review['username']); ?></p>
                            <p><strong>Date:</strong> <?php echo formatDateTime($review['created_at']); ?></p>
                            
                            <div style="color: #FFD700; font-size: 1.2rem; margin: 0.5rem 0;">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php echo $i <= $review['rating'] ? '★' : '☆'; ?>
                                <?php endfor; ?>
                                <span style="color: var(--text-color); font-size: 1rem; margin-left: 0.5rem;">
                                    (<?php echo $review['rating']; ?>/5)
                                </span>
                            </div>
                            
                            <p style="margin-top: 1rem; padding: 1rem; background-color: var(--light-bg); border-radius: 5px;">
                                <?php echo nl2br(htmlspecialchars($review['review_text'] ?? 'No review text')); ?>
                            </p>
                        </div>
                        
                        <div>
                            <form method="POST" action="">
                                <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                                
                                <div class="form-group">
                                    <label for="status_<?php echo $review['review_id']; ?>">Status</label>
                                    <select id="status_<?php echo $review['review_id']; ?>" name="status" class="form-control">
                                        <option value="pending" <?php echo $review['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="approved" <?php echo $review['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                        <option value="rejected" <?php echo $review['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                </div>
                                
                                <button type="submit" name="update_status" class="btn btn-primary btn-block">
                                    Update Status
                                </button>
                                
                                <a href="/admin/reviews.php?delete=<?php echo $review['review_id']; ?>" 
                                   class="btn btn-block" 
                                   onclick="return confirm('Are you sure you want to delete this review?');"
                                   style="margin-top: 0.5rem; color: var(--danger-color);">
                                    Delete Review
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center">
                <p>No <?php echo $statusFilter; ?> reviews found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
closeDBConnection($conn);
include __DIR__ . '/../includes/footer.php';
?>
