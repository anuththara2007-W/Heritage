<?php
$pageTitle = 'Our Story';
include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="hero" style="margin: -2rem -20px 2rem;">
        <h1>Our Story</h1>
        <p>A Journey of Cultural Preservation and Artisan Empowerment</p>
    </div>
    
    <div class="card">
        <h2>The Beginning</h2>
        <p>
            Heritage was born from a passion for preserving Sri Lanka's rich cultural heritage and 
            supporting local artisans. Our founders recognized that traditional crafts were at risk 
            of being forgotten in the modern age, and talented artisans struggled to reach wider markets. 
            This realization sparked the idea to create a platform that bridges the gap between tradition 
            and technology.
        </p>
    </div>
    
    <div class="card">
        <h2>Our Journey</h2>
        <p>
            What started as a small initiative to help a handful of local craftspeople has grown into 
            a thriving marketplace featuring hundreds of authentic Sri Lankan products. We've traveled 
            across the island, visiting villages and meeting with artisans who have dedicated their 
            lives to perfecting their crafts.
        </p>
        
        <div style="margin-top: 2rem;">
            <h3>The Artisans We Work With</h3>
            <p>
                From the mask makers of Ambalangoda to the batik artists of Matara, from the woodcarvers 
                of Galle to the potters of Pinnawala - each artisan has a story, a tradition passed down 
                through generations. We're honored to be part of their journey and to help share their 
                work with the world.
            </p>
        </div>
    </div>
    
    <div class="card">
        <h2>Cultural Impact</h2>
        <p>
            Every purchase made through Heritage has a direct impact on preserving Sri Lankan culture:
        </p>
        <ul style="margin-top: 1rem;">
            <li>Supporting families who depend on traditional crafts for their livelihood</li>
            <li>Encouraging young people to learn and continue traditional art forms</li>
            <li>Preserving ancient techniques that might otherwise be lost</li>
            <li>Promoting Sri Lankan culture to a global audience</li>
            <li>Creating sustainable income for rural communities</li>
        </ul>
    </div>
    
    <div class="card">
        <h2>Looking Forward</h2>
        <p>
            As we continue to grow, our commitment remains unchanged: to preserve Sri Lankan cultural 
            heritage while empowering local artisans. We're constantly working to:
        </p>
        <ul style="margin-top: 1rem;">
            <li>Expand our network of artisans and craftspeople</li>
            <li>Introduce new traditional crafts to our platform</li>
            <li>Provide training and resources to help artisans improve their craft and business skills</li>
            <li>Share the stories and cultural significance behind each product</li>
            <li>Make authentic Sri Lankan crafts accessible to customers worldwide</li>
        </ul>
    </div>
    
    <div class="card" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
        <h2 style="color: var(--accent-color);">Join Our Story</h2>
        <p>
            When you shop with Heritage, you're not just buying a product - you're becoming part of 
            a story that spans centuries. You're helping preserve traditions, support families, and 
            keep cultural heritage alive for future generations.
        </p>
        <div style="text-align: center; margin-top: 2rem;">
            <a href="/pages/shop.php" class="btn btn-large" 
               style="background-color: white; color: var(--primary-color); border-color: white;">
                Start Shopping
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
