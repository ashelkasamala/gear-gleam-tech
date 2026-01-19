<?php
/**
 * AUTO SPARE WORKSHOP - Header Include
 */
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'dark' : ''; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Auto Spare Workshop'; ?> - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="<?php echo $pageDescription ?? 'Premium auto parts and professional workshop services.'; ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container header-container">
            <!-- Logo -->
            <a href="/" class="logo">
                <div class="logo-icon">AS</div>
                <div>
                    <span class="logo-text">Auto Spare</span>
                    <span class="logo-subtext">Workshop</span>
                </div>
            </a>
            
            <!-- Navigation -->
            <nav class="nav">
                <?php
                $navLinks = [
                    ['label' => 'Shop', 'href' => '/shop.php'],
                    ['label' => 'Categories', 'href' => '/categories.php'],
                    ['label' => 'Services', 'href' => '/services.php'],
                    ['label' => 'Deals', 'href' => '/deals.php'],
                    ['label' => 'About', 'href' => '/about.php'],
                ];
                
                $currentPage = $_SERVER['REQUEST_URI'];
                
                foreach ($navLinks as $link):
                    $isActive = strpos($currentPage, $link['href']) === 0;
                ?>
                <a href="<?php echo $link['href']; ?>" class="nav-link <?php echo $isActive ? 'active' : ''; ?>">
                    <?php echo $link['label']; ?>
                </a>
                <?php endforeach; ?>
            </nav>
            
            <!-- Search -->
            <div class="search-input">
                <div class="input-group">
                    <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" class="input input-filled" placeholder="Search parts, brands, models...">
                </div>
            </div>
            
            <!-- Actions -->
            <div class="header-actions">
                <!-- Theme Toggle -->
                <button class="btn btn-ghost btn-icon theme-toggle" title="Toggle theme">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                </button>
                
                <?php if (isLoggedIn()): ?>
                <!-- Notifications -->
                <button class="btn btn-ghost btn-icon relative">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <?php 
                    $unreadNotifications = fetchOne("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0", [$_SESSION['user_id']])['count'];
                    if ($unreadNotifications > 0): 
                    ?>
                    <span class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-destructive text-xs text-white flex items-center justify-center">
                        <?php echo $unreadNotifications; ?>
                    </span>
                    <?php endif; ?>
                </button>
                
                <?php if (!isAdmin()): ?>
                <!-- Wishlist -->
                <a href="/wishlist.php" class="btn btn-ghost btn-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                </a>
                <?php endif; ?>
                <?php endif; ?>
                
                <?php if (!isAdmin()): ?>
                <!-- Cart -->
                <a href="/cart.php" class="btn btn-ghost btn-icon relative">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <?php 
                    $cartCount = Cart::getCount();
                    if ($cartCount > 0): 
                    ?>
                    <span class="cart-count absolute -top-1 -right-1 h-4 w-4 rounded-full bg-primary text-xs text-white flex items-center justify-center">
                        <?php echo $cartCount; ?>
                    </span>
                    <?php endif; ?>
                </a>
                <?php endif; ?>
                
                <!-- User Menu -->
                <?php if (isLoggedIn()): ?>
                <div class="relative">
                    <a href="<?php echo isAdmin() ? '/admin/index.php' : '/dashboard.php'; ?>" class="btn btn-ghost flex items-center gap-2">
                        <img src="<?php echo $_SESSION['user_avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default'; ?>" alt="Avatar" class="h-8 w-8 rounded-full">
                        <span class="font-medium text-sm"><?php echo explode(' ', $_SESSION['user_name'])[0]; ?></span>
                    </a>
                </div>
                <?php else: ?>
                <div class="flex items-center gap-2">
                    <a href="/login.php" class="btn btn-ghost btn-sm">Sign in</a>
                    <a href="/register.php" class="btn btn-primary btn-sm">Get Started</a>
                </div>
                <?php endif; ?>
                
                <!-- Mobile Menu Toggle -->
                <button class="btn btn-ghost btn-icon mobile-menu-toggle" style="display: none;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Mobile Navigation -->
        <nav class="mobile-menu hidden py-4 border-t">
            <div class="container flex flex-col gap-1">
                <?php foreach ($navLinks as $link): ?>
                <a href="<?php echo $link['href']; ?>" class="nav-link">
                    <?php echo $link['label']; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </nav>
    </header>
    
    <main>
