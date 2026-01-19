<?php
/**
 * AUTO SPARE WORKSHOP - User Class
 * Handles user authentication and management
 */

class User {
    private $id;
    private $email;
    private $name;
    private $avatar;
    private $role;
    private $phone;
    private $loyaltyPoints;
    
    /**
     * Find user by ID
     */
    public static function find($id) {
        $data = fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
        return $data ? new self($data) : null;
    }
    
    /**
     * Find user by email
     */
    public static function findByEmail($email) {
        $data = fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
        return $data ? new self($data) : null;
    }
    
    /**
     * Get all users with pagination
     */
    public static function all($page = 1, $perPage = ITEMS_PER_PAGE, $search = '') {
        $offset = ($page - 1) * $perPage;
        
        if ($search) {
            $search = "%{$search}%";
            return fetchAll(
                "SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?",
                [$search, $search, $perPage, $offset]
            );
        }
        
        return fetchAll("SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?", [$perPage, $offset]);
    }
    
    /**
     * Count total users
     */
    public static function count($search = '') {
        if ($search) {
            $search = "%{$search}%";
            return fetchOne("SELECT COUNT(*) as total FROM users WHERE name LIKE ? OR email LIKE ?", [$search, $search])['total'];
        }
        return fetchOne("SELECT COUNT(*) as total FROM users")['total'];
    }
    
    /**
     * Register new user
     */
    public static function register($data) {
        // Check if email exists
        if (self::findByEmail($data['email'])) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Hash password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Insert user
        $userId = insert('users', [
            'email' => $data['email'],
            'password' => $hashedPassword,
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($data['name']),
            'role' => 'user',
            'loyalty_points' => 0
        ]);
        
        logAudit('user_registered', 'users', $userId);
        
        return ['success' => true, 'user_id' => $userId];
    }
    
    /**
     * Login user
     */
    public static function login($email, $password) {
        $user = fetchOne("SELECT * FROM users WHERE email = ? AND is_active = 1", [$email]);
        
        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        // Update last login
        update('users', [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $_SERVER['REMOTE_ADDR'] ?? null
        ], 'id = :id', ['id' => $user['id']]);
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_avatar'] = $user['avatar'];
        $_SESSION['last_activity'] = time();
        
        logAudit('user_login', 'users', $user['id']);
        
        return ['success' => true, 'user' => $user];
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        if (isLoggedIn()) {
            logAudit('user_logout', 'users', $_SESSION['user_id']);
        }
        session_unset();
        session_destroy();
    }
    
    /**
     * Update user profile
     */
    public function update($data) {
        $updateData = [];
        
        if (isset($data['name'])) $updateData['name'] = $data['name'];
        if (isset($data['phone'])) $updateData['phone'] = $data['phone'];
        if (isset($data['avatar'])) $updateData['avatar'] = $data['avatar'];
        
        if (!empty($data['password'])) {
            $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (!empty($updateData)) {
            update('users', $updateData, 'id = :id', ['id' => $this->id]);
            logAudit('user_updated', 'users', $this->id);
        }
        
        return true;
    }
    
    /**
     * Add loyalty points
     */
    public function addLoyaltyPoints($points) {
        query("UPDATE users SET loyalty_points = loyalty_points + ? WHERE id = ?", [$points, $this->id]);
        $this->loyaltyPoints += $points;
    }
    
    /**
     * Get user's orders
     */
    public function getOrders($limit = 10) {
        return fetchAll(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ?",
            [$this->id, $limit]
        );
    }
    
    /**
     * Get user's vehicles
     */
    public function getVehicles() {
        return fetchAll("SELECT * FROM vehicles WHERE user_id = ? ORDER BY is_default DESC, created_at DESC", [$this->id]);
    }
    
    /**
     * Get user's addresses
     */
    public function getAddresses() {
        return fetchAll("SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC", [$this->id]);
    }
    
    /**
     * Constructor
     */
    public function __construct($data) {
        $this->id = $data['id'];
        $this->email = $data['email'];
        $this->name = $data['name'];
        $this->avatar = $data['avatar'];
        $this->role = $data['role'];
        $this->phone = $data['phone'] ?? null;
        $this->loyaltyPoints = $data['loyalty_points'];
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getEmail() { return $this->email; }
    public function getName() { return $this->name; }
    public function getAvatar() { return $this->avatar; }
    public function getRole() { return $this->role; }
    public function getPhone() { return $this->phone; }
    public function getLoyaltyPoints() { return $this->loyaltyPoints; }
    public function isAdmin() { return $this->role === 'admin'; }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'avatar' => $this->avatar,
            'role' => $this->role,
            'phone' => $this->phone,
            'loyalty_points' => $this->loyaltyPoints
        ];
    }
}
?>
