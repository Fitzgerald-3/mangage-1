<?php

/**
 * AUTH USAGE EXAMPLES
 * 
 * This file shows you how to use the flexible Auth class with:
 * 1. Your own database implementation
 * 2. Custom admin defaults  
 * 3. Custom security settings
 * 4. Different database types (mysqli, PDO, custom)
 */

require_once '../backend/src/Auth_Database.php';
use App\Auth;

// ================================================================
// EXAMPLE 1: BASIC USAGE WITH YOUR ADMIN DEFAULTS
// ================================================================

// Simple setup with your own admin credentials
$auth = new Auth();
$auth->setAdminDefaults('your_admin', 'your_secure_password', 'admin@yourcompany.com', 'Your', 'Name')
     ->setPasswordRequirements(8, true, true, false) // 8 chars, uppercase, numbers required
     ->setSecuritySettings(7200, 3, 1800); // 2 hour session, 3 attempts, 30 min lock

// ================================================================
// EXAMPLE 2: MYSQLI DATABASE IMPLEMENTATION
// ================================================================

// Your existing mysqli connection
$mysqli = new mysqli('localhost', 'username', 'password', 'database');

// Configure Auth with your database
$auth = new Auth();
$auth->setDatabaseConnection($mysqli, 'your_users_table')
     ->setAdminDefaults('admin', 'SecurePass123!', 'admin@nananom-farms.com');

// If your table has different field names, map them:
$fieldMapping = [
    'username' => 'user_name',        // Your field => Auth expects
    'password' => 'user_password',
    'email' => 'user_email',
    'first_name' => 'fname',
    'last_name' => 'lname'
];

$auth->setDatabaseConnection($mysqli, 'custom_users', $fieldMapping);

// ================================================================
// EXAMPLE 3: PDO DATABASE IMPLEMENTATION  
// ================================================================

// Your existing PDO connection
$pdo = new PDO('mysql:host=localhost;dbname=your_db', 'username', 'password');

$auth = new Auth();
$auth->setDatabaseConnection($pdo, 'users')
     ->setAdminDefaults('administrator', 'MySecurePassword2024', 'admin@yoursite.com')
     ->setPasswordRequirements(10, true, true, true); // Strong password requirements

// ================================================================
// EXAMPLE 4: CUSTOM DATABASE IMPLEMENTATION
// ================================================================

// For custom databases, extend the Auth class
class CustomAuth extends Auth {
    
    // Override this method for your custom database
    protected function dbExecuteQuery($query, $params = []) {
        
        // EXAMPLE: Custom API-based database
        if ($this->dbConnection instanceof YourCustomDatabaseClass) {
            return $this->executeWithCustomDB($query, $params);
        }
        
        // EXAMPLE: MongoDB implementation
        if ($this->dbConnection instanceof MongoDB\Client) {
            return $this->executeWithMongoDB($query, $params);
        }
        
        // EXAMPLE: Redis implementation
        if ($this->dbConnection instanceof Redis) {
            return $this->executeWithRedis($query, $params);
        }
        
        // Fallback to parent implementation
        return parent::dbExecuteQuery($query, $params);
    }
    
    private function executeWithCustomDB($query, $params) {
        // Your custom database logic here
        // Convert SQL-like query to your database format
        // Return results in the expected format
    }
    
    private function executeWithMongoDB($query, $params) {
        // MongoDB implementation
        // Parse SQL-like query and convert to MongoDB operations
    }
    
    private function executeWithRedis($query, $params) {
        // Redis implementation for caching/sessions
    }
}

// Usage with custom database
$customDB = new YourCustomDatabaseClass();
$auth = new CustomAuth();
$auth->setDatabaseConnection($customDB, 'users')
     ->setAdminDefaults('admin', 'password123', 'admin@site.com');

// ================================================================
// EXAMPLE 5: COMPLETE CONFIGURATION
// ================================================================

