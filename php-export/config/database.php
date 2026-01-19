<?php
/**
 * AUTO SPARE WORKSHOP - Database Configuration
 * Premium Auto Parts & Services Platform
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'autospare_workshop');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Create PDO connection
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

// Simple query helper
function query($sql, $params = []) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

// Fetch single row
function fetchOne($sql, $params = []) {
    return query($sql, $params)->fetch();
}

// Fetch all rows
function fetchAll($sql, $params = []) {
    return query($sql, $params)->fetchAll();
}

// Insert and return last ID
function insert($table, $data) {
    $pdo = getDBConnection();
    $columns = implode(', ', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));
    
    $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
    
    return $pdo->lastInsertId();
}

// Update rows
function update($table, $data, $where, $whereParams = []) {
    $pdo = getDBConnection();
    $set = [];
    foreach (array_keys($data) as $column) {
        $set[] = "{$column} = :{$column}";
    }
    
    $sql = "UPDATE {$table} SET " . implode(', ', $set) . " WHERE {$where}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_merge($data, $whereParams));
    
    return $stmt->rowCount();
}

// Delete rows
function delete($table, $where, $params = []) {
    $pdo = getDBConnection();
    $sql = "DELETE FROM {$table} WHERE {$where}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->rowCount();
}
?>
