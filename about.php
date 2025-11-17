<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

$pageTitle = 'About Us - Heritage';
include 'includes/header.php';
?>

<div class="container">
    <div style="background: white; padding: 3rem; border-radius: 10px; margin: 2rem 0;">
        <h2 style="text-align: center; color: #8B4513; margin-bottom: 2rem;">About Heritage</h2>
        
        <div style="max-width: 900px; margin: 0 auto;">
            <h3 style="color: #8B4513; margin-top: 2rem;">Our Mission</h3>
            <p style="line-height: 1.8; color: #666;">
                Heritage is dedicated to preserving and promoting Sri Lankan arts and crafts by providing a 
                platform for local artisans and vendors to showcase their traditional products to a wider audience. 
                We believe in supporting local businesses and preserving our rich cultural heritage for future generations.
            </p>
            
            <h3 style="color: #8B4513; margin-top: 2rem;">What We Offer</h3>
            <ul style="line-height: 2; color: #666;">
                <li>A marketplace for authentic Sri Lankan arts and crafts</li>
                <li>Direct connection between local vendors and customers</li>
                <li>Secure and user-friendly shopping experience</li>
                <li>Support for traditional artisans and craftspeople</li>
                <li>Wide variety of categories including handcrafts, paintings, pottery, textiles, and more</li>
            </ul>
            
            <h3 style="color: #8B4513; margin-top: 2rem;">For Vendors</h3>
            <p style="line-height: 1.8; color: #666;">
                Heritage provides local vendors with an easy-to-use platform to showcase their products online. 
                Create your shop, list your products, manage orders, and grow your business with our comprehensive 
                vendor dashboard. We handle the technical aspects so you can focus on creating beautiful crafts.
            </p>
            
            <h3 style="color: #8B4513; margin-top: 2rem;">For Customers</h3>
            <p style="line-height: 1.8; color: #666;">
                Browse our extensive collection of authentic Sri Lankan arts and crafts, read reviews from other 
                customers, and purchase directly from local artisans. Each purchase supports local businesses and 
                helps preserve our cultural heritage. Enjoy a secure checkout process and direct communication with vendors.
            </p>
            
            <h3 style="color: #8B4513; margin-top: 2rem;">Categories We Feature</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
                <div style="padding: 1rem; background: #f8f9fa; border-radius: 5px;">
                    <strong style="color: #8B4513;">Handcrafts</strong>
                    <p style="color: #666; font-size: 0.9rem;">Traditional Sri Lankan handcrafted items</p>
                </div>
                <div style="padding: 1rem; background: #f8f9fa; border-radius: 5px;">
                    <strong style="color: #8B4513;">Paintings</strong>
                    <p style="color: #666; font-size: 0.9rem;">Art by local artists</p>
                </div>
                <div style="padding: 1rem; background: #f8f9fa; border-radius: 5px;">
                    <strong style="color: #8B4513;">Pottery</strong>
                    <p style="color: #666; font-size: 0.9rem;">Ceramic and pottery items</p>
                </div>
                <div style="padding: 1rem; background: #f8f9fa; border-radius: 5px;">
                    <strong style="color: #8B4513;">Textiles</strong>
                    <p style="color: #666; font-size: 0.9rem;">Traditional fabrics</p>
                </div>
                <div style="padding: 1rem; background: #f8f9fa; border-radius: 5px;">
                    <strong style="color: #8B4513;">Jewelry</strong>
                    <p style="color: #666; font-size: 0.9rem;">Handmade accessories</p>
                </div>
                <div style="padding: 1rem; background: #f8f9fa; border-radius: 5px;">
                    <strong style="color: #8B4513;">Wood Carvings</strong>
                    <p style="color: #666; font-size: 0.9rem;">Wooden crafts</p>
                </div>
                <div style="padding: 1rem; background: #f8f9fa; border-radius: 5px;">
                    <strong style="color: #8B4513;">Batik</strong>
                    <p style="color: #666; font-size: 0.9rem;">Traditional batik art</p>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #ddd;">
                <h3 style="color: #8B4513; margin-bottom: 1rem;">Join Us Today!</h3>
                <p style="color: #666; margin-bottom: 1.5rem;">
                    Whether you're a vendor looking to showcase your crafts or a customer seeking authentic 
                    Sri Lankan products, Heritage is your destination.
                </p>
                <?php if (!isLoggedIn()): ?>
                    <a href="register.php" class="btn" style="margin-right: 1rem;">Register as Customer</a>
                    <a href="register.php?type=vendor" class="btn btn-secondary">Register as Vendor</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
