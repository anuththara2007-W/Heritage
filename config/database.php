<?php
/**
 * Database Configuration
 * Update these values according to your database setup
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'heritage_db');
define('DB_USER', 'root');
define('DB_PASS', '');

/**
 * Create database connection
 */
function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        die("Database connection error: " . $e->getMessage());
    }
}

/**
 * Close database connection
 */
function closeDBConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}
?>
