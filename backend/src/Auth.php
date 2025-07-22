<?php

namespace App;

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../config/storage.php';

class Auth {
    private $storage;
    private $table = 'users';

    public function __construct() {
        $this->storage = new \FileStorage();
        $this->startSession();
        $this->initializeDefaultUser();
    }

    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function initializeDefaultUser() {
        $users = $this->storage->query($this->table);
        if (empty($users)) {
            // Create default admin user
            $defaultUser = [
                'username' => 'admin',
                'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@nananom-farms.com',
                'role' => 'admin',
                'status' => 'active'
            ];
            
            $this->storage->insert($this->table, $defaultUser);
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
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['email'] = $user['email'];
                
                // Update last login time
                $this->storage->update($this->table, ['id' => $user['id']], [
                    'last_login' => date('Y-m-d H:i:s')
                ]);
                
                return true;
            }
        }
        return false;
    }

    public function logout() {
        session_destroy();
        return true;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
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

    public function createUser($data) {
        $userData = [
            'username' => $this->storage->escape_string($data['username']),
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'first_name' => $this->storage->escape_string($data['first_name']),
            'last_name' => $this->storage->escape_string($data['last_name']),
            'email' => $this->storage->escape_string($data['email']),
            'role' => $this->storage->escape_string($data['role'] ?? 'support'),
            'status' => 'active'
        ];
        
        // Check if username or email already exists
        $existingUsers = $this->storage->query($this->table);
        foreach ($existingUsers as $user) {
            if ($user['username'] === $userData['username'] || $user['email'] === $userData['email']) {
                return false; // User already exists
            }
        }
        
        return $this->storage->insert($this->table, $userData);
    }

    public function changePassword($userId, $newPassword) {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->storage->update($this->table, ['id' => $userId], [
            'password_hash' => $passwordHash
        ]);
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