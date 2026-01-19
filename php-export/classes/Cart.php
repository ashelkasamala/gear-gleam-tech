<?php
/**
 * AUTO SPARE WORKSHOP - Cart Class
 * Handles shopping cart functionality
 */

class Cart {
    
    /**
     * Initialize cart in session
     */
    public static function init() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }
    
    /**
     * Get cart items
     */
    public static function getItems() {
        self::init();
        $items = [];
        
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $items[] = [
                    'product' => $product->toArray(),
                    'quantity' => $quantity,
                    'subtotal' => $product->getCurrentPrice() * $quantity
                ];
            }
        }
        
        return $items;
    }
    
    /**
     * Add item to cart
     */
    public static function add($productId, $quantity = 1) {
        self::init();
        
        $product = Product::find($productId);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }
        
        if (!$product->isInStock()) {
            return ['success' => false, 'message' => 'Product is out of stock'];
        }
        
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
        
        // Check stock limit
        if ($_SESSION['cart'][$productId] > $product->getStock()) {
            $_SESSION['cart'][$productId] = $product->getStock();
            return ['success' => true, 'message' => 'Quantity adjusted to available stock'];
        }
        
        // If user is logged in, sync to database
        if (isLoggedIn()) {
            self::syncToDatabase();
        }
        
        return ['success' => true, 'message' => 'Added to cart'];
    }
    
    /**
     * Update item quantity
     */
    public static function update($productId, $quantity) {
        self::init();
        
        if ($quantity <= 0) {
            return self::remove($productId);
        }
        
        $product = Product::find($productId);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }
        
        if ($quantity > $product->getStock()) {
            $quantity = $product->getStock();
        }
        
        $_SESSION['cart'][$productId] = $quantity;
        
        if (isLoggedIn()) {
            self::syncToDatabase();
        }
        
        return ['success' => true, 'message' => 'Cart updated'];
    }
    
    /**
     * Remove item from cart
     */
    public static function remove($productId) {
        self::init();
        
        unset($_SESSION['cart'][$productId]);
        
        if (isLoggedIn()) {
            delete('cart_items', 'user_id = ? AND product_id = ?', [$_SESSION['user_id'], $productId]);
        }
        
        return ['success' => true, 'message' => 'Item removed'];
    }
    
    /**
     * Clear cart
     */
    public static function clear() {
        $_SESSION['cart'] = [];
        
        if (isLoggedIn()) {
            delete('cart_items', 'user_id = ?', [$_SESSION['user_id']]);
        }
    }
    
    /**
     * Get cart count
     */
    public static function getCount() {
        self::init();
        return array_sum($_SESSION['cart']);
    }
    
    /**
     * Get cart totals
     */
    public static function getTotals($couponCode = null) {
        $items = self::getItems();
        
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['subtotal'];
        }
        
        $discount = 0;
        if ($couponCode) {
            $coupon = fetchOne(
                "SELECT * FROM coupons WHERE code = ? AND is_active = 1 AND valid_from <= NOW() AND valid_to >= NOW() AND (usage_limit IS NULL OR usage_count < usage_limit)",
                [$couponCode]
            );
            
            if ($coupon && ($coupon['min_order_amount'] === null || $subtotal >= $coupon['min_order_amount'])) {
                if ($coupon['type'] === 'percentage') {
                    $discount = $subtotal * ($coupon['value'] / 100);
                    if ($coupon['max_discount'] && $discount > $coupon['max_discount']) {
                        $discount = $coupon['max_discount'];
                    }
                } else {
                    $discount = $coupon['value'];
                }
            }
        }
        
        $tax = ($subtotal - $discount) * 0.08; // 8% tax
        $shipping = $subtotal >= 50 ? 0 : 9.99; // Free shipping over $50
        $total = $subtotal - $discount + $tax + $shipping;
        
        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'item_count' => self::getCount()
        ];
    }
    
    /**
     * Sync cart to database
     */
    public static function syncToDatabase() {
        if (!isLoggedIn()) return;
        
        $userId = $_SESSION['user_id'];
        
        // Clear existing cart items
        delete('cart_items', 'user_id = ?', [$userId]);
        
        // Insert current cart items
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            insert('cart_items', [
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }
    }
    
    /**
     * Load cart from database
     */
    public static function loadFromDatabase() {
        if (!isLoggedIn()) return;
        
        $userId = $_SESSION['user_id'];
        $items = fetchAll("SELECT product_id, quantity FROM cart_items WHERE user_id = ?", [$userId]);
        
        self::init();
        
        foreach ($items as $item) {
            $_SESSION['cart'][$item['product_id']] = $item['quantity'];
        }
    }
    
    /**
     * Merge guest cart with user cart on login
     */
    public static function mergeOnLogin() {
        if (!isLoggedIn()) return;
        
        // Get database cart
        $userId = $_SESSION['user_id'];
        $dbItems = fetchAll("SELECT product_id, quantity FROM cart_items WHERE user_id = ?", [$userId]);
        
        // Merge with session cart
        foreach ($dbItems as $item) {
            if (isset($_SESSION['cart'][$item['product_id']])) {
                // Keep the higher quantity
                $_SESSION['cart'][$item['product_id']] = max(
                    $_SESSION['cart'][$item['product_id']],
                    $item['quantity']
                );
            } else {
                $_SESSION['cart'][$item['product_id']] = $item['quantity'];
            }
        }
        
        // Sync back to database
        self::syncToDatabase();
    }
}
?>
