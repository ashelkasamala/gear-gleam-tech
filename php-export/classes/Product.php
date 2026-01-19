<?php
/**
 * AUTO SPARE WORKSHOP - Product Class
 * Handles product management
 */

class Product {
    private $id;
    private $name;
    private $slug;
    private $description;
    private $price;
    private $salePrice;
    private $brand;
    private $partNumber;
    private $categoryId;
    private $stock;
    private $images;
    private $specifications;
    private $rating;
    private $reviewCount;
    
    /**
     * Find product by ID
     */
    public static function find($id) {
        $data = fetchOne("SELECT * FROM products WHERE id = ? AND is_active = 1", [$id]);
        return $data ? new self($data) : null;
    }
    
    /**
     * Find product by slug
     */
    public static function findBySlug($slug) {
        $data = fetchOne("SELECT * FROM products WHERE slug = ? AND is_active = 1", [$slug]);
        return $data ? new self($data) : null;
    }
    
    /**
     * Get all products with filters and pagination
     */
    public static function all($options = []) {
        $page = $options['page'] ?? 1;
        $perPage = $options['perPage'] ?? ITEMS_PER_PAGE;
        $category = $options['category'] ?? null;
        $brand = $options['brand'] ?? null;
        $search = $options['search'] ?? null;
        $minPrice = $options['minPrice'] ?? null;
        $maxPrice = $options['maxPrice'] ?? null;
        $inStock = $options['inStock'] ?? false;
        $sort = $options['sort'] ?? 'newest';
        
        $offset = ($page - 1) * $perPage;
        $conditions = ['is_active = 1'];
        $params = [];
        
        if ($category) {
            $conditions[] = 'category_id = ?';
            $params[] = $category;
        }
        
        if ($brand) {
            $conditions[] = 'brand = ?';
            $params[] = $brand;
        }
        
        if ($search) {
            $conditions[] = '(name LIKE ? OR description LIKE ? OR brand LIKE ? OR part_number LIKE ?)';
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
        }
        
        if ($minPrice !== null) {
            $conditions[] = 'COALESCE(sale_price, price) >= ?';
            $params[] = $minPrice;
        }
        
        if ($maxPrice !== null) {
            $conditions[] = 'COALESCE(sale_price, price) <= ?';
            $params[] = $maxPrice;
        }
        
        if ($inStock) {
            $conditions[] = 'stock > 0';
        }
        
        $orderBy = match($sort) {
            'price-low' => 'COALESCE(sale_price, price) ASC',
            'price-high' => 'COALESCE(sale_price, price) DESC',
            'popular' => 'review_count DESC',
            'rating' => 'rating DESC',
            default => 'created_at DESC'
        };
        
        $whereClause = implode(' AND ', $conditions);
        $params[] = $perPage;
        $params[] = $offset;
        
        return fetchAll(
            "SELECT * FROM products WHERE {$whereClause} ORDER BY {$orderBy} LIMIT ? OFFSET ?",
            $params
        );
    }
    
    /**
     * Count products with filters
     */
    public static function count($options = []) {
        $category = $options['category'] ?? null;
        $brand = $options['brand'] ?? null;
        $search = $options['search'] ?? null;
        $minPrice = $options['minPrice'] ?? null;
        $maxPrice = $options['maxPrice'] ?? null;
        $inStock = $options['inStock'] ?? false;
        
        $conditions = ['is_active = 1'];
        $params = [];
        
        if ($category) {
            $conditions[] = 'category_id = ?';
            $params[] = $category;
        }
        
        if ($brand) {
            $conditions[] = 'brand = ?';
            $params[] = $brand;
        }
        
        if ($search) {
            $conditions[] = '(name LIKE ? OR description LIKE ? OR brand LIKE ? OR part_number LIKE ?)';
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
        }
        
        if ($minPrice !== null) {
            $conditions[] = 'COALESCE(sale_price, price) >= ?';
            $params[] = $minPrice;
        }
        
        if ($maxPrice !== null) {
            $conditions[] = 'COALESCE(sale_price, price) <= ?';
            $params[] = $maxPrice;
        }
        
        if ($inStock) {
            $conditions[] = 'stock > 0';
        }
        
        $whereClause = implode(' AND ', $conditions);
        
        return fetchOne("SELECT COUNT(*) as total FROM products WHERE {$whereClause}", $params)['total'];
    }
    
    /**
     * Get featured products
     */
    public static function featured($limit = 4) {
        return fetchAll(
            "SELECT * FROM products WHERE is_active = 1 AND is_featured = 1 ORDER BY created_at DESC LIMIT ?",
            [$limit]
        );
    }
    
    /**
     * Get products on sale
     */
    public static function onSale($limit = 8) {
        return fetchAll(
            "SELECT * FROM products WHERE is_active = 1 AND sale_price IS NOT NULL ORDER BY created_at DESC LIMIT ?",
            [$limit]
        );
    }
    
    /**
     * Get low stock products
     */
    public static function lowStock($limit = 10) {
        return fetchAll(
            "SELECT * FROM products WHERE is_active = 1 AND stock > 0 AND stock <= low_stock_threshold ORDER BY stock ASC LIMIT ?",
            [$limit]
        );
    }
    
    /**
     * Get all unique brands
     */
    public static function getBrands() {
        return fetchAll("SELECT DISTINCT brand FROM products WHERE is_active = 1 AND brand IS NOT NULL ORDER BY brand");
    }
    
