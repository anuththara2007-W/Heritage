<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireVendor();

$user_id = getCurrentUserId();
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get vendor profile
$vendor_query = "SELECT * FROM vendor_profiles WHERE user_id = ?";
$vendor_stmt = mysqli_prepare($conn, $vendor_query);
mysqli_stmt_bind_param($vendor_stmt, "i", $user_id);
mysqli_stmt_execute($vendor_stmt);
$vendor_result = mysqli_stmt_get_result($vendor_stmt);
$vendor = mysqli_fetch_assoc($vendor_result);
$vendor_id = $vendor['vendor_id'];

// Check if product belongs to vendor
$check_query = "SELECT * FROM products WHERE product_id = ? AND vendor_id = ?";
$check_stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($check_stmt, "ii", $product_id, $vendor_id);
mysqli_stmt_execute($check_stmt);
$result = mysqli_stmt_get_result($check_stmt);
$product = mysqli_fetch_assoc($result);

if ($product) {
    // Delete image file
    if ($product['image_url'] && file_exists('../' . $product['image_url'])) {
        unlink('../' . $product['image_url']);
    }
    
    // Delete product
    $delete_query = "DELETE FROM products WHERE product_id = ? AND vendor_id = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($delete_stmt, "ii", $product_id, $vendor_id);
    mysqli_stmt_execute($delete_stmt);
}

header("Location: products.php");
exit();
?>
