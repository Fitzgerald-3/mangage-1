<?php

namespace App;

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../config/storage.php';

class BookingManager {
    private $storage;
    private $table = 'bookings';

    public function __construct() {
        $this->storage = new \FileStorage();
    }

    public function createBooking($data) {
        $bookingData = [
            'service_id' => intval($data['service_id']),
            'customer_name' => $this->storage->escape_string($data['customer_name']),
            'customer_email' => $this->storage->escape_string($data['customer_email']),
            'customer_phone' => $this->storage->escape_string($data['customer_phone']),
            'booking_date' => $this->storage->escape_string($data['booking_date']),
            'booking_time' => $this->storage->escape_string($data['booking_time']),
            'message' => $this->storage->escape_string($data['message'] ?? ''),
            'status' => 'pending'
        ];
        
        return $this->storage->insert($this->table, $bookingData);
    }

    public function getAllBookings($limit = null, $offset = 0) {
        // Get bookings with service information using join
        $bookings = $this->storage->join('bookings', 'services', 'service_id', 'id');
        
        // Sort by created_at descending
        usort($bookings, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        // Apply pagination if specified
        if ($limit) {
            return array_slice($bookings, $offset, $limit);
        }
        
        return $bookings;
    }

    public function getBookingById($id) {
        $booking = $this->storage->selectOne($this->table, ['id' => $id]);
        
        if ($booking) {
            // Get service information
            $serviceManager = new \App\ServiceManager();
            $service = $serviceManager->getServiceById($booking['service_id']);
            
            if ($service) {
                $booking['service_name'] = $service['name'];
                $booking['service_price'] = $service['price'];
            }
        }
        
        return $booking;
    }

    public function updateBookingStatus($id, $status) {
        $validStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        return $this->storage->update($this->table, ['id' => $id], ['status' => $status]);
    }

    public function deleteBooking($id) {
        return $this->storage->delete($this->table, ['id' => $id]);
    }

    public function getBookingStats() {
        $allBookings = $this->storage->query($this->table);
        
        $stats = [
            'total_bookings' => count($allBookings),
            'pending_bookings' => 0,
            'confirmed_bookings' => 0,
            'completed_bookings' => 0,
            'cancelled_bookings' => 0
        ];
        
        foreach ($allBookings as $booking) {
            switch ($booking['status']) {
                case 'pending':
                    $stats['pending_bookings']++;
                    break;
                case 'confirmed':
                    $stats['confirmed_bookings']++;
                    break;
                case 'completed':
                    $stats['completed_bookings']++;
                    break;
                case 'cancelled':
                    $stats['cancelled_bookings']++;
                    break;
            }
        }
        
        return $stats;
    }

    public function getAvailableSlots($date) {
        // Get booked time slots for the given date
        $bookedBookings = $this->storage->select($this->table, ['booking_date' => $date]);
        $bookedSlots = array_column($bookedBookings, 'booking_time');
        
        // Define available time slots
        $timeSlots = [
            '09:00', '10:00', '11:00', '12:00', 
            '14:00', '15:00', '16:00', '17:00'
        ];
        
        // Return available slots (excluding booked ones)
        return array_diff($timeSlots, $bookedSlots);
    }

    public function getBookingsByDate($date) {
        return $this->storage->select($this->table, ['booking_date' => $date]);
    }

    public function getBookingsByStatus($status) {
        return $this->storage->select($this->table, ['status' => $status]);
    }

    public function getBookingsByCustomer($customerEmail) {
        return $this->storage->select($this->table, ['customer_email' => $customerEmail]);
    }

    public function getRecentBookings($limit = 5) {
        $bookings = $this->getAllBookings($limit);
        return $bookings;
    }

    public function searchBookings($searchTerm) {
        $allBookings = $this->storage->query($this->table);
        $searchTerm = strtolower($searchTerm);
        
        return array_filter($allBookings, function($booking) use ($searchTerm) {
            return strpos(strtolower($booking['customer_name']), $searchTerm) !== false ||
                   strpos(strtolower($booking['customer_email']), $searchTerm) !== false ||
                   strpos(strtolower($booking['customer_phone']), $searchTerm) !== false;
        });
    }
}