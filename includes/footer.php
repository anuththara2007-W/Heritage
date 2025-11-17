    </main>
    
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Heritage</h3>
                    <p>Authentic Sri Lankan cultural items and handicrafts. Supporting local artisans and preserving our heritage.</p>
                    <div class="social-links">
                        <a href="#" title="Facebook">📘</a>
                        <a href="#" title="Instagram">📷</a>
                        <a href="#" title="WhatsApp">💬</a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="/index.php">Home</a></li>
                        <li><a href="/pages/shop.php">Shop</a></li>
                        <li><a href="/pages/categories.php">Categories</a></li>
                        <li><a href="/pages/about.php">About Us</a></li>
                        <li><a href="/pages/our-story.php">Our Story</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Customer Service</h3>
                    <ul>
                        <li><a href="/pages/contact.php">Contact Us</a></li>
                        <li><a href="/pages/account.php">My Account</a></li>
                        <li><a href="/pages/reviews.php">Customer Reviews</a></li>
                        <li><a href="#">Shipping Info</a></li>
                        <li><a href="#">Returns Policy</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <ul>
                        <li>📍 123 Galle Road, Colombo 03, Sri Lanka</li>
                        <li>📞 +94 11 234 5678</li>
                        <li>✉️ info@heritage.lk</li>
                        <li>🕒 Mon - Sat: 9:00 AM - 6:00 PM</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Heritage Marketplace. All rights reserved.</p>
                <p>Preserving Sri Lankan Culture, One Artifact at a Time</p>
            </div>
        </div>
    </footer>
    
    <script src="/assets/js/main.js"></script>
    <?php if (isset($additionalJS)): ?>
        <script src="<?php echo $additionalJS; ?>"></script>
    <?php endif; ?>
</body>
</html>
