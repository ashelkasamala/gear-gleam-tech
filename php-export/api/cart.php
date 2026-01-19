<?php
/**
 * AUTO SPARE WORKSHOP - Cart API
 * Handles cart operations via AJAX
 */

require_once '../config/config.php';

header('Content-Type: application/json');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        $productId = $input['product_id'] ?? 0;
        $quantity = $input['quantity'] ?? 1;
        
        $result = Cart::add($productId, $quantity);
        $result['cart_count'] = Cart::getCount();
        
        echo json_encode($result);
        break;
        
    case 'update':
        $productId = $input['product_id'] ?? 0;
        $quantity = $input['quantity'] ?? 1;
        
        $result = Cart::update($productId, $quantity);
        $result['cart_count'] = Cart::getCount();
        $result['totals'] = Cart::getTotals();
        
        echo json_encode($result);
        break;
        
    case 'remove':
        $productId = $input['product_id'] ?? 0;
        
        $result = Cart::remove($productId);
        $result['cart_count'] = Cart::getCount();
        $result['totals'] = Cart::getTotals();
        
        echo json_encode($result);
        break;
        
    case 'get':
        echo json_encode([
            'success' => true,
            'items' => Cart::getItems(),
            'totals' => Cart::getTotals(),
            'cart_count' => Cart::getCount()
        ]);
        break;
        
    case 'apply_coupon':
        $couponCode = $input['coupon_code'] ?? '';
        $totals = Cart::getTotals($couponCode);
        
        if ($totals['discount'] > 0) {
            $_SESSION['coupon_code'] = $couponCode;
            echo json_encode([
                'success' => true,
                'message' => 'Coupon applied!',
                'totals' => $totals
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid or expired coupon'
            ]);
        }
        break;
        
    case 'remove_coupon':
        unset($_SESSION['coupon_code']);
        echo json_encode([
            'success' => true,
            'totals' => Cart::getTotals()
        ]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
