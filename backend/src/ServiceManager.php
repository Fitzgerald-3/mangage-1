<?php

namespace App;

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../config/storage.php';

class ServiceManager {
    private $storage;
    private $filename = 'services';

    public function __construct() {
        $this->storage = new \FileStorage();
        $this->initializeDefaultServices();
    }

    private function initializeDefaultServices() {
        $services = $this->storage->read($this->filename);
        if (empty($services)) {
            $defaultServices = [
                [
                    'id' => 1,
                    'name' => 'Palm Oil Consultation',
                    'description' => 'Expert consultation on palm oil production and marketing strategies',
                    'price' => 150.00,
                    'duration_minutes' => 60,
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 2,
                    'name' => 'Quality Testing',
                    'description' => 'Comprehensive quality testing of palm oil products',
                    'price' => 200.00,
                    'duration_minutes' => 90,
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 3,
                    'name' => 'Distribution Planning',
                    'description' => 'Strategic planning for palm oil distribution networks',
                    'price' => 300.00,
                    'duration_minutes' => 120,
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'id' => 4,
                    'name' => 'Market Analysis',
                    'description' => 'Detailed market analysis and pricing strategies',
                    'price' => 250.00,
                    'duration_minutes' => 90,
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
            $this->storage->write($this->filename, $defaultServices);
        }
    }

    public function createService($data) {
        $services = $this->storage->read($this->filename);
        $newService = [
            'id' => $this->storage->generateId($this->filename),
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => floatval($data['price']),
            'duration_minutes' => intval($data['duration_minutes']),
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $services[] = $newService;
        return $this->storage->write($this->filename, $services);
    }

    public function getAllServices($activeOnly = false) {
        $services = $this->storage->read($this->filename);
        
        if ($activeOnly) {
            $services = array_filter($services, function($service) {
                return $service['status'] === 'active';
            });
        }
        
        // Sort by name
        usort($services, function($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });
        
        return $services;
    }

    public function getServiceById($id) {
        $services = $this->storage->read($this->filename);
        foreach ($services as $service) {
            if ($service['id'] == $id) {
                return $service;
            }
        }
        return null;
    }

    public function updateService($id, $data) {
        $services = $this->storage->read($this->filename);
        
        foreach ($services as &$service) {
            if ($service['id'] == $id) {
                $service['name'] = $data['name'];
                $service['description'] = $data['description'];
                $service['price'] = floatval($data['price']);
                $service['duration_minutes'] = intval($data['duration_minutes']);
                $service['updated_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
        
        return $this->storage->write($this->filename, $services);
    }

    public function toggleServiceStatus($id) {
        $services = $this->storage->read($this->filename);
        
        foreach ($services as &$service) {
            if ($service['id'] == $id) {
                $service['status'] = $service['status'] === 'active' ? 'inactive' : 'active';
                $service['updated_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
        
        return $this->storage->write($this->filename, $services);
    }

    public function deleteService($id) {
        $services = $this->storage->read($this->filename);
        
        $services = array_filter($services, function($service) use ($id) {
            return $service['id'] != $id;
        });
        
        // Re-index array
        $services = array_values($services);
        
        return $this->storage->write($this->filename, $services);
    }
}