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

$cartId = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
$userId = getCurrentUserId();

if ($cartId <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart item or quantity']);
    exit();
}

$conn = getDBConnection();

// Check stock availability
$checkStmt = $conn->prepare("SELECT p.stock_quantity FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.cart_id = ? AND c.user_id = ?");
$checkStmt->bind_param("ii", $cartId, $userId);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Cart item not found']);
    $checkStmt->close();
    closeDBConnection($conn);
    exit();
}

$data = $result->fetch_assoc();
if ($quantity > $data['stock_quantity']) {
    echo json_encode(['success' => false, 'message' => 'Quantity exceeds available stock']);
    $checkStmt->close();
    closeDBConnection($conn);
    exit();
}

// Update quantity
$stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ? AND user_id = ?");
$stmt->bind_param("iii", $quantity, $cartId, $userId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Cart updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating cart']);
}

$checkStmt->close();
$stmt->close();
closeDBConnection($conn);
?>
