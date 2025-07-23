<?php

namespace App;

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../config/database.php';

class ServiceManager {
    private $db;
    private $table = 'services';

    public function __construct() {
        $database = new \Database();
        $this->db = $database->connect();
    }

    public function createService($data) {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (name, description, price, duration_minutes, status) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['description'],
            floatval($data['price']),
            intval($data['duration_minutes']),
            'active'
        ]);
    }

    public function getAllServices($activeOnly = false) {
        $sql = "SELECT * FROM {$this->table}";
        if ($activeOnly) {
            $sql .= " WHERE status = 'active'";
        }
        $sql .= " ORDER BY name";
        
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getServiceById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateService($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET name = ?, description = ?, price = ?, duration_minutes = ? 
            WHERE id = ?
        ");
        
        return $stmt->bind_param("ssdii", 
            $data['name'],
            $data['description'],
            $data['price'],
            $data['duration_minutes'],
            $id
        ) && $stmt->execute();
    }

    public function toggleServiceStatus($id) {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function deleteService($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getServiceStats() {
        $result = $this->db->query("
            SELECT 
                COUNT(*) as total_services,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active_services,
                COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive_services
            FROM {$this->table}
        ");
        return $result->fetch_assoc();
    }
}