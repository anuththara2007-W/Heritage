<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
$userId = getCurrentUserId();

if ($productId <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
    exit();
}

$conn = getDBConnection();

// Check if product exists and has stock
$stmt = $conn->prepare("SELECT stock_quantity FROM products WHERE product_id = ? AND status = 'active'");
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    $stmt->close();
    closeDBConnection($conn);
    exit();
}

$product = $result->fetch_assoc();

if ($product['stock_quantity'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
    $stmt->close();
    closeDBConnection($conn);
    exit();
}

// Check if product already in cart
$stmt = $conn->prepare("SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $userId, $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing cart item
    $cart = $result->fetch_assoc();
    $newQuantity = $cart['quantity'] + $quantity;
    
    if ($newQuantity > $product['stock_quantity']) {
        echo json_encode(['success' => false, 'message' => 'Cannot add more than available stock']);
        $stmt->close();
        closeDBConnection($conn);
        exit();
    }
    
    $updateStmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
    $updateStmt->bind_param("ii", $newQuantity, $cart['cart_id']);
    $updateStmt->execute();
    $updateStmt->close();
} else {
    // Insert new cart item
    $insertStmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $insertStmt->bind_param("iii", $userId, $productId, $quantity);
    $insertStmt->execute();
    $insertStmt->close();
}

echo json_encode(['success' => true, 'message' => 'Product added to cart']);

$stmt->close();
closeDBConnection($conn);
?>
