<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../backend/autoload.php';
require_once '../backend/src/ServiceManager.php';

use App\ServiceManager;

try {
    $serviceManager = new ServiceManager();
    $services = $serviceManager->getAllServices(true); // Only active services
    
    echo json_encode($services);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch services']);
}