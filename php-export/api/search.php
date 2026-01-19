<?php
/**
 * AUTO SPARE WORKSHOP - Search API
 * Handles product search with autocomplete
 */

require_once '../config/config.php';

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';
$limit = min((int)($_GET['limit'] ?? 10), 20);

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

$results = Product::search($query, $limit);

// Format results for autocomplete
$formatted = array_map(function($product) {
    $images = json_decode($product['images'], true);
    return [
        'id' => $product['id'],
        'name' => $product['name'],
        'slug' => $product['slug'],
        'brand' => $product['brand'],
        'part_number' => $product['part_number'],
        'price' => $product['sale_price'] ?? $product['price'],
        'original_price' => $product['sale_price'] ? $product['price'] : null,
        'image' => $images[0] ?? 'placeholder.svg',
        'url' => '/product.php?slug=' . $product['slug']
    ];
}, $results);

echo json_encode($formatted);
?>
