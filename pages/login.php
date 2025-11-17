<?php
$pageTitle = 'Login';
include __DIR__ . '/../includes/header.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("SELECT user_id, username, email, password_hash, user_type, status FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if ($user['status'] === 'inactive') {
                $error = 'Your account has been deactivated. Please contact support.';
            } elseif (verifyPassword($password, $user['password_hash'])) {
                loginUser($user['user_id'], $user['username'], $user['user_type']);
                
                setFlashMessage('Welcome back, ' . $user['username'] . '!', 'success');
                
                if ($user['user_type'] === 'admin') {
                    redirect('/admin/dashboard.php');
                } else {
                    redirect('/index.php');
                }
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
        
        $stmt->close();
        closeDBConnection($conn);
    }
}
?>

<div class="container">
    <div class="card" style="max-width: 500px; margin: 2rem auto;">
        <h2 class="text-center">Login to Heritage</h2>
        
        <?php if ($error): ?>
            <div class="flash-message flash-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        
        <p class="text-center mt-1">
            Don't have an account? <a href="/pages/register.php">Register here</a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
