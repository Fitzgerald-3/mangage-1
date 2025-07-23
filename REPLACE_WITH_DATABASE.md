# 🗄️ Complete Database Replacement Guide

**Yes, you can completely delete the file storage and replace it with your own database!** Here's exactly how to do it.

## 🎯 **Two Approaches Available:**

### **Option 1: Quick Switch (Use Auth_Database.php)**
- Keep existing managers, just switch Auth to database
- **5 minutes setup**
- Recommended for most users

### **Option 2: Complete Replacement** 
- Replace all file storage with database
- **Full database control**
- For advanced users

---

## ⚡ **Option 1: Quick Database Switch (Recommended)**

### **Step 1: Create Database Tables**

```sql
-- Users table (required)
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

-- Optional: Services table (or keep file storage for services)
CREATE TABLE services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration_minutes INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Optional: Bookings table
CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    message TEXT,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id)
);
```

### **Step 2: Update Your Auth Usage**

Replace in your files where Auth is used:

```php
// OLD (file-based)
require_once 'backend/src/Auth.php';

// NEW (database-based)
require_once 'backend/src/Auth_Database.php';

// Your database connection
$mysqli = new mysqli('localhost', 'username', 'password', 'database');

// Initialize with database
$auth = new Auth();
$auth->setDatabaseConnection($mysqli, 'users')
     ->setAdminDefaults('your_admin', 'your_password', 'admin@yoursite.com');

// Everything else stays exactly the same!
$result = $auth->login($username, $password);
```

### **Step 3: Delete File Storage (Optional)**

```bash
# Remove the file storage files
rm -rf data/
rm backend/config/storage.php
```

**Done!** Your system now uses your database for authentication while keeping other features.

---

## 🔄 **Option 2: Complete Database Replacement**

Replace ALL managers with database versions:

### **Step 1: Create All Database Tables**

```sql
-- Complete schema
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

CREATE TABLE services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration_minutes INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    message TEXT,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id)
);

CREATE TABLE enquiries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'in_progress', 'resolved', 'closed') DEFAULT 'new',
    assigned_to INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'responded', 'archived') DEFAULT 'new',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### **Step 2: Replace Manager Files**

Create database versions of your managers. Example provided in `examples/DatabaseServiceManager.php`.

### **Step 3: Update All File References**

```php
// Replace in all your PHP files:

// OLD
require_once 'backend/src/ServiceManager.php';
$serviceManager = new App\ServiceManager();

// NEW  
require_once 'examples/DatabaseServiceManager.php';
$serviceManager = new App\DatabaseServiceManager($mysqli);
```

---

## 🔗 **Migration Script**

Want to migrate existing file data to database? Here's a script:

```php
<?php
// migrate_to_database.php

require_once 'backend/config/storage.php';

// Your database connection
$mysqli = new mysqli('localhost', 'username', 'password', 'database');

// Initialize file storage
$fileStorage = new FileStorage();

// Migrate services
$services = $fileStorage->query('services');
foreach ($services as $service) {
    $stmt = $mysqli->prepare("
        INSERT INTO services (name, description, price, duration_minutes, status, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $service['name'],
        $service['description'],
        $service['price'],
        $service['duration_minutes'],
        $service['status'],
        $service['created_at'],
        $service['updated_at']
    ]);
}

// Migrate users
$users = $fileStorage->query('users');
foreach ($users as $user) {
    $stmt = $mysqli->prepare("
        INSERT INTO users (username, password_hash, email, first_name, last_name, role, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $user['username'],
        $user['password_hash'],
        $user['email'],
        $user['first_name'],
        $user['last_name'],
        $user['role'],
        $user['status'],
        $user['created_at']
    ]);
}

// Migrate bookings (if you have booking data)
$bookings = $fileStorage->query('bookings');
foreach ($bookings as $booking) {
    $stmt = $mysqli->prepare("
        INSERT INTO bookings (service_id, customer_name, customer_email, customer_phone, booking_date, booking_time, message, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $booking['service_id'],
        $booking['customer_name'],
        $booking['customer_email'],
        $booking['customer_phone'],
        $booking['booking_date'],
        $booking['booking_time'],
        $booking['message'] ?? '',
        $booking['status'],
        $booking['created_at']
    ]);
}

echo "Migration completed successfully!";
?>
```

---

## 🗑️ **What to Delete After Migration**

Once you've switched to database:

```bash
# Delete file storage components
rm -rf data/                           # All JSON data files
rm backend/config/storage.php          # File storage engine
rm setup.php                          # File-based setup (optional)

# Delete file-based managers (if using database versions)
rm backend/src/ServiceManager.php      # Replace with DatabaseServiceManager
rm backend/src/BookingManager.php      # Replace with DatabaseBookingManager  
rm backend/src/EnquiryManager.php      # Replace with DatabaseEnquiryManager
```

---

## ✅ **Benefits of Database Replacement**

### **Performance:**
- ✅ **Faster queries** with proper indexing
- ✅ **Better concurrency** for multiple users
- ✅ **Efficient joins** and complex queries

### **Scalability:**
- ✅ **Handle larger datasets** 
- ✅ **Better memory management**
- ✅ **Professional database features**

### **Features:**
- ✅ **Transactions** for data integrity
- ✅ **Foreign key constraints**
- ✅ **Advanced querying** capabilities
- ✅ **Database-level security**

---

## 🚀 **Quick Start Example**

Here's a complete working example:

```php
<?php
// config/database.php - Your new config file

$mysqli = new mysqli('localhost', 'your_user', 'your_password', 'your_database');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

return $mysqli;
?>
```

```php
<?php
// Updated login.php

$mysqli = require_once 'config/database.php';
require_once 'backend/src/Auth_Database.php';

$auth = new App\Auth();
$auth->setDatabaseConnection($mysqli, 'users')
     ->setAdminDefaults('your_admin', 'your_password', 'admin@yoursite.com');

// Same login logic as before
if ($_POST) {
    $result = $auth->login($_POST['username'], $_POST['password']);
    if ($result['success']) {
        header('Location: dashboard.php');
    }
}
?>
```

---

## 🎉 **You're Done!**

**Choose your approach:**
- **Quick Switch**: Just replace Auth, keep everything else (5 minutes)
- **Complete Replacement**: Full database system (30 minutes)

Both approaches give you **complete control** over your data while maintaining all the functionality of the original system! 🚀

The file storage was just a **stepping stone** - now you can use the full power of your own database!