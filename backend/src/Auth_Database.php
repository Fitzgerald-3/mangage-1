<?php

namespace App;

require_once __DIR__ . '/../autoload.php';

/**
 * Flexible Authentication Class
 * 
 * This class allows you to:
 * 1. Use your own database implementation (mysqli, PDO, etc.)
 * 2. Configure admin defaults easily
 * 3. Maintain security features
 * 4. Switch between storage methods
 */

class Auth {
    private $storage;
    private $useDatabase;
    private $dbConnection;
    private $config;
    
    // Configuration - Easily modify these defaults
    private $defaultConfig = [
        'admin_defaults' => [
            'username' => 'admin',
            'password' => 'admin123',
            'email' => 'admin@nananom-farms.com',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'role' => 'admin'
        ],
        'password_requirements' => [
            'min_length' => 6,
            'require_uppercase' => false,
            'require_numbers' => false,
            'require_special_chars' => false
        ],
        'security' => [
            'session_timeout' => 3600, // 1 hour
            'max_login_attempts' => 5,
            'lock_duration' => 900, // 15 minutes
            'remember_me_duration' => 2592000 // 30 days
        ],
        'database' => [
            'use_database' => false, // Set to true to use database
            'table_name' => 'users',
            'fields' => [
                'id' => 'id',
                'username' => 'username',
                'password' => 'password_hash',
                'email' => 'email',
                'first_name' => 'first_name',
                'last_name' => 'last_name',
                'role' => 'role',
                'status' => 'status',
                'login_attempts' => 'login_attempts',
                'locked_until' => 'locked_until',
                'last_login' => 'last_login',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at'
            ]
        ]
    ];

    public function __construct($dbConnection = null, $config = []) {
        $this->config = array_merge_recursive($this->defaultConfig, $config);
        $this->useDatabase = $this->config['database']['use_database'];
        $this->dbConnection = $dbConnection;
        
        $this->startSession();
        $this->initializeAuth();
    }

    /**
     * EASY CONFIGURATION METHODS
     * Modify these to customize your authentication
     */
    
    public function setAdminDefaults($username, $password, $email, $firstName = 'Admin', $lastName = 'User') {
        $this->config['admin_defaults'] = [
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'role' => 'admin'
        ];
        return $this;
    }
    
    public function setPasswordRequirements($minLength = 6, $requireUpper = false, $requireNumbers = false, $requireSpecial = false) {
        $this->config['password_requirements'] = [
            'min_length' => $minLength,
            'require_uppercase' => $requireUpper,
            'require_numbers' => $requireNumbers,
            'require_special_chars' => $requireSpecial
        ];
        return $this;
    }
    
    public function setSecuritySettings($sessionTimeout = 3600, $maxAttempts = 5, $lockDuration = 900) {
        $this->config['security'] = [
            'session_timeout' => $sessionTimeout,
            'max_login_attempts' => $maxAttempts,
            'lock_duration' => $lockDuration,
            'remember_me_duration' => $this->config['security']['remember_me_duration']
        ];
        return $this;
    }

    /**
     * DATABASE IMPLEMENTATION METHODS
     * Override these methods to use your own database
     */
    
    public function setDatabaseConnection($connection, $tableName = 'users', $fieldMapping = []) {
        $this->dbConnection = $connection;
        $this->useDatabase = true;
        $this->config['database']['use_database'] = true;
        $this->config['database']['table_name'] = $tableName;
        
        if (!empty($fieldMapping)) {
            $this->config['database']['fields'] = array_merge($this->config['database']['fields'], $fieldMapping);
        }
        
        return $this;
    }
    
    // Override this method for your database implementation
    protected function dbExecuteQuery($query, $params = []) {
        if (!$this->useDatabase || !$this->dbConnection) {
            throw new Exception('Database not configured');
        }
        
        // MYSQLI IMPLEMENTATION EXAMPLE
        if ($this->dbConnection instanceof mysqli) {
            return $this->executeWithMysqli($query, $params);
        }
        
        // PDO IMPLEMENTATION EXAMPLE  
        if ($this->dbConnection instanceof PDO) {
            return $this->executeWithPDO($query, $params);
        }
        
        // CUSTOM DATABASE IMPLEMENTATION
        // Override this method to implement your own database logic
        throw new Exception('Database type not supported. Override dbExecuteQuery method.');
    }
    
