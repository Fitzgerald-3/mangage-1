# 🗄️ Database Implementation Guide

This guide shows you how to implement your own database with the flexible Auth class while maintaining easy admin configuration.

## 🎯 **Quick Setup (3 Steps)**

### Step 1: Set Your Admin Defaults
```php
$auth = new Auth();
$auth->setAdminDefaults('your_admin', 'your_password', 'admin@yoursite.com');
```

### Step 2: Connect Your Database  
```php
// For MySQLi
$mysqli = new mysqli('localhost', 'username', 'password', 'database');
$auth->setDatabaseConnection($mysqli, 'your_users_table');

// For PDO
$pdo = new PDO('mysql:host=localhost;dbname=db', 'user', 'pass');
$auth->setDatabaseConnection($pdo, 'users');
```

### Step 3: Use It
```php
$result = $auth->login($username, $password);
if ($result['success']) {
    // User logged in successfully
}
```

---

## 🔧 **Configuration Options**

### Admin Defaults Configuration
```php
$auth->setAdminDefaults(
    'admin_username',           // Username
    'secure_password',          // Password  
    'admin@yoursite.com',       // Email
    'Your',                     // First name
    'Name'                      // Last name
);
```

### Password Requirements
```php
$auth->setPasswordRequirements(
    8,      // Minimum length
    true,   // Require uppercase
    true,   // Require numbers
    false   // Require special characters
);
```

### Security Settings
```php
$auth->setSecuritySettings(
    3600,   // Session timeout (seconds)
    5,      // Max login attempts
    900     // Lock duration (seconds)
);
```

---

## 🗄️ **Database Implementations**

### MySQLi Implementation
```php
<?php
// Your existing database connection
$mysqli = new mysqli('localhost', 'db_user', 'db_pass', 'database_name');

// Initialize Auth with your database
$auth = new Auth();
$auth->setDatabaseConnection($mysqli, 'users')
     ->setAdminDefaults('admin', 'MySecurePassword123', 'admin@nananom-farms.com')
     ->setPasswordRequirements(8, true, true, false);

// Ready to use!
$result = $auth->login($_POST['username'], $_POST['password']);
?>
```

### PDO Implementation
```php
<?php
// Your existing PDO connection
$pdo = new PDO('mysql:host=localhost;dbname=your_db', 'username', 'password');

// Configure Auth
$auth = new Auth();
$auth->setDatabaseConnection($pdo)
     ->setAdminDefaults('administrator', 'SecurePass2024', 'admin@site.com');

// Use in login
if ($_POST) {
    $result = $auth->login($_POST['username'], $_POST['password']);
    
    if ($result['success']) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = $result['message'];
    }
}
?>
```

### Custom Database Implementation
```php
<?php
// For custom databases, extend the Auth class
class MyCustomAuth extends Auth {
    
    protected function dbExecuteQuery($query, $params = []) {
        // Your custom database implementation
        if ($this->dbConnection instanceof YourDatabaseClass) {
            return $this->executeWithYourDB($query, $params);
        }
        
        // Fallback to parent
        return parent::dbExecuteQuery($query, $params);
    }
    
    private function executeWithYourDB($query, $params) {
        // Convert SQL query to your database format
        // Execute and return results
        // Format: array of associative arrays for SELECT
        // Boolean for INSERT/UPDATE/DELETE
    }
}

// Usage
$customDB = new YourDatabaseClass();
$auth = new MyCustomAuth();
$auth->setDatabaseConnection($customDB)
     ->setAdminDefaults('admin', 'password', 'admin@site.com');
?>
```

---

## 🏗️ **Required Database Table**

Create this table in your database (adjust field names as needed):

```sql
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
```

### Custom Field Mapping
If your table has different field names:

```php
$fieldMapping = [
    'username' => 'user_name',          // Your field => Auth expects
    'password' => 'user_password',
    'email' => 'user_email', 
    'first_name' => 'fname',
    'last_name' => 'lname',
    'role' => 'user_role',
    'status' => 'is_active',
    'login_attempts' => 'failed_logins',
    'locked_until' => 'unlock_time',
    'last_login' => 'last_login_date'
];

$auth->setDatabaseConnection($mysqli, 'custom_users', $fieldMapping);
```

