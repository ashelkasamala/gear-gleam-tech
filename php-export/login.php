<?php
/**
 * AUTO SPARE WORKSHOP - Login Page
 */

require_once 'config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? '/admin/index.php' : '/dashboard.php'));
    exit;
}

$error = '';
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $result = User::login($email, $password);
        
        if ($result['success']) {
            Cart::mergeOnLogin();
            
            $redirectTo = $_GET['redirect'] ?? ($result['user']['role'] === 'admin' ? '/admin/index.php' : '/dashboard.php');
            header('Location: ' . $redirectTo);
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = 'Sign In';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="auth-page">
        <!-- Left Side - Form -->
        <div class="auth-form-side">
            <div class="auth-form-container">
                <a href="/" class="logo mb-8">
                    <div class="logo-icon">AS</div>
                    <div>
                        <span class="logo-text">Auto Spare</span>
                        <span class="logo-subtext">Workshop</span>
                    </div>
                </a>
                
                <div class="card card-glass">
                    <div class="card-header">
                        <h1 class="card-title text-2xl">Welcome back</h1>
                        <p class="card-description">Sign in to your account to continue</p>
                    </div>
                    
                    <form method="POST" action="" data-validate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="card-content">
                            <?php if ($error): ?>
                            <div class="alert alert-error mb-4" style="padding: 0.75rem; border-radius: 0.5rem; background-color: hsla(0, 84%, 60%, 0.1); color: var(--destructive); font-size: 0.875rem;">
                                <?php echo $error; ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="mb-4">
                                <label class="label" for="email">Email</label>
                                <div class="input-group">
                                    <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                    <input type="email" id="email" name="email" class="input" placeholder="name@example.com" value="<?php echo htmlspecialchars($email); ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="label mb-0" for="password">Password</label>
                                    <a href="/forgot-password.php" class="text-sm text-primary">Forgot password?</a>
                                </div>
                                <div class="input-group">
                                    <svg class="input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                    <input type="password" id="password" name="password" class="input" placeholder="••••••••" required>
                                </div>
                            </div>
                            
                            <!-- Demo Credentials -->
                            <div class="demo-credentials">
                                <p class="demo-credentials-title">Demo Credentials:</p>
                                <button type="button" class="demo-credential" onclick="fillCredentials('admin@autospare.com', 'admin123')">
                                    Admin: admin@autospare.com
                                </button>
                                <button type="button" class="demo-credential" onclick="fillCredentials('user@example.com', 'user123')">
                                    User: user@example.com
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-lg w-full">
                                Sign In
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </button>
                            <p class="text-center text-sm text-muted mt-4">
                                Don't have an account?
                                <a href="/register.php" class="text-primary font-medium">Create one</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Image -->
        <div class="auth-image-side">
            <div class="auth-image-content">
                <h2 class="text-3xl font-bold mb-4">
                    Your trusted partner for auto parts
                </h2>
                <p class="text-muted mb-8" style="color: rgba(255,255,255,0.7);">
                    Access your dashboard, track orders, book services, and chat with our support team - all in one place.
                </p>
                <ul class="auth-features">
                    <li class="auth-feature">
                        <span class="auth-feature-dot"></span>
                        <span>Track your orders in real-time</span>
                    </li>
                    <li class="auth-feature">
                        <span class="auth-feature-dot"></span>
                        <span>Book workshop services online</span>
                    </li>
                    <li class="auth-feature">
                        <span class="auth-feature-dot"></span>
                        <span>Earn loyalty points with every purchase</span>
                    </li>
                    <li class="auth-feature">
                        <span class="auth-feature-dot"></span>
                        <span>Get exclusive member discounts</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <script>
        function fillCredentials(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
    <script src="/assets/js/main.js"></script>
</body>
</html>