// Full configuration with all options
$config = [
    'admin_defaults' => [
        'username' => 'super_admin',
        'password' => 'VerySecurePassword2024!',
        'email' => 'admin@nananom-farms.com',
        'first_name' => 'Super',
        'last_name' => 'Administrator',
        'role' => 'admin'
    ],
    'password_requirements' => [
        'min_length' => 12,
        'require_uppercase' => true,
        'require_numbers' => true,
        'require_special_chars' => true
    ],
    'security' => [
        'session_timeout' => 14400, // 4 hours
        'max_login_attempts' => 3,
        'lock_duration' => 3600, // 1 hour
        'remember_me_duration' => 604800 // 1 week
    ],
    'database' => [
        'use_database' => true,
        'table_name' => 'app_users',
        'fields' => [
            'id' => 'user_id',
            'username' => 'login_name',
            'password' => 'password_hash',
            'email' => 'email_address',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'role' => 'user_role',
            'status' => 'account_status',
            'login_attempts' => 'failed_attempts',
            'locked_until' => 'locked_until',
            'last_login' => 'last_login_time',
            'created_at' => 'created_timestamp',
            'updated_at' => 'updated_timestamp'
        ]
    ]
];

$auth = new Auth($mysqli, $config);

// ================================================================
// EXAMPLE 6: USAGE IN YOUR APPLICATION
// ================================================================

// In your login.php file:
require_once 'backend/src/Auth_Database.php';

// Setup your database connection
$mysqli = new mysqli('localhost', 'db_user', 'db_pass', 'database');

// Initialize Auth with your settings
$auth = new Auth();
$auth->setDatabaseConnection($mysqli)
     ->setAdminDefaults('your_admin', 'your_password', 'admin@yoursite.com');

// Handle login
if ($_POST) {
    $result = $auth->login($_POST['username'], $_POST['password']);
    
    if ($result['success']) {
        header('Location: dashboard.php');
    } else {
        $error = $result['message'];
    }
}

// ================================================================
// EXAMPLE 7: SWITCHING BETWEEN FILE AND DATABASE STORAGE
// ================================================================

// Development: Use file storage
$auth = new Auth(); // Uses file storage by default

// Production: Use database
$auth = new Auth();
$auth->setDatabaseConnection($mysqli, 'users')
     ->setAdminDefaults('admin', 'secure_password', 'admin@site.com');

// ================================================================
// EXAMPLE 8: CUSTOM TABLE STRUCTURE
// ================================================================

// If you have a completely different table structure:
$customFields = [
    'id' => 'pk_user_id',
    'username' => 'login_username', 
    'password' => 'encrypted_password',
    'email' => 'contact_email',
    'first_name' => 'given_name',
    'last_name' => 'family_name',
    'role' => 'permission_level',
    'status' => 'is_active',
    'login_attempts' => 'login_failures',
    'locked_until' => 'unlock_time',
    'last_login' => 'previous_login',
    'created_at' => 'registration_date',
    'updated_at' => 'last_modified'
];

$auth = new Auth();
$auth->setDatabaseConnection($pdo, 'members', $customFields)
     ->setAdminDefaults('root', 'RootPassword123', 'root@domain.com');

// ================================================================
// REQUIRED DATABASE TABLE STRUCTURE (if using database)
// ================================================================

/*
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('admin', 'support', 'user') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    login_attempts INT DEFAULT 0,
    locked_until DATETIME NULL,
    last_login DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
*/

// ================================================================
// TESTING YOUR IMPLEMENTATION
// ================================================================

function testAuth($auth) {
    echo "Testing authentication...\n";
    
    // Test admin creation
    $adminDefaults = $auth->getAdminDefaults();
    echo "Admin username: " . $adminDefaults['username'] . "\n";
    
    // Test login
    $result = $auth->login($adminDefaults['username'], $adminDefaults['password']);
    echo "Login test: " . ($result['success'] ? 'SUCCESS' : 'FAILED') . "\n";
    
    // Test session
    echo "Is logged in: " . ($auth->isLoggedIn() ? 'YES' : 'NO') . "\n";
    
    // Test user data
    $user = $auth->getUser();
    echo "User: " . json_encode($user) . "\n";
    
    // Test logout
    $auth->logout();
    echo "After logout: " . ($auth->isLoggedIn() ? 'STILL LOGGED IN' : 'LOGGED OUT') . "\n";
}

// Test your implementation
// testAuth($auth);

?>