---

## 🔀 **Switching Between Storage Methods**

### Development vs Production
```php
// Development: Use file storage (no database needed)
if ($_ENV['APP_ENV'] === 'development') {
    $auth = new Auth(); // Uses file storage
    $auth->setAdminDefaults('dev_admin', 'dev_password', 'dev@localhost');
}

// Production: Use database
if ($_ENV['APP_ENV'] === 'production') {
    $mysqli = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);
    $auth = new Auth();
    $auth->setDatabaseConnection($mysqli, 'users')
         ->setAdminDefaults('admin', $_ENV['ADMIN_PASSWORD'], 'admin@yoursite.com');
}
```

---

## 📝 **Complete Working Example**

Here's a complete working example for your login system:

```php
<?php
// login.php

require_once 'backend/src/Auth_Database.php';
use App\Auth;

// Database connection (replace with your details)
$mysqli = new mysqli('localhost', 'your_user', 'your_password', 'your_database');

if ($mysqli->connect_error) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

// Initialize Auth with your settings
$auth = new Auth();
$auth->setDatabaseConnection($mysqli, 'users')
     ->setAdminDefaults(
         'your_admin_username',      // Change this
         'your_secure_password',     // Change this  
         'admin@yoursite.com',       // Change this
         'Your',                     // Change this
         'Name'                      // Change this
     )
     ->setPasswordRequirements(8, true, true, false)  // Customize as needed
     ->setSecuritySettings(7200, 5, 1800);            // Customize as needed

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $result = $auth->login($username, $password);
        
        if ($result['success']) {
            header('Location: admin/dashboard.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

// Check if already logged in
if ($auth->isLoggedIn()) {
    header('Location: admin/dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Your Site</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
```

---

## 🛡️ **Security Features Included**

- ✅ **Password Hashing** - Uses PHP's secure `password_hash()`
- ✅ **Login Attempt Limiting** - Prevents brute force attacks
- ✅ **Account Locking** - Temporary lockouts after failed attempts
- ✅ **Session Management** - Configurable timeout and security
- ✅ **Input Sanitization** - All inputs are properly escaped
- ✅ **SQL Injection Protection** - Uses prepared statements

---

## 🚀 **Migration from File Storage**

Already using the file-based system? Easy migration:

```php
// Before (file storage)
$auth = new Auth();

// After (database)
$mysqli = new mysqli('localhost', 'user', 'pass', 'db');
$auth = new Auth();
$auth->setDatabaseConnection($mysqli)
     ->setAdminDefaults('your_admin', 'your_password', 'admin@site.com');

// Everything else stays the same!
$result = $auth->login($username, $password);
```

---

## 🔧 **Troubleshooting**

### Common Issues:

**"Database not configured" error:**
```php
// Make sure you call setDatabaseConnection()
$auth->setDatabaseConnection($mysqli, 'users');
```

**"Table doesn't exist" error:**
```sql
-- Create the users table (see SQL above)
-- Or use your existing table with field mapping
```

**Custom field names not working:**
```php
// Use field mapping for different column names
$fieldMapping = ['username' => 'your_username_field'];
$auth->setDatabaseConnection($db, 'table', $fieldMapping);
```

**Can't login with admin credentials:**
```php
// Check your admin defaults
$adminDefaults = $auth->getAdminDefaults();
var_dump($adminDefaults); // Debug your settings
```

---

## 🎉 **You're Ready!**

With this implementation, you get:
- ✅ **Your own database** - Full control over data storage
- ✅ **Easy admin setup** - Configure credentials in code
- ✅ **Flexible configuration** - Customize everything
- ✅ **Security built-in** - Enterprise-grade features
- ✅ **Simple integration** - Works with existing systems

Start with the basic example above and customize as needed!