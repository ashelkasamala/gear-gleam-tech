<?php
/**
 * AUTO SPARE WORKSHOP - Homepage
 * Premium Auto Parts & Services Platform
 */

require_once 'config/config.php';

// Get featured products
$featuredProducts = Product::featured(4);
$categories = fetchAll("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order LIMIT 6");

$pageTitle = 'Premium Auto Parts & Workshop Services';
$pageDescription = 'Your one-stop destination for quality auto parts and professional workshop services.';

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-container">
            <span class="hero-badge">
                🔧 Premium Auto Parts & Services
            </span>
            
            <h1 class="hero-title">
                Quality Parts for
                <span class="highlight">Every Vehicle</span>
            </h1>
            
            <p class="hero-description">
                Your one-stop destination for premium auto parts and professional workshop services. 
                Trusted by thousands of car owners nationwide.
            </p>
            
            <div class="hero-actions">
                <a href="/shop.php" class="btn btn-gradient btn-lg">
                    Shop Now
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
                <a href="/services.php" class="btn btn-outline btn-lg" style="border-color: rgba(255,255,255,0.3); color: white;">
                    Book Service
                </a>
            </div>
            
            <div class="hero-stats">
                <div>
                    <div class="hero-stat-value">50K+</div>
                    <div class="hero-stat-label">Products</div>
                </div>
                <div>
                    <div class="hero-stat-value">15K+</div>
                    <div class="hero-stat-label">Happy Customers</div>
                </div>
                <div>
                    <div class="hero-stat-value">99%</div>
                    <div class="hero-stat-label">Satisfaction</div>
                </div>
                <div>
                    <div class="hero-stat-value">24/7</div>
                    <div class="hero-stat-label">Support</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Bar -->
<section class="features-bar">
    <div class="container">
        <div class="grid grid-cols-3 gap-6">
            <div class="feature-item">
                <div class="feature-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="feature-title">Expert Service</h3>
                    <p class="feature-description">Professional mechanics with years of experience</p>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="feature-title">Quality Guaranteed</h3>
                    <p class="feature-description">All parts come with warranty protection</p>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                </div>
                <div>
                    <h3 class="feature-title">Fast Delivery</h3>
                    <p class="feature-description">Free shipping on orders over $50</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-16">
    <div class="container">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold">Shop by Category</h2>
                <p class="text-muted mt-1">Find parts for every system in your vehicle</p>
            </div>
            <a href="/categories.php" class="btn btn-ghost">
                View All
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </a>
        </div>
        
        <div class="grid grid-cols-6 gap-4">
            <?php foreach ($categories as $index => $category): ?>
            <a href="/shop.php?category=<?php echo $category['id']; ?>" class="card card-interactive animate-fade-in" style="animation-delay: <?php echo $index * 50; ?>ms">
                <div class="category-card">
                    <div class="category-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                    </div>
                    <h3 class="category-name"><?php echo htmlspecialchars($category['name']); ?></h3>
                    <p class="category-count"><?php echo $category['product_count']; ?> products</p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-16" style="background-color: var(--secondary);">
    <div class="container">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold">Featured Products</h2>
                <p class="text-muted mt-1">Top-rated parts from trusted brands</p>
            </div>
            <a href="/shop.php" class="btn btn-ghost">
                View All
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </a>
        </div>
        
        <div class="grid grid-cols-4 gap-6">
            <?php foreach ($featuredProducts as $index => $product): 
                $images = json_decode($product['images'], true);
                $image = $images[0] ?? 'placeholder.svg';
            ?>
            <a href="/product.php?slug=<?php echo $product['slug']; ?>" class="card card-interactive product-card animate-fade-in" style="animation-delay: <?php echo $index * 100; ?>ms">
                <div class="product-image-container">
                    <img src="/assets/images/<?php echo $image; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                    
                    <?php if ($product['sale_price']): ?>
                    <span class="badge badge-destructive product-badge">Sale</span>
                    <?php endif; ?>
                    
                    <?php if ($product['stock'] == 0): ?>
                    <span class="badge badge-secondary product-stock-badge">Out of Stock</span>
                    <?php elseif ($product['stock'] <= 10): ?>
                    <span class="badge badge-warning product-stock-badge">Low Stock</span>
                    <?php endif; ?>
                </div>
                
                <div class="product-info">
                    <p class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></p>
                    <h3 class="product-name line-clamp-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                    
                    <div class="product-rating">
                        <div class="flex gap-1">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="<?php echo $i <= floor($product['rating']) ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2" class="star <?php echo $i <= floor($product['rating']) ? 'filled' : ''; ?>">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-count">(<?php echo $product['review_count']; ?>)</span>
                    </div>
                    
                    <div class="product-price">
                        <?php if ($product['sale_price']): ?>
                        <span class="current-price sale">$<?php echo number_format($product['sale_price'], 2); ?></span>
                        <span class="original-price">$<?php echo number_format($product['price'], 2); ?></span>
                        <?php else: ?>
                        <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2 class="cta-title">Need Professional Service?</h2>
        <p class="cta-description">
            Book an appointment with our certified mechanics. Quality service, fair prices, 
            and your satisfaction guaranteed.
        </p>
        <div class="cta-actions">
            <a href="/services.php" class="btn btn-secondary btn-lg">
                Book Service
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
            <a href="/contact.php" class="btn btn-outline btn-lg" style="border-color: rgba(255,255,255,0.3); color: white;">
                Contact Us
            </a>
        </div>
    </div>
</section>

<!-- Brands Section -->
<section class="py-16">
    <div class="container">
        <h2 class="text-center text-xl font-semibold text-muted mb-8">
            Trusted Brands We Carry
        </h2>
        <div class="flex justify-center items-center gap-16 flex-wrap">
            <?php 
            $brands = ['Bosch', 'NGK', 'Brembo', 'Monroe', 'K&N', 'Mobil 1'];
            foreach ($brands as $brand): 
            ?>
            <div class="text-2xl font-bold text-muted opacity-40 transition hover:opacity-70">
                <?php echo $brand; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
