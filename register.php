<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

$pageTitle = 'Register - Heritage';
$error = '';
$success = '';
$user_type = isset($_GET['type']) && $_GET['type'] == 'vendor' ? 'vendor' : 'customer';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitizeInput($_POST['full_name']);
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    $user_type = $_POST['user_type'];
    
    // Validation
    if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        // Check if username or email already exists
        $check_query = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $error = "Username or email already exists";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $insert_query = "INSERT INTO users (username, email, password, full_name, phone, address, user_type) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, "sssssss", $username, $email, $hashed_password, $full_name, $phone, $address, $user_type);
            
            if (mysqli_stmt_execute($stmt)) {
                $user_id = mysqli_insert_id($conn);
                
                // If vendor, create vendor profile
                if ($user_type == 'vendor') {
                    $shop_name = sanitizeInput($_POST['shop_name']);
                    $shop_description = sanitizeInput($_POST['shop_description']);
                    
                    $vendor_query = "INSERT INTO vendor_profiles (user_id, shop_name, shop_description) VALUES (?, ?, ?)";
                    $vendor_stmt = mysqli_prepare($conn, $vendor_query);
                    mysqli_stmt_bind_param($vendor_stmt, "iss", $user_id, $shop_name, $shop_description);
                    mysqli_stmt_execute($vendor_stmt);
                }
                
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="form-container">
        <h2 style="text-align: center; color: #8B4513; margin-bottom: 2rem;">
            Register as <?php echo ucfirst($user_type); ?>
        </h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
                <a href="login.php">Click here to login</a>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="hidden" name="user_type" value="<?php echo $user_type; ?>">
            
            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone">
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="3"></textarea>
            </div>
            
            <?php if ($user_type == 'vendor'): ?>
                <div class="form-group">
                    <label for="shop_name">Shop Name *</label>
                    <input type="text" id="shop_name" name="shop_name" required>
                </div>
                
                <div class="form-group">
                    <label for="shop_description">Shop Description</label>
                    <textarea id="shop_description" name="shop_description" rows="3"></textarea>
                </div>
            <?php endif; ?>
            
            <button type="submit" class="btn" style="width: 100%;">Register</button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem;">
            Already have an account? <a href="login.php" style="color: #D2691E;">Login here</a>
        </p>
        
        <p style="text-align: center; margin-top: 0.5rem;">
            <?php if ($user_type == 'vendor'): ?>
                <a href="register.php" style="color: #D2691E;">Register as Customer instead</a>
            <?php else: ?>
                <a href="register.php?type=vendor" style="color: #D2691E;">Register as Vendor instead</a>
            <?php endif; ?>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
