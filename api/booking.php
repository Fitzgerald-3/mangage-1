<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../backend/autoload.php';
require_once '../backend/src/BookingManager.php';

use App\BookingManager;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Validate required fields
    $required_fields = ['service_id', 'customer_name', 'customer_email', 'customer_phone', 'booking_date', 'booking_time'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Missing required fields: ' . implode(', ', $missing_fields)
        ]);
        exit;
    }
    
    // Validate email
    if (!filter_var($_POST['customer_email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }
    
    // Validate date (must be in the future)
    $booking_date = $_POST['booking_date'];
    if (strtotime($booking_date) < strtotime('today')) {
        echo json_encode(['success' => false, 'message' => 'Booking date must be in the future']);
        exit;
    }
    
    $bookingManager = new BookingManager();
    
    $booking_data = [
        'service_id' => intval($_POST['service_id']),
        'customer_name' => trim($_POST['customer_name']),
        'customer_email' => trim($_POST['customer_email']),
        'customer_phone' => trim($_POST['customer_phone']),
        'booking_date' => $booking_date,
        'booking_time' => $_POST['booking_time'],
        'message' => trim($_POST['message'] ?? '')
    ];
    
    if ($bookingManager->createBooking($booking_data)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Booking submitted successfully! We will contact you shortly to confirm your appointment.'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit booking. Please try again.']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error occurred. Please try again later.']);
}