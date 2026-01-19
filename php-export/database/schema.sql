-- ============================================
-- AUTO SPARE WORKSHOP - DATABASE SCHEMA
-- Premium Auto Parts & Services Platform
-- ============================================

-- Drop existing tables if they exist
DROP TABLE IF EXISTS chat_messages;
DROP TABLE IF EXISTS chat_conversations;
DROP TABLE IF EXISTS wishlist;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS service_bookings;
DROP TABLE IF EXISTS vehicles;
DROP TABLE IF EXISTS product_reviews;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS addresses;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS coupons;
DROP TABLE IF EXISTS users;

-- ============================================
-- USERS TABLE
-- ============================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    avatar VARCHAR(500),
    role ENUM('user', 'admin', 'moderator') DEFAULT 'user',
    phone VARCHAR(50),
    loyalty_points INT DEFAULT 0,
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    two_factor_secret VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(255),
    last_login_at TIMESTAMP NULL,
    last_login_ip VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- ============================================
-- CATEGORIES TABLE
-- ============================================
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    icon VARCHAR(100),
    parent_id INT NULL,
    description TEXT,
    image VARCHAR(500),
    product_count INT DEFAULT 0,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_parent (parent_id)
);

-- ============================================
-- PRODUCTS TABLE
-- ============================================
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    sale_price DECIMAL(10, 2) NULL,
    sku VARCHAR(100) UNIQUE,
    part_number VARCHAR(100),
    brand VARCHAR(100),
    category_id INT,
    stock INT DEFAULT 0,
    low_stock_threshold INT DEFAULT 10,
    images JSON,
    specifications JSON,
    compatibility JSON,
    rating DECIMAL(2, 1) DEFAULT 0,
    review_count INT DEFAULT 0,
    views INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_category (category_id),
    INDEX idx_brand (brand),
    INDEX idx_stock (stock),
    INDEX idx_featured (is_featured),
    FULLTEXT idx_search (name, description, brand, part_number)
);

-- ============================================
-- ADDRESSES TABLE
-- ============================================
CREATE TABLE addresses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    line1 VARCHAR(255) NOT NULL,
    line2 VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'USA',
    phone VARCHAR(50),
    is_default BOOLEAN DEFAULT FALSE,
    address_type ENUM('shipping', 'billing', 'both') DEFAULT 'both',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
);

-- ============================================
-- VEHICLES TABLE (User's Vehicle Garage)
-- ============================================
CREATE TABLE vehicles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    make VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    year INT NOT NULL,
    vin VARCHAR(17),
    license_plate VARCHAR(20),
    mileage INT,
    color VARCHAR(50),
    nickname VARCHAR(100),
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_make_model (make, model)
);

-- ============================================
-- ORDERS TABLE
-- ============================================
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending',
    subtotal DECIMAL(10, 2) NOT NULL,
    tax DECIMAL(10, 2) DEFAULT 0,
    shipping DECIMAL(10, 2) DEFAULT 0,
    discount DECIMAL(10, 2) DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL,
    shipping_address_id INT,
    billing_address_id INT,
    shipping_address JSON,
    billing_address JSON,
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_transaction_id VARCHAR(255),
    tracking_number VARCHAR(100),
    carrier VARCHAR(100),
    notes TEXT,
    coupon_code VARCHAR(50),
    shipped_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_order_number (order_number),
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
);

-- ============================================
-- ORDER ITEMS TABLE
-- ============================================
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_sku VARCHAR(100),
    product_image VARCHAR(500),
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    INDEX idx_order (order_id)
);

-- ============================================
-- SERVICE BOOKINGS TABLE
-- ============================================
CREATE TABLE service_bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_number VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    vehicle_id INT,
    service_type VARCHAR(100) NOT NULL,
    service_date DATE NOT NULL,
    time_slot VARCHAR(50) NOT NULL,
    status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    estimated_cost DECIMAL(10, 2),
    actual_cost DECIMAL(10, 2),
    customer_notes TEXT,
    technician_notes TEXT,
    assigned_technician_id INT,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_date (service_date),
    INDEX idx_status (status)
);

-- ============================================
-- PRODUCT REVIEWS TABLE
-- ============================================
CREATE TABLE product_reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    order_id INT,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    title VARCHAR(255),
    content TEXT,
    images JSON,
    is_verified_purchase BOOLEAN DEFAULT FALSE,
    helpful_count INT DEFAULT 0,
    is_approved BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_product (product_id),
    INDEX idx_rating (rating)
);

-- ============================================
-- CART ITEMS TABLE
-- ============================================
CREATE TABLE cart_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user (user_id)
);

-- ============================================
-- WISHLIST TABLE
-- ============================================
CREATE TABLE wishlist (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id),
    INDEX idx_user (user_id)
);

-- ============================================
-- CHAT CONVERSATIONS TABLE
-- ============================================
CREATE TABLE chat_conversations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    admin_id INT,
    status ENUM('active', 'closed') DEFAULT 'active',
    subject VARCHAR(255),
    last_message_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_status (status)
);

