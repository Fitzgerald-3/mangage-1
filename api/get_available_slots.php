<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../backend/autoload.php';
require_once '../backend/src/BookingManager.php';

use App\BookingManager;

if (!isset($_GET['date'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Date parameter is required']);
    exit;
}

try {
    $date = $_GET['date'];
    
    // Validate date format
    if (!DateTime::createFromFormat('Y-m-d', $date)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid date format. Use YYYY-MM-DD']);
        exit;
    }
    
    // Check if date is in the past
    if (strtotime($date) < strtotime('today')) {
        echo json_encode([]);
        exit;
    }
    
    // Check if it's a weekend
    $dayOfWeek = date('N', strtotime($date));
    if ($dayOfWeek >= 6) { // 6 = Saturday, 7 = Sunday
        echo json_encode([]);
        exit;
    }
    
    $bookingManager = new BookingManager();
    $availableSlots = $bookingManager->getAvailableSlots($date);
    
    echo json_encode(array_values($availableSlots));
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch available slots']);
}