<?php

namespace App;

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../config/database.php';

class ServiceManager {
    private $db;

    public function __construct() {
        $database = new \Database();
        $this->db = $database->connect();
    }

    public function createService($data) {
        $stmt = $this->db->prepare("
            INSERT INTO services (name, description, price, duration_minutes) 
            VALUES (?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['price'],
            $data['duration_minutes']
        ]);
    }

    public function getAllServices($activeOnly = false) {
        $sql = "SELECT * FROM services";
        if ($activeOnly) {
            $sql .= " WHERE status = 'active'";
        }
        $sql .= " ORDER BY name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getServiceById($id) {
        $stmt = $this->db->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateService($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE services 
            SET name = ?, description = ?, price = ?, duration_minutes = ? 
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['price'],
            $data['duration_minutes'],
            $id
        ]);
    }

    public function toggleServiceStatus($id) {
        $stmt = $this->db->prepare("
            UPDATE services 
            SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END 
            WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }

    public function deleteService($id) {
        $stmt = $this->db->prepare("DELETE FROM services WHERE id = ?");
        return $stmt->execute([$id]);
    }
}