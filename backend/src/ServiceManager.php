<?php

namespace App;

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../config/storage.php';

class ServiceManager {
    private $storage;
    private $table = 'services';

    public function __construct() {
        $this->storage = new \FileStorage();
        $this->initializeDefaultServices();
    }

    private function initializeDefaultServices() {
        $services = $this->storage->query($this->table);
        if (empty($services)) {
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
                $this->storage->insert($this->table, $service);
            }
        }
    }

    public function createService($data) {
        $serviceData = [
            'name' => $this->storage->escape_string($data['name']),
            'description' => $this->storage->escape_string($data['description']),
            'price' => floatval($data['price']),
            'duration_minutes' => intval($data['duration_minutes']),
            'status' => 'active'
        ];
        
        return $this->storage->insert($this->table, $serviceData);
    }

    public function getAllServices($activeOnly = false) {
        $conditions = [];
        if ($activeOnly) {
            $conditions['status'] = 'active';
        }
        
        return $this->storage->select(
            $this->table, 
            $conditions, 
            ['field' => 'name', 'direction' => 'asc']
        );
    }

    public function getServiceById($id) {
        return $this->storage->selectOne($this->table, ['id' => $id]);
    }

    public function updateService($id, $data) {
        $updateData = [
            'name' => $this->storage->escape_string($data['name']),
            'description' => $this->storage->escape_string($data['description']),
            'price' => floatval($data['price']),
            'duration_minutes' => intval($data['duration_minutes'])
        ];
        
        return $this->storage->update($this->table, ['id' => $id], $updateData);
    }

    public function toggleServiceStatus($id) {
        $service = $this->getServiceById($id);
        if ($service) {
            $newStatus = $service['status'] === 'active' ? 'inactive' : 'active';
            return $this->storage->update($this->table, ['id' => $id], ['status' => $newStatus]);
        }
        return false;
    }

    public function deleteService($id) {
        return $this->storage->delete($this->table, ['id' => $id]);
    }

    public function getServiceStats() {
        $allServices = $this->storage->query($this->table);
        
        $stats = [
            'total_services' => count($allServices),
            'active_services' => 0,
            'inactive_services' => 0
        ];
        
        foreach ($allServices as $service) {
            if ($service['status'] === 'active') {
                $stats['active_services']++;
            } else {
                $stats['inactive_services']++;
            }
        }
        
        return $stats;
    }
}