<?php

namespace App;

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../config/storage.php';

class Auth {
    private $storage;
    private $table = 'users';
    private $configFile = 'auth_config';

    public function __construct() {
        $this->storage = new \FileStorage();
        $this->startSession();
        $this->initializeAuth();
    }

    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function initializeAuth() {
        // Check if custom auth config exists
        $authConfig = $this->storage->query($this->configFile);
        
        if (empty($authConfig)) {
            // Create default configuration that can be easily modified
            $defaultConfig = [
                'default_admin' => [
                    'username' => 'admin',
                    'password' => 'admin123', // This will be hashed
                    'email' => 'admin@nananom-farms.com',
                    'first_name' => 'Admin',
                    'last_name' => 'User'
                ],
                'password_requirements' => [
                    'min_length' => 6,
                    'require_uppercase' => false,
                    'require_numbers' => false,
                    'require_special_chars' => false
                ],
                'session_timeout' => 3600, // 1 hour
                'max_login_attempts' => 5
            ];
            
            $this->storage->insert($this->configFile, $defaultConfig);
            $authConfig = [$defaultConfig];
        }
        
        $this->createDefaultUsers($authConfig[0]);
    }

    private function createDefaultUsers($config) {
        $users = $this->storage->query($this->table);
        if (empty($users)) {
            $defaultAdmin = $config['default_admin'];
            
            $adminUser = [
                'username' => $defaultAdmin['username'],
                'password_hash' => password_hash($defaultAdmin['password'], PASSWORD_DEFAULT),
                'first_name' => $defaultAdmin['first_name'],
                'last_name' => $defaultAdmin['last_name'],
                'email' => $defaultAdmin['email'],
                'role' => 'admin',
                'status' => 'active',
                'login_attempts' => 0
            ];
            
            $this->storage->insert($this->table, $adminUser);
        }
    }

    public function login($username, $password) {
        $username = $this->storage->escape_string($username);
        $users = $this->storage->select($this->table, [
            'username' => $username,
            'status' => 'active'
        ]);

        if (!empty($users)) {
            $user = $users[0];
            
            // Check login attempts
            if ($user['login_attempts'] >= 5) {
                return ['success' => false, 'message' => 'Account locked due to too many failed attempts'];
            }
            
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['login_time'] = time();
                
                // Reset login attempts and update last login
                $this->storage->update($this->table, ['id' => $user['id']], [
                    'last_login' => date('Y-m-d H:i:s'),
                    'login_attempts' => 0
                ]);
                
                return ['success' => true, 'message' => 'Login successful'];
            } else {
                // Increment login attempts
                $attempts = $user['login_attempts'] + 1;
                $this->storage->update($this->table, ['id' => $user['id']], [
                    'login_attempts' => $attempts
                ]);
                
                return ['success' => false, 'message' => 'Invalid credentials'];
            }
        }
        
        return ['success' => false, 'message' => 'Invalid credentials'];
    }

    public function logout() {
        session_destroy();
        return true;
    }

    public function isLoggedIn() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['login_time'])) {
            return false;
        }
        
        // Check session timeout
        $authConfig = $this->storage->query($this->configFile);
        $timeout = $authConfig[0]['session_timeout'] ?? 3600;
        
        if (time() - $_SESSION['login_time'] > $timeout) {
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
                'email' => $_SESSION['email'] ?? ''
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

    // Admin functions for managing authentication
    public function updateAuthConfig($config) {
        $this->requireAdmin();
        
        $currentConfig = $this->storage->query($this->configFile);
        if (!empty($currentConfig)) {
            return $this->storage->update($this->configFile, ['id' => $currentConfig[0]['id']], $config);
        } else {
            return $this->storage->insert($this->configFile, $config);
        }
    }

    public function getAuthConfig() {
        $this->requireAdmin();
        $config = $this->storage->query($this->configFile);
        return !empty($config) ? $config[0] : null;
    }

    public function createUser($data) {
        // Validate password requirements
        $config = $this->storage->query($this->configFile);
        $requirements = $config[0]['password_requirements'] ?? [];
        
        if (!$this->validatePassword($data['password'], $requirements)) {
            return ['success' => false, 'message' => 'Password does not meet requirements'];
        }
        
        $userData = [
            'username' => $this->storage->escape_string($data['username']),
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'first_name' => $this->storage->escape_string($data['first_name']),
            'last_name' => $this->storage->escape_string($data['last_name']),
            'email' => $this->storage->escape_string($data['email']),
            'role' => $this->storage->escape_string($data['role'] ?? 'support'),
            'status' => 'active',
            'login_attempts' => 0
        ];
        
        // Check if username or email already exists
        $existingUsers = $this->storage->query($this->table);
        foreach ($existingUsers as $user) {
            if ($user['username'] === $userData['username']) {
                return ['success' => false, 'message' => 'Username already exists'];
            }
            if ($user['email'] === $userData['email']) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
        }
        
        $success = $this->storage->insert($this->table, $userData);
        return ['success' => $success, 'message' => $success ? 'User created successfully' : 'Failed to create user'];
    }

    private function validatePassword($password, $requirements) {
        if (strlen($password) < ($requirements['min_length'] ?? 6)) {
            return false;
        }
        
        if (($requirements['require_uppercase'] ?? false) && !preg_match('/[A-Z]/', $password)) {
            return false;
        }
        
        if (($requirements['require_numbers'] ?? false) && !preg_match('/[0-9]/', $password)) {
            return false;
        }
        
        if (($requirements['require_special_chars'] ?? false) && !preg_match('/[^A-Za-z0-9]/', $password)) {
            return false;
        }
        
        return true;
    }

    public function changePassword($userId, $newPassword) {
        $config = $this->storage->query($this->configFile);
        $requirements = $config[0]['password_requirements'] ?? [];
        
        if (!$this->validatePassword($newPassword, $requirements)) {
            return ['success' => false, 'message' => 'Password does not meet requirements'];
        }
        
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $success = $this->storage->update($this->table, ['id' => $userId], [
            'password_hash' => $passwordHash,
            'login_attempts' => 0 // Reset attempts on password change
        ]);
        
        return ['success' => $success, 'message' => $success ? 'Password changed successfully' : 'Failed to change password'];
    }

    public function resetLoginAttempts($userId) {
        $this->requireAdmin();
        return $this->storage->update($this->table, ['id' => $userId], ['login_attempts' => 0]);
    }

    public function getAllUsers() {
        return $this->storage->select($this->table, [], ['field' => 'username', 'direction' => 'asc']);
    }

    public function getUserById($id) {
        return $this->storage->selectOne($this->table, ['id' => $id]);
    }

    public function updateUserStatus($id, $status) {
        $validStatuses = ['active', 'inactive'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        return $this->storage->update($this->table, ['id' => $id], ['status' => $status]);
    }

    public function deleteUser($id) {
        // Don't delete the default admin user
        $user = $this->getUserById($id);
        if ($user && $user['username'] === 'admin') {
            return false;
        }
        
        return $this->storage->delete($this->table, ['id' => $id]);
    }
}