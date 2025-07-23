<?php

namespace App;

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../config/database.php';

class Auth {
    private $db;
    private $table = 'users';

    public function __construct() {
        $database = new \Database();
        $this->db = $database->connect();
        $this->startSession();
        $this->initializeDefaultUser();
    }

    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function initializeDefaultUser() {
        // Check if admin user exists
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->table} WHERE username = ?");
        $username = 'admin';
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] == 0) {
            // Create default admin user
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} (username, password_hash, email, first_name, last_name, role, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt->bind_param("sssssss",
                $username,
                $passwordHash,
                $email = 'admin@nananom-farms.com',
                $firstName = 'Admin',
                $lastName = 'User',
                $role = 'admin',
                $status = 'active'
            );
            $stmt->execute();
        }
    }

    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = ? AND status = 'active'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['login_time'] = time();
            
            // Update last login
            $updateStmt = $this->db->prepare("UPDATE {$this->table} SET last_login = NOW(), login_attempts = 0 WHERE id = ?");
            $updateStmt->bind_param("i", $user['id']);
            $updateStmt->execute();
            
            return ['success' => true, 'message' => 'Login successful'];
        } else {
            // Increment login attempts if user exists
            if ($user) {
                $attempts = $user['login_attempts'] + 1;
                $updateStmt = $this->db->prepare("UPDATE {$this->table} SET login_attempts = ? WHERE id = ?");
                $updateStmt->bind_param("ii", $attempts, $user['id']);
                $updateStmt->execute();
            }
            
            return ['success' => false, 'message' => 'Invalid credentials'];
        }
    }

    public function logout() {
        session_destroy();
        return true;
    }

    public function isLoggedIn() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['login_time'])) {
            return false;
        }
        
        // Check session timeout (1 hour)
        if (time() - $_SESSION['login_time'] > 3600) {
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

    public function createUser($data) {
        // Check if username exists
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->table} WHERE username = ?");
        $stmt->bind_param("s", $data['username']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            return ['success' => false, 'message' => 'Username already exists'];
        }
        
        // Create user
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (username, password_hash, email, first_name, last_name, role, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $success = $stmt->bind_param("sssssss",
            $data['username'],
            $passwordHash,
            $data['email'],
            $data['first_name'],
            $data['last_name'],
            $data['role'] ?? 'support',
            'active'
        ) && $stmt->execute();
        
        return ['success' => $success, 'message' => $success ? 'User created successfully' : 'Failed to create user'];
    }

    public function changePassword($userId, $newPassword) {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE {$this->table} SET password_hash = ?, login_attempts = 0 WHERE id = ?");
        $success = $stmt->bind_param("si", $passwordHash, $userId) && $stmt->execute();
        
        return ['success' => $success, 'message' => $success ? 'Password changed successfully' : 'Failed to change password'];
    }

    public function getAllUsers() {
        $result = $this->db->query("SELECT id, username, email, first_name, last_name, role, status, last_login, created_at FROM {$this->table} ORDER BY username");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateUserStatus($id, $status) {
        $validStatuses = ['active', 'inactive'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ? WHERE id = ?");
        return $stmt->bind_param("si", $status, $id) && $stmt->execute();
    }

    public function deleteUser($id) {
        // Don't delete admin user
        $user = $this->getUserById($id);
        if ($user && $user['username'] === 'admin') {
            return false;
        }
        
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}