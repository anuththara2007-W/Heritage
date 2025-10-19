<?php
$pageTitle = 'Contact Us';
include __DIR__ . '/../includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } elseif (!isValidEmail($email)) {
        $error = 'Please enter a valid email address.';
    } else {
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("INSERT INTO contact_inquiries (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        
        if ($stmt->execute()) {
            $success = 'Thank you for contacting us! We will respond to your inquiry soon.';
            // Clear form
            $_POST = [];
        } else {
            $error = 'Error submitting inquiry. Please try again.';
        }
        
        $stmt->close();
        closeDBConnection($conn);
    }
}
?>

<div class="container">
    <h1 class="text-center">Contact Us</h1>
    <p class="text-center">We'd love to hear from you! Get in touch with us for any questions or inquiries.</p>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
        <div class="card">
            <h2>Send Us a Message</h2>
            
            <?php if ($error): ?>
                <div class="flash-message flash-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="flash-message flash-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required 
                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" class="form-control" 
                           value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea id="message" name="message" class="form-control" rows="5" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Send Message</button>
            </form>
        </div>
        
        <div>
            <div class="card">
                <h2>Contact Information</h2>
                
                <div style="margin-bottom: 1.5rem;">
                    <h4>📍 Address</h4>
                    <p>123 Galle Road<br>Colombo 03<br>Sri Lanka</p>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <h4>📞 Phone</h4>
                    <p>+94 11 234 5678</p>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <h4>✉️ Email</h4>
                    <p>info@heritage.lk<br>support@heritage.lk</p>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <h4>🕒 Business Hours</h4>
                    <p>Monday - Saturday: 9:00 AM - 6:00 PM<br>Sunday: Closed</p>
                </div>
            </div>
            
            <div class="card">
                <h2>Connect With Us</h2>
                <p>Follow us on social media for updates and promotions:</p>
                
                <div style="display: flex; gap: 1rem; font-size: 2rem; margin-top: 1rem;">
                    <a href="#" title="Facebook">📘</a>
                    <a href="#" title="Instagram">📷</a>
                    <a href="#" title="Twitter">🐦</a>
                    <a href="#" title="WhatsApp">💬</a>
                </div>
            </div>
            
            <div class="card">
                <h2>Customer Support</h2>
                <p>Need immediate assistance? Chat with our AI support bot or reach us on WhatsApp!</p>
                
                <a href="#" class="btn btn-primary btn-block" style="margin-bottom: 0.5rem;">
                    💬 Chat with AI Bot
                </a>
                <a href="https://wa.me/94112345678" class="btn btn-block" target="_blank">
                    WhatsApp Support
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
