<?php
/**
 * AUTO SPARE WORKSHOP - Order Class
 * Handles order management
 */

class Order {
    private $id;
    private $orderNumber;
    private $userId;
    private $status;
    private $subtotal;
    private $tax;
    private $shipping;
    private $discount;
    private $total;
    private $paymentStatus;
    private $trackingNumber;
    private $createdAt;
    
    /**
     * Find order by ID
     */
    public static function find($id) {
        $data = fetchOne("SELECT * FROM orders WHERE id = ?", [$id]);
        return $data ? new self($data) : null;
    }
    
    /**
     * Find order by order number
     */
    public static function findByNumber($orderNumber) {
        $data = fetchOne("SELECT * FROM orders WHERE order_number = ?", [$orderNumber]);
        return $data ? new self($data) : null;
    }
    
    /**
     * Get user's orders
     */
    public static function getUserOrders($userId, $page = 1, $perPage = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $perPage;
        return fetchAll(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$userId, $perPage, $offset]
        );
    }
    
    /**
     * Get all orders with filters (admin)
     */
    public static function all($options = []) {
        $page = $options['page'] ?? 1;
        $perPage = $options['perPage'] ?? ITEMS_PER_PAGE;
        $status = $options['status'] ?? null;
        $search = $options['search'] ?? null;
        $dateFrom = $options['dateFrom'] ?? null;
        $dateTo = $options['dateTo'] ?? null;
        
        $offset = ($page - 1) * $perPage;
        $conditions = ['1=1'];
        $params = [];
        
        if ($status) {
            $conditions[] = 'status = ?';
            $params[] = $status;
        }
        
        if ($search) {
            $conditions[] = '(order_number LIKE ? OR JSON_EXTRACT(shipping_address, "$.name") LIKE ?)';
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        if ($dateFrom) {
            $conditions[] = 'created_at >= ?';
            $params[] = $dateFrom;
        }
        
        if ($dateTo) {
            $conditions[] = 'created_at <= ?';
            $params[] = $dateTo . ' 23:59:59';
        }
        
        $whereClause = implode(' AND ', $conditions);
        $params[] = $perPage;
        $params[] = $offset;
        
        return fetchAll(
            "SELECT o.*, u.name as customer_name, u.email as customer_email 
             FROM orders o 
             LEFT JOIN users u ON o.user_id = u.id 
             WHERE {$whereClause} 
             ORDER BY o.created_at DESC 
             LIMIT ? OFFSET ?",
            $params
        );
    }
    
    /**
     * Count orders
     */
    public static function count($options = []) {
        $status = $options['status'] ?? null;
        
        if ($status) {
            return fetchOne("SELECT COUNT(*) as total FROM orders WHERE status = ?", [$status])['total'];
        }
        
        return fetchOne("SELECT COUNT(*) as total FROM orders")['total'];
    }
    
    /**
     * Create order from cart
     */
    public static function createFromCart($userId, $shippingAddress, $billingAddress, $paymentMethod, $couponCode = null) {
        $cartItems = Cart::getItems();
        
        if (empty($cartItems)) {
            return ['success' => false, 'message' => 'Cart is empty'];
        }
        
        $totals = Cart::getTotals($couponCode);
        $orderNumber = generateOrderNumber();
        
        $pdo = getDBConnection();
        $pdo->beginTransaction();
        
        try {
            // Create order
            $orderId = insert('orders', [
                'order_number' => $orderNumber,
                'user_id' => $userId,
                'status' => 'pending',
                'subtotal' => $totals['subtotal'],
                'tax' => $totals['tax'],
                'shipping' => $totals['shipping'],
                'discount' => $totals['discount'],
                'total' => $totals['total'],
                'shipping_address' => json_encode($shippingAddress),
                'billing_address' => json_encode($billingAddress),
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'coupon_code' => $couponCode
            ]);
            
            // Create order items and update stock
            foreach ($cartItems as $item) {
                insert('order_items', [
                    'order_id' => $orderId,
                    'product_id' => $item['product']['id'],
                    'product_name' => $item['product']['name'],
                    'product_sku' => $item['product']['part_number'],
                    'product_image' => $item['product']['images'][0] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['product']['sale_price'] ?? $item['product']['price'],
                    'total' => $item['subtotal']
                ]);
                
                // Update stock
                $product = Product::find($item['product']['id']);
                $product->updateStock($item['quantity'], 'subtract');
            }
            
            // Update coupon usage
            if ($couponCode) {
                query("UPDATE coupons SET usage_count = usage_count + 1 WHERE code = ?", [$couponCode]);
            }
            
            // Add loyalty points (1 point per $1 spent)
            $user = User::find($userId);
            $user->addLoyaltyPoints(floor($totals['total']));
            
            // Clear cart
            Cart::clear();
            
            $pdo->commit();
            
            logAudit('order_created', 'orders', $orderId);
            
            // Send notification
            insert('notifications', [
                'user_id' => $userId,
                'title' => 'Order Placed!',
                'message' => "Your order {$orderNumber} has been placed successfully.",
                'type' => 'order',
                'link' => "/order.php?id={$orderId}"
            ]);
            
            return [
                'success' => true,
                'order_id' => $orderId,
                'order_number' => $orderNumber
            ];
            
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['success' => false, 'message' => 'Failed to create order: ' . $e->getMessage()];
        }
    }
    
    /**
     * Update order status
     */
    public function updateStatus($status) {
        $validStatuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];
        
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'message' => 'Invalid status'];
        }
        
        $oldStatus = $this->status;
        
        $updateData = ['status' => $status];
        
        if ($status === 'shipped') {
            $updateData['shipped_at'] = date('Y-m-d H:i:s');
        } elseif ($status === 'delivered') {
            $updateData['delivered_at'] = date('Y-m-d H:i:s');
        }
        
        update('orders', $updateData, 'id = :id', ['id' => $this->id]);
        
        logAudit('order_status_updated', 'orders', $this->id, ['status' => $oldStatus], ['status' => $status]);
        
        // Send notification to user
        insert('notifications', [
            'user_id' => $this->userId,
            'title' => 'Order Status Updated',
            'message' => "Your order {$this->orderNumber} is now {$status}.",
            'type' => 'order',
            'link' => "/order.php?id={$this->id}"
        ]);
        
        return ['success' => true];
    }
    
    /**
     * Add tracking number
     */
    public function addTracking($trackingNumber, $carrier = null) {
        update('orders', [
            'tracking_number' => $trackingNumber,
            'carrier' => $carrier
        ], 'id = :id', ['id' => $this->id]);
        
        logAudit('tracking_added', 'orders', $this->id);
        
        return ['success' => true];
    }
    
    /**
     * Get order items
     */
    public function getItems() {
        return fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$this->id]);
    }
    
    /**
     * Get customer
     */
    public function getCustomer() {
        return User::find($this->userId);
    }
    
    /**
     * Get shipping address
     */
    public function getShippingAddress() {
        $address = fetchOne("SELECT shipping_address FROM orders WHERE id = ?", [$this->id]);
        return json_decode($address['shipping_address'], true);
    }
    
    /**
     * Get analytics data
     */
    public static function getAnalytics($period = 'month') {
        $dateFormat = $period === 'day' ? '%Y-%m-%d' : '%Y-%m';
        $dateLimit = $period === 'day' ? 30 : 12;
        
        $data = fetchAll(
            "SELECT 
                DATE_FORMAT(created_at, '{$dateFormat}') as date,
                COUNT(*) as orders,
                SUM(total) as revenue,
                AVG(total) as average_order_value
             FROM orders 
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? {$period})
             AND payment_status = 'paid'
             GROUP BY DATE_FORMAT(created_at, '{$dateFormat}')
             ORDER BY date",
            [$dateLimit]
        );
        
        return $data;
    }
    
    /**
     * Get dashboard stats
     */
    public static function getDashboardStats() {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m-01');
        $lastMonth = date('Y-m-01', strtotime('-1 month'));
        
        $currentMonthStats = fetchOne(
            "SELECT COUNT(*) as orders, COALESCE(SUM(total), 0) as revenue 
             FROM orders WHERE created_at >= ? AND payment_status = 'paid'",
            [$thisMonth]
        );
        
        $lastMonthStats = fetchOne(
            "SELECT COUNT(*) as orders, COALESCE(SUM(total), 0) as revenue 
             FROM orders WHERE created_at >= ? AND created_at < ? AND payment_status = 'paid'",
            [$lastMonth, $thisMonth]
        );
        
        $totalCustomers = fetchOne("SELECT COUNT(*) as total FROM users WHERE role = 'user'")['total'];
        $totalProducts = fetchOne("SELECT COUNT(*) as total FROM products WHERE is_active = 1")['total'];
        $lowStockCount = fetchOne("SELECT COUNT(*) as total FROM products WHERE stock > 0 AND stock <= low_stock_threshold")['total'];
        
        $revenueChange = $lastMonthStats['revenue'] > 0 
            ? (($currentMonthStats['revenue'] - $lastMonthStats['revenue']) / $lastMonthStats['revenue']) * 100 
            : 0;
        
        $ordersChange = $lastMonthStats['orders'] > 0 
            ? (($currentMonthStats['orders'] - $lastMonthStats['orders']) / $lastMonthStats['orders']) * 100 
            : 0;
        
        return [
            'total_revenue' => $currentMonthStats['revenue'],
            'total_orders' => $currentMonthStats['orders'],
            'total_customers' => $totalCustomers,
            'total_products' => $totalProducts,
            'revenue_change' => round($revenueChange, 1),
            'orders_change' => round($ordersChange, 1),
            'low_stock_products' => $lowStockCount
        ];
    }
    
    /**
     * Constructor
     */
    public function __construct($data) {
        $this->id = $data['id'];
        $this->orderNumber = $data['order_number'];
        $this->userId = $data['user_id'];
        $this->status = $data['status'];
        $this->subtotal = $data['subtotal'];
        $this->tax = $data['tax'];
        $this->shipping = $data['shipping'];
        $this->discount = $data['discount'];
        $this->total = $data['total'];
        $this->paymentStatus = $data['payment_status'];
        $this->trackingNumber = $data['tracking_number'];
        $this->createdAt = $data['created_at'];
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getOrderNumber() { return $this->orderNumber; }
    public function getUserId() { return $this->userId; }
    public function getStatus() { return $this->status; }
    public function getSubtotal() { return $this->subtotal; }
    public function getTax() { return $this->tax; }
    public function getShipping() { return $this->shipping; }
    public function getDiscount() { return $this->discount; }
    public function getTotal() { return $this->total; }
    public function getPaymentStatus() { return $this->paymentStatus; }
    public function getTrackingNumber() { return $this->trackingNumber; }
    public function getCreatedAt() { return $this->createdAt; }
    
    public function getStatusColor() {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed', 'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled', 'refunded' => 'destructive',
            default => 'secondary'
        };
    }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'order_number' => $this->orderNumber,
            'user_id' => $this->userId,
            'status' => $this->status,
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'shipping' => $this->shipping,
            'discount' => $this->discount,
            'total' => $this->total,
            'payment_status' => $this->paymentStatus,
            'tracking_number' => $this->trackingNumber,
            'created_at' => $this->createdAt
        ];
    }
}
?>
