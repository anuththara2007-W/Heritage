<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/functions.php';

if (!isLoggedIn()) {
    setFlashMessage('Please login to submit a review', 'error');
    header("Location: /pages/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /pages/shop.php");
    exit();
}

$productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$reviewText = sanitize($_POST['review_text'] ?? '');
$userId = getCurrentUserId();

if ($productId <= 0 || $rating < 1 || $rating > 5) {
    setFlashMessage('Invalid review data', 'error');
    header("Location: /pages/product.php?id=$productId");
    exit();
}

$conn = getDBConnection();

// Check if user has already reviewed this product
$checkStmt = $conn->prepare("SELECT review_id FROM reviews WHERE product_id = ? AND user_id = ?");
$checkStmt->bind_param("ii", $productId, $userId);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    setFlashMessage('You have already reviewed this product', 'error');
} else {
    // Insert review
    $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, review_text, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->bind_param("iiis", $productId, $userId, $rating, $reviewText);
    
    if ($stmt->execute()) {
        setFlashMessage('Review submitted successfully! It will be visible after approval.', 'success');
    } else {
        setFlashMessage('Error submitting review', 'error');
    }
    $stmt->close();
}

$checkStmt->close();
closeDBConnection($conn);

header("Location: /pages/product.php?id=$productId");
exit();
?>