-- ============================================
-- CHAT MESSAGES TABLE
-- ============================================
CREATE TABLE chat_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    conversation_id INT NOT NULL,
    sender_id INT NOT NULL,
    message_type ENUM('text', 'image', 'file', 'system') DEFAULT 'text',
    content TEXT NOT NULL,
    attachments JSON,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES chat_conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_conversation (conversation_id),
    INDEX idx_sender (sender_id),
    INDEX idx_created (created_at)
);

-- ============================================
-- NOTIFICATIONS TABLE
-- ============================================
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('order', 'service', 'promotion', 'system', 'chat') DEFAULT 'system',
    link VARCHAR(500),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_read (is_read),
    INDEX idx_type (type)
);

-- ============================================
-- COUPONS TABLE
-- ============================================
CREATE TABLE coupons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) NOT NULL UNIQUE,
    type ENUM('percentage', 'fixed') NOT NULL,
    value DECIMAL(10, 2) NOT NULL,
    min_order_amount DECIMAL(10, 2),
    max_discount DECIMAL(10, 2),
    usage_limit INT,
    usage_count INT DEFAULT 0,
    valid_from TIMESTAMP NOT NULL,
    valid_to TIMESTAMP NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_dates (valid_from, valid_to)
);

-- ============================================
-- AUDIT LOGS TABLE (for security)
-- ============================================
CREATE TABLE audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100),
    entity_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
);

-- ============================================
-- INSERT SAMPLE DATA
-- ============================================

-- Insert Admin User (password: admin123)
INSERT INTO users (email, password, name, avatar, role, loyalty_points) VALUES
('admin@autospare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'https://api.dicebear.com/7.x/avataaars/svg?seed=admin', 'admin', 0),
('user@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Customer', 'https://api.dicebear.com/7.x/avataaars/svg?seed=john', 'user', 2500),
('sarah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah Miller', 'https://api.dicebear.com/7.x/avataaars/svg?seed=sarah', 'user', 1200);

-- Insert Categories
INSERT INTO categories (name, slug, icon, product_count) VALUES
('Engine Parts', 'engine-parts', 'cog', 156),
('Brake System', 'brake-system', 'disc', 89),
('Suspension', 'suspension', 'car', 67),
('Electrical', 'electrical', 'zap', 234),
('Filters', 'filters', 'filter', 45),
('Transmission', 'transmission', 'cog', 78),
('Body Parts', 'body-parts', 'car', 189),
('Accessories', 'accessories', 'package', 312);

-- Insert Products
INSERT INTO products (name, slug, description, price, sale_price, part_number, brand, category_id, stock, images, specifications, rating, review_count) VALUES
('Premium Ceramic Brake Pads Set', 'premium-ceramic-brake-pads', 'High-performance ceramic brake pads with low dust formula. Provides superior stopping power and quiet operation.', 89.99, 74.99, 'BP-2024-CR', 'Brembo', 2, 45, '["placeholder.svg"]', '{"Material": "Ceramic", "Position": "Front", "Warranty": "3 Years"}', 4.8, 234),
('Synthetic Motor Oil 5W-30', 'synthetic-motor-oil-5w30', 'Full synthetic motor oil providing superior engine protection and improved fuel economy.', 49.99, NULL, 'M1-5W30-5QT', 'Mobil 1', 1, 120, '["placeholder.svg"]', '{"Viscosity": "5W-30", "Volume": "5 Quarts", "Type": "Full Synthetic"}', 4.9, 567),
('LED Headlight Conversion Kit', 'led-headlight-kit', 'Ultra-bright LED headlight bulbs with 6000K white light. Easy plug-and-play installation.', 129.99, 99.99, 'LED-H11-6K', 'Philips', 4, 8, '["placeholder.svg"]', '{"Color Temperature": "6000K", "Lumens": "12000", "Warranty": "2 Years"}', 4.6, 189),
('Heavy Duty Air Filter', 'heavy-duty-air-filter', 'High-flow air filter for improved engine performance and filtration.', 34.99, NULL, 'AF-KN-2024', 'K&N', 5, 0, '["placeholder.svg"]', '{"Type": "High-Flow", "Washable": "Yes", "Warranty": "Lifetime"}', 4.7, 89),
('Complete Suspension Kit', 'complete-suspension-kit', 'Front and rear suspension kit including struts, springs, and mounting hardware.', 599.99, 499.99, 'SUSP-COMPLETE', 'Monroe', 3, 12, '["placeholder.svg"]', '{"Position": "Front & Rear", "Includes": "Struts, Springs, Mounts"}', 4.5, 45),
('Performance Spark Plugs (Set of 4)', 'performance-spark-plugs', 'Iridium spark plugs for improved ignition and fuel efficiency.', 44.99, NULL, 'NGK-IR-4SET', 'NGK', 1, 89, '["placeholder.svg"]', '{"Material": "Iridium", "Gap": "0.044", "Quantity": "4"}', 4.8, 312);