    // MySQLi implementation example
    private function executeWithMysqli($query, $params = []) {
        $stmt = $this->dbConnection->prepare($query);
        
        if (!empty($params)) {
            $types = str_repeat('s', count($params)); // Assuming all strings, adjust as needed
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result === false) {
            return $stmt->affected_rows > 0;
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // PDO implementation example
    private function executeWithPDO($query, $params = []) {
        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute($params);
        
        if (strpos(strtoupper($query), 'SELECT') === 0) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $stmt->rowCount() > 0;
    }

    /**
     * CORE AUTHENTICATION METHODS
     */
    
    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    private function initializeAuth() {
        if (!$this->useDatabase) {
            // Use file storage for configuration
            require_once __DIR__ . '/../config/storage.php';
            $this->storage = new \FileStorage();
        }
        
        $this->createDefaultAdmin();
    }
    
    private function createDefaultAdmin() {
        $adminExists = $this->getUserByUsername($this->config['admin_defaults']['username']);
        
        if (!$adminExists) {
            $adminData = [
                $this->config['database']['fields']['username'] => $this->config['admin_defaults']['username'],
                $this->config['database']['fields']['password'] => password_hash($this->config['admin_defaults']['password'], PASSWORD_DEFAULT),
                $this->config['database']['fields']['email'] => $this->config['admin_defaults']['email'],
                $this->config['database']['fields']['first_name'] => $this->config['admin_defaults']['first_name'],
                $this->config['database']['fields']['last_name'] => $this->config['admin_defaults']['last_name'],
                $this->config['database']['fields']['role'] => $this->config['admin_defaults']['role'],
                $this->config['database']['fields']['status'] => 'active',
                $this->config['database']['fields']['login_attempts'] => 0,
                $this->config['database']['fields']['created_at'] => date('Y-m-d H:i:s')
            ];
            
            $this->createUser($adminData, true);
        }
    }
    
    public function login($username, $password, $rememberMe = false) {
        try {
            $user = $this->getUserByUsername($username);
            
            if (!$user) {
                return ['success' => false, 'message' => 'Invalid credentials'];
            }
            
            // Check if account is locked
            if ($this->isAccountLocked($user)) {
                return ['success' => false, 'message' => 'Account temporarily locked due to too many failed attempts'];
            }
            
            // Check if account is active
            if ($user[$this->config['database']['fields']['status']] !== 'active') {
                return ['success' => false, 'message' => 'Account is inactive'];
            }
            
            // Verify password
            if (!password_verify($password, $user[$this->config['database']['fields']['password']])) {
                $this->incrementLoginAttempts($user[$this->config['database']['fields']['id']]);
                return ['success' => false, 'message' => 'Invalid credentials'];
            }
            
            // Successful login
            $this->createUserSession($user);
            $this->resetLoginAttempts($user[$this->config['database']['fields']['id']]);
            $this->updateLastLogin($user[$this->config['database']['fields']['id']]);
            
            if ($rememberMe) {
                $this->setRememberMeCookie($user[$this->config['database']['fields']['id']]);
            }
            
            return ['success' => true, 'message' => 'Login successful'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Login error occurred'];
        }
    }
    
    private function getUserByUsername($username) {
        if ($this->useDatabase) {
            $table = $this->config['database']['table_name'];
            $usernameField = $this->config['database']['fields']['username'];
            $query = "SELECT * FROM {$table} WHERE {$usernameField} = ? AND {$this->config['database']['fields']['status']} = 'active' LIMIT 1";
            
            $result = $this->dbExecuteQuery($query, [$username]);
            return !empty($result) ? $result[0] : null;
        } else {
            // File storage fallback
            $users = $this->storage->select('users', ['username' => $username, 'status' => 'active']);
            return !empty($users) ? $users[0] : null;
        }
    }
    
    private function isAccountLocked($user) {
        $attempts = $user[$this->config['database']['fields']['login_attempts']] ?? 0;
        $lockedUntil = $user[$this->config['database']['fields']['locked_until']] ?? null;
        
        if ($attempts >= $this->config['security']['max_login_attempts']) {
            if ($lockedUntil && strtotime($lockedUntil) > time()) {
                return true;
            }
        }
        
        return false;
    }
    
    private function incrementLoginAttempts($userId) {
        $attempts = $this->getUserById($userId)[$this->config['database']['fields']['login_attempts']] + 1;
        $lockedUntil = null;
        
        if ($attempts >= $this->config['security']['max_login_attempts']) {
            $lockedUntil = date('Y-m-d H:i:s', time() + $this->config['security']['lock_duration']);
        }
        
        $this->updateUser($userId, [
            $this->config['database']['fields']['login_attempts'] => $attempts,
            $this->config['database']['fields']['locked_until'] => $lockedUntil
        ]);
    }
    
    private function resetLoginAttempts($userId) {
        $this->updateUser($userId, [
            $this->config['database']['fields']['login_attempts'] => 0,
            $this->config['database']['fields']['locked_until'] => null
        ]);
    }
    
    private function updateLastLogin($userId) {
        $this->updateUser($userId, [
            $this->config['database']['fields']['last_login'] => date('Y-m-d H:i:s')
        ]);
    }
    
    private function createUserSession($user) {
        $_SESSION['user_id'] = $user[$this->config['database']['fields']['id']];
        $_SESSION['username'] = $user[$this->config['database']['fields']['username']];
        $_SESSION['role'] = $user[$this->config['database']['fields']['role']];
        $_SESSION['full_name'] = $user[$this->config['database']['fields']['first_name']] . ' ' . $user[$this->config['database']['fields']['last_name']];
        $_SESSION['email'] = $user[$this->config['database']['fields']['email']];
        $_SESSION['login_time'] = time();
    }
    
    public function logout() {
        $this->clearRememberMeCookie();
        session_destroy();
        return true;
    }
    
    public function isLoggedIn() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['login_time'])) {
            return $this->checkRememberMe();
        }
        
        // Check session timeout
        if (time() - $_SESSION['login_time'] > $this->config['security']['session_timeout']) {
            $this->logout();
            return false;
        }
        
        return true;
    }
    