    /**
     * Search products (for autocomplete)
     */
    public static function search($query, $limit = 10) {
        $search = "%{$query}%";
        return fetchAll(
            "SELECT id, name, slug, brand, part_number, price, sale_price, images 
             FROM products 
             WHERE is_active = 1 AND (name LIKE ? OR brand LIKE ? OR part_number LIKE ?)
             LIMIT ?",
            [$search, $search, $search, $limit]
        );
    }
    
    /**
     * Create product
     */
    public static function create($data) {
        $slug = self::generateSlug($data['name']);
        
        $productId = insert('products', [
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'sale_price' => $data['sale_price'] ?? null,
            'sku' => $data['sku'] ?? null,
            'part_number' => $data['part_number'] ?? null,
            'brand' => $data['brand'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'stock' => $data['stock'] ?? 0,
            'images' => json_encode($data['images'] ?? []),
            'specifications' => json_encode($data['specifications'] ?? []),
            'is_featured' => $data['is_featured'] ?? false
        ]);
        
        logAudit('product_created', 'products', $productId);
        
        return $productId;
    }
    
    /**
     * Generate unique slug
     */
    private static function generateSlug($name) {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
        $slug = trim($slug, '-');
        
        $original = $slug;
        $counter = 1;
        
        while (fetchOne("SELECT id FROM products WHERE slug = ?", [$slug])) {
            $slug = $original . '-' . $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Update product
     */
    public function update($data) {
        $updateData = [];
        
        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
            $updateData['slug'] = self::generateSlug($data['name']);
        }
        if (isset($data['description'])) $updateData['description'] = $data['description'];
        if (isset($data['price'])) $updateData['price'] = $data['price'];
        if (array_key_exists('sale_price', $data)) $updateData['sale_price'] = $data['sale_price'];
        if (isset($data['stock'])) $updateData['stock'] = $data['stock'];
        if (isset($data['brand'])) $updateData['brand'] = $data['brand'];
        if (isset($data['category_id'])) $updateData['category_id'] = $data['category_id'];
        if (isset($data['images'])) $updateData['images'] = json_encode($data['images']);
        if (isset($data['specifications'])) $updateData['specifications'] = json_encode($data['specifications']);
        if (isset($data['is_featured'])) $updateData['is_featured'] = $data['is_featured'];
        
        if (!empty($updateData)) {
            update('products', $updateData, 'id = :id', ['id' => $this->id]);
            logAudit('product_updated', 'products', $this->id);
        }
        
        return true;
    }
    
    /**
     * Update stock
     */
    public function updateStock($quantity, $operation = 'set') {
        if ($operation === 'add') {
            query("UPDATE products SET stock = stock + ? WHERE id = ?", [$quantity, $this->id]);
        } elseif ($operation === 'subtract') {
            query("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?", [$quantity, $this->id, $quantity]);
        } else {
            query("UPDATE products SET stock = ? WHERE id = ?", [$quantity, $this->id]);
        }
    }
    
    /**
     * Get product reviews
     */
    public function getReviews($limit = 10) {
        return fetchAll(
            "SELECT r.*, u.name as user_name, u.avatar as user_avatar 
             FROM product_reviews r 
             JOIN users u ON r.user_id = u.id 
             WHERE r.product_id = ? AND r.is_approved = 1 
             ORDER BY r.created_at DESC 
             LIMIT ?",
            [$this->id, $limit]
        );
    }
    
    /**
     * Get category
     */
    public function getCategory() {
        return fetchOne("SELECT * FROM categories WHERE id = ?", [$this->categoryId]);
    }
    
    /**
     * Increment views
     */
    public function incrementViews() {
        query("UPDATE products SET views = views + 1 WHERE id = ?", [$this->id]);
    }
    
    /**
     * Constructor
     */
    public function __construct($data) {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->slug = $data['slug'];
        $this->description = $data['description'];
        $this->price = $data['price'];
        $this->salePrice = $data['sale_price'];
        $this->brand = $data['brand'];
        $this->partNumber = $data['part_number'];
        $this->categoryId = $data['category_id'];
        $this->stock = $data['stock'];
        $this->images = json_decode($data['images'] ?? '[]', true);
        $this->specifications = json_decode($data['specifications'] ?? '{}', true);
        $this->rating = $data['rating'];
        $this->reviewCount = $data['review_count'];
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getSlug() { return $this->slug; }
    public function getDescription() { return $this->description; }
    public function getPrice() { return $this->price; }
    public function getSalePrice() { return $this->salePrice; }
    public function getCurrentPrice() { return $this->salePrice ?? $this->price; }
    public function getBrand() { return $this->brand; }
    public function getPartNumber() { return $this->partNumber; }
    public function getCategoryId() { return $this->categoryId; }
    public function getStock() { return $this->stock; }
    public function getImages() { return $this->images; }
    public function getFirstImage() { return $this->images[0] ?? 'placeholder.svg'; }
    public function getSpecifications() { return $this->specifications; }
    public function getRating() { return $this->rating; }
    public function getReviewCount() { return $this->reviewCount; }
    public function isOnSale() { return $this->salePrice !== null; }
    public function isInStock() { return $this->stock > 0; }
    public function isLowStock() { return $this->stock > 0 && $this->stock <= 10; }
    
    public function getStockStatus() {
        if ($this->stock === 0) return 'out';
        if ($this->stock <= 10) return 'low';
        return 'high';
    }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'sale_price' => $this->salePrice,
            'brand' => $this->brand,
            'part_number' => $this->partNumber,
            'category_id' => $this->categoryId,
            'stock' => $this->stock,
            'images' => $this->images,
            'specifications' => $this->specifications,
            'rating' => $this->rating,
            'review_count' => $this->reviewCount
        ];
    }
}
?>
