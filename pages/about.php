<?php
$pageTitle = 'About Us';
include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="hero" style="margin: -2rem -20px 2rem;">
        <h1>About Heritage</h1>
        <p>Preserving Sri Lankan Culture Through Authentic Craftsmanship</p>
    </div>
    
    <div class="card">
        <h2>Our Mission</h2>
        <p>
            Heritage is a web-based marketplace designed to connect Sri Lankan vendors and customers 
            through the rich tapestry of local arts and crafts. We believe in preserving and promoting 
            Sri Lanka's cultural heritage by providing a platform for local artisans to showcase their 
            exceptional work to a global audience.
        </p>
    </div>
    
    <div class="card">
        <h2>What We Do</h2>
        <p>
            We allow local vendors to showcase their products online, while customers can browse, 
            purchase, and review authentic Sri Lankan cultural items. Our platform promotes local 
            businesses and provides a secure, user-friendly shopping experience.
        </p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 2rem;">
            <div>
                <h3>🎨 Authentic Products</h3>
                <p>Every item on Heritage is carefully selected to ensure authenticity and quality, representing the true essence of Sri Lankan craftsmanship.</p>
            </div>
            
            <div>
                <h3>🤝 Supporting Local Artisans</h3>
                <p>We work directly with local vendors and artisans, ensuring fair compensation and helping preserve traditional crafts.</p>
            </div>
            
            <div>
                <h3>🌍 Global Reach</h3>
                <p>We make it easy for customers around the world to discover and purchase authentic Sri Lankan cultural items.</p>
            </div>
            
            <div>
                <h3>🔒 Secure Shopping</h3>
                <p>Our platform provides a secure and reliable shopping experience with multiple payment options and buyer protection.</p>
            </div>
        </div>
    </div>
    
    <div class="card">
        <h2>Our Vision</h2>
        <p>
            To become the leading platform for Sri Lankan cultural items and handicrafts, preserving 
            traditional art forms while empowering local communities and artisans. We envision a future 
            where every purchase contributes to the sustainability of Sri Lankan heritage and the 
            livelihoods of skilled craftspeople.
        </p>
    </div>
    
    <div class="card">
        <h2>Why Choose Heritage?</h2>
        <ul style="list-style: none; padding: 0;">
            <li style="padding: 0.5rem 0;">✓ Wide selection of authentic Sri Lankan products</li>
            <li style="padding: 0.5rem 0;">✓ Direct support to local artisans and vendors</li>
            <li style="padding: 0.5rem 0;">✓ Secure payment and delivery options</li>
            <li style="padding: 0.5rem 0;">✓ Quality assurance on all products</li>
            <li style="padding: 0.5rem 0;">✓ Customer reviews and ratings</li>
            <li style="padding: 0.5rem 0;">✓ Responsive customer support</li>
            <li style="padding: 0.5rem 0;">✓ Easy returns and refunds policy</li>
        </ul>
    </div>
    
    <div class="text-center" style="margin: 3rem 0;">
        <h2>Start Your Journey</h2>
        <p>Discover the beauty and richness of Sri Lankan culture through our curated collection.</p>
        <a href="/pages/shop.php" class="btn btn-primary btn-large">Explore Products</a>
        <a href="/pages/contact.php" class="btn btn-large" style="margin-left: 1rem;">Contact Us</a>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
