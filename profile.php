<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

requireLogin();

$pageTitle = 'My Profile - Heritage';
$user_id = getCurrentUserId();
$success = '';
$error = '';

// Fetch user data
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Fetch vendor profile if vendor
$vendor = null;
if (isVendor()) {
    $vendor_query = "SELECT * FROM vendor_profiles WHERE user_id = ?";
    $vendor_stmt = mysqli_prepare($conn, $vendor_query);
    mysqli_stmt_bind_param($vendor_stmt, "i", $user_id);
    mysqli_stmt_execute($vendor_stmt);
    $vendor_result = mysqli_stmt_get_result($vendor_stmt);
    $vendor = mysqli_fetch_assoc($vendor_result);
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitizeInput($_POST['full_name']);
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    
    $update_query = "UPDATE users SET full_name = ?, phone = ?, address = ? WHERE user_id = ?";
    $update_stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($update_stmt, "sssi", $full_name, $phone, $address, $user_id);
    
    if (mysqli_stmt_execute($update_stmt)) {
        // Update vendor profile if vendor
        if (isVendor() && $vendor) {
            $shop_name = sanitizeInput($_POST['shop_name']);
            $shop_description = sanitizeInput($_POST['shop_description']);
            
            $vendor_update = "UPDATE vendor_profiles SET shop_name = ?, shop_description = ? WHERE user_id = ?";
            $vendor_update_stmt = mysqli_prepare($conn, $vendor_update);
            mysqli_stmt_bind_param($vendor_update_stmt, "ssi", $shop_name, $shop_description, $user_id);
            mysqli_stmt_execute($vendor_update_stmt);
        }
        
        $success = "Profile updated successfully!";
        
        // Refresh data
        $user['full_name'] = $full_name;
        $user['phone'] = $phone;
        $user['address'] = $address;
        if (isVendor() && $vendor) {
            $vendor['shop_name'] = $shop_name;
            $vendor['shop_description'] = $shop_description;
        }
    } else {
        $error = "Error updating profile. Please try again.";
    }
}

include 'includes/header.php';
?>

<div class="container">
    <h2 style="text-align: center; margin: 2rem 0; color: #8B4513;">My Profile</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <div class="form-container">
        <div style="background: #f8f9fa; padding: 1rem; border-radius: 5px; margin-bottom: 2rem;">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Account Type:</strong> <?php echo ucfirst($user['user_type']); ?></p>
            <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>
            
            <?php if (isVendor() && $vendor): ?>
                <div class="form-group">
                    <label for="shop_name">Shop Name</label>
                    <input type="text" id="shop_name" name="shop_name" value="<?php echo htmlspecialchars($vendor['shop_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="shop_description">Shop Description</label>
                    <textarea id="shop_description" name="shop_description" rows="3"><?php echo htmlspecialchars($vendor['shop_description'] ?? ''); ?></textarea>
                </div>
            <?php endif; ?>
            
            <button type="submit" class="btn" style="width: 100%;">Update Profile</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
