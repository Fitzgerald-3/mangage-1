<?php

namespace App;

/**
 * Database-based Service Manager
 * Replaces the file storage version completely
 */

class DatabaseServiceManager {
    private $db;
    private $table = 'services';

    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
        $this->initializeDefaultServices();
    }

    // Initialize with default services (same as file version)
    private function initializeDefaultServices() {
        $count = $this->getServiceCount();
        
        if ($count == 0) {
            $defaultServices = [
                [
                    'name' => 'Palm Oil Consultation',
                    'description' => 'Expert consultation on palm oil production and marketing strategies',
                    'price' => 150.00,
                    'duration_minutes' => 60,
                    'status' => 'active'
                ],
                [
                    'name' => 'Quality Testing',
                    'description' => 'Comprehensive quality testing of palm oil products',
                    'price' => 200.00,
                    'duration_minutes' => 90,
                    'status' => 'active'
                ],
                [
                    'name' => 'Distribution Planning',
                    'description' => 'Strategic planning for palm oil distribution networks',
                    'price' => 300.00,
                    'duration_minutes' => 120,
                    'status' => 'active'
                ],
                [
                    'name' => 'Market Analysis',
                    'description' => 'Detailed market analysis and pricing strategies',
                    'price' => 250.00,
                    'duration_minutes' => 90,
                    'status' => 'active'
                ]
            ];
            
            foreach ($defaultServices as $service) {
                $this->createService($service);
            }
        }
    }

    // CRUD Operations using your database

    public function createService($data) {
        if ($this->db instanceof mysqli) {
            return $this->createServiceMysqli($data);
        } elseif ($this->db instanceof PDO) {
            return $this->createServicePDO($data);
        }
        
        throw new Exception('Unsupported database type');
    }

    private function createServiceMysqli($data) {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (name, description, price, duration_minutes, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['price'],
            $data['duration_minutes'],
            $data['status'] ?? 'active'
        ]);
    }

    private function createServicePDO($data) {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (name, description, price, duration_minutes, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['price'],
            $data['duration_minutes'],
            $data['status'] ?? 'active'
        ]);
    }

    public function getAllServices($activeOnly = false) {
        $sql = "SELECT * FROM {$this->table}";
        if ($activeOnly) {
            $sql .= " WHERE status = 'active'";
        }
        $sql .= " ORDER BY name";
        
        if ($this->db instanceof mysqli) {
            $result = $this->db->query($sql);
            return $result->fetch_all(MYSQLI_ASSOC);
        } elseif ($this->db instanceof PDO) {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public function getServiceById($id) {
        if ($this->db instanceof mysqli) {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } elseif ($this->db instanceof PDO) {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    public function updateService($id, $data) {
        if ($this->db instanceof mysqli) {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET name = ?, description = ?, price = ?, duration_minutes = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['name'],
                $data['description'],
                $data['price'],
                $data['duration_minutes'],
                $id
            ]);
        } elseif ($this->db instanceof PDO) {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET name = ?, description = ?, price = ?, duration_minutes = ?, updated_at = NOW() 
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
    }

    public function toggleServiceStatus($id) {
        if ($this->db instanceof mysqli) {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END,
                    updated_at = NOW()
                WHERE id = ?
            ");
            return $stmt->execute([$id]);
        } elseif ($this->db instanceof PDO) {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END,
                    updated_at = NOW()
                WHERE id = ?
            ");
            return $stmt->execute([$id]);
        }
    }

    public function deleteService($id) {
        if ($this->db instanceof mysqli) {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            return $stmt->execute([$id]);
        } elseif ($this->db instanceof PDO) {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            return $stmt->execute([$id]);
        }
    }

    public function getServiceStats() {
        if ($this->db instanceof mysqli) {
            $result = $this->db->query("
                SELECT 
                    COUNT(*) as total_services,
                    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_services,
                    COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive_services
                FROM {$this->table}
            ");
            return $result->fetch_assoc();
        } elseif ($this->db instanceof PDO) {
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total_services,
                    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_services,
                    COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive_services
                FROM {$this->table}
            ");
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    private function getServiceCount() {
        if ($this->db instanceof mysqli) {
            $result = $this->db->query("SELECT COUNT(*) as count FROM {$this->table}");
            $row = $result->fetch_assoc();
            return $row['count'];
        } elseif ($this->db instanceof PDO) {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM {$this->table}");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['count'];
        }
    }
}