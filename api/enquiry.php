<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../backend/autoload.php';
require_once '../backend/src/EnquiryManager.php';

use App\EnquiryManager;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Validate required fields
    $required_fields = ['name', 'email', 'subject', 'message'];
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
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }
    
    $enquiryManager = new EnquiryManager();
    
    $enquiry_data = [
        'name' => trim($_POST['name']),
        'email' => trim($_POST['email']),
        'phone' => trim($_POST['phone'] ?? ''),
        'subject' => trim($_POST['subject']),
        'message' => trim($_POST['message'])
    ];
    
    if ($enquiryManager->createEnquiry($enquiry_data)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Enquiry submitted successfully! We will respond to you within 24 hours.'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit enquiry. Please try again.']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error occurred. Please try again later.']);
}