    public function getUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role'],
                'full_name' => $_SESSION['full_name'],
                'email' => $_SESSION['email']
            ];
        }
        return null;
    }
    
    public function hasRole($role) {
        return $this->isLoggedIn() && $_SESSION['role'] === $role;
    }
    
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: /admin/login.php');
            exit;
        }
    }
    
    public function requireAdmin() {
        $this->requireAuth();
        if (!$this->hasRole('admin')) {
            header('HTTP/1.1 403 Forbidden');
            exit('Access denied');
        }
    }
    
    /**
     * USER MANAGEMENT METHODS
     */
    
    public function createUser($userData, $isSystem = false) {
        // Validate password if not system user
        if (!$isSystem && !$this->validatePassword($userData[$this->config['database']['fields']['password']])) {
            return ['success' => false, 'message' => 'Password does not meet requirements'];
        }
        
        // Check if username exists
        if ($this->getUserByUsername($userData[$this->config['database']['fields']['username']])) {
            return ['success' => false, 'message' => 'Username already exists'];
        }
        
        try {
            if ($this->useDatabase) {
                $table = $this->config['database']['table_name'];
                $fields = implode(', ', array_keys($userData));
                $placeholders = implode(', ', array_fill(0, count($userData), '?'));
                $query = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";
                
                $success = $this->dbExecuteQuery($query, array_values($userData));
            } else {
                // File storage fallback
                $success = $this->storage->insert('users', $userData);
            }
            
            return ['success' => $success, 'message' => $success ? 'User created successfully' : 'Failed to create user'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error creating user'];
        }
    }
    
    public function getUserById($id) {
        if ($this->useDatabase) {
            $table = $this->config['database']['table_name'];
            $idField = $this->config['database']['fields']['id'];
            $query = "SELECT * FROM {$table} WHERE {$idField} = ? LIMIT 1";
            
            $result = $this->dbExecuteQuery($query, [$id]);
            return !empty($result) ? $result[0] : null;
        } else {
            return $this->storage->selectOne('users', ['id' => $id]);
        }
    }
    
    public function updateUser($id, $data) {
        if ($this->useDatabase) {
            $table = $this->config['database']['table_name'];
            $idField = $this->config['database']['fields']['id'];
            
            $setPairs = [];
            $values = [];
            
            foreach ($data as $field => $value) {
                $setPairs[] = "{$field} = ?";
                $values[] = $value;
            }
            
            $values[] = $id;
            $setClause = implode(', ', $setPairs);
            $query = "UPDATE {$table} SET {$setClause} WHERE {$idField} = ?";
            
            return $this->dbExecuteQuery($query, $values);
        } else {
            return $this->storage->update('users', ['id' => $id], $data);
        }
    }
    
    /**
     * UTILITY METHODS
     */
    
    private function validatePassword($password) {
        $req = $this->config['password_requirements'];
        
        if (strlen($password) < $req['min_length']) return false;
        if ($req['require_uppercase'] && !preg_match('/[A-Z]/', $password)) return false;
        if ($req['require_numbers'] && !preg_match('/[0-9]/', $password)) return false;
        if ($req['require_special_chars'] && !preg_match('/[^A-Za-z0-9]/', $password)) return false;
        
        return true;
    }
    
    private function setRememberMeCookie($userId) {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + $this->config['security']['remember_me_duration'];
        
        setcookie('remember_token', $token, $expiry, '/', '', true, true);
        
        // Store token in database/file (you should implement this)
        // $this->storeRememberToken($userId, $token, $expiry);
    }
    
    private function clearRememberMeCookie() {
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    }
    
    private function checkRememberMe() {
        if (!isset($_COOKIE['remember_token'])) {
            return false;
        }
        
        // Implement remember me token validation
        // $user = $this->validateRememberToken($_COOKIE['remember_token']);
        // if ($user) {
        //     $this->createUserSession($user);
        //     return true;
        // }
        
        return false;
    }
    
    /**
     * CONFIGURATION GETTERS
     */
    
    public function getConfig() {
        return $this->config;
    }
    
    public function getAdminDefaults() {
        return $this->config['admin_defaults'];
    }
    
    public function getPasswordRequirements() {
        return $this->config['password_requirements'];
    }
    
    public function getSecuritySettings() {
        return $this->config['security'];
    }
}