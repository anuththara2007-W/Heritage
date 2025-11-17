<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !isCustomer()) {
    echo json_encode(['success' => false, 'message' => 'Please login as customer']);
    exit();
}

$customer_id = getCurrentUserId();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Add to cart
if ($action == 'add') {
    $product_id = intval($_POST['product_id']);
    
    // Check if product exists and has stock
    $check_query = "SELECT stock_quantity FROM products WHERE product_id = ? AND is_active = 1";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    
    if (!$product || $product['stock_quantity'] < 1) {
        echo json_encode(['success' => false, 'message' => 'Product not available']);
        exit();
    }
    
    // Check if already in cart
    $cart_check = "SELECT * FROM cart WHERE customer_id = ? AND product_id = ?";
    $cart_stmt = mysqli_prepare($conn, $cart_check);
    mysqli_stmt_bind_param($cart_stmt, "ii", $customer_id, $product_id);
    mysqli_stmt_execute($cart_stmt);
    $cart_result = mysqli_stmt_get_result($cart_stmt);
    
    if (mysqli_num_rows($cart_result) > 0) {
        // Update quantity
        $update_query = "UPDATE cart SET quantity = quantity + 1 WHERE customer_id = ? AND product_id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "ii", $customer_id, $product_id);
        mysqli_stmt_execute($update_stmt);
    } else {
        // Insert new cart item
        $insert_query = "INSERT INTO cart (customer_id, product_id, quantity) VALUES (?, ?, 1)";
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "ii", $customer_id, $product_id);
        mysqli_stmt_execute($insert_stmt);
    }
    
    echo json_encode(['success' => true, 'message' => 'Product added to cart']);
    exit();
}

// Remove from cart
if ($action == 'remove') {
    $cart_id = intval($_POST['cart_id']);
    
    $delete_query = "DELETE FROM cart WHERE cart_id = ? AND customer_id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "ii", $cart_id, $customer_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error removing item']);
    }
    exit();
}

// Update quantity
if ($action == 'update') {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);
    
    if ($quantity < 1) {
        echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
        exit();
    }
    
    $update_query = "UPDATE cart SET quantity = ? WHERE cart_id = ? AND customer_id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "iii", $quantity, $cart_id, $customer_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating quantity']);
    }
    exit();
}

// Get cart count
if ($action == 'count') {
    $count_query = "SELECT SUM(quantity) as total FROM cart WHERE customer_id = ?";
    $stmt = mysqli_prepare($conn, $count_query);
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    echo json_encode(['success' => true, 'count' => $row['total'] ?? 0]);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
