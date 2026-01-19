<?php
/**
 * AUTO SPARE WORKSHOP - Main Configuration
 * Premium Auto Parts & Services Platform
 */

// Start session
session_start();

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Application settings
define('SITE_NAME', 'Auto Spare Workshop');
define('SITE_URL', 'http://localhost/autospare');
define('SITE_EMAIL', 'support@autospare.com');

// Security settings
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_TIMEOUT', 3600); // 1 hour

// Pagination
define('ITEMS_PER_PAGE', 12);

// Upload settings
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Include required files
require_once __DIR__ . '/database.php';

// Auto-load classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// CSRF Protection
function generateCSRFToken() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function validateCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// Session timeout check
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_unset();
        session_destroy();
        header('Location: ' . SITE_URL . '/login.php?timeout=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Require authentication
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/login.php');
        exit;
    }
    checkSessionTimeout();
}

// Require admin access
function requireAdmin() {
    requireAuth();
    if (!isAdmin()) {
        header('Location: ' . SITE_URL . '/dashboard.php');
        exit;
    }
}

// Get current user
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    return fetchOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
}

// Flash messages
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Format price
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// Generate order number
function generateOrderNumber() {
    return 'ORD-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

// Log audit trail
function logAudit($action, $entityType = null, $entityId = null, $oldValues = null, $newValues = null) {
    if (isLoggedIn()) {
        insert('audit_logs', [
            'user_id' => $_SESSION['user_id'],
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
}
?>
