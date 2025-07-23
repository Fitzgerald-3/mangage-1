<?php

namespace App;

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../config/database.php';

class BookingManager {
    private $db;
    private $table = 'bookings';

    public function __construct() {
        $database = new \Database();
        $this->db = $database->connect();
    }

    public function createBooking($data) {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (service_id, customer_name, customer_email, customer_phone, 
                                      booking_date, booking_time, message, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->bind_param("isssssss",
            $data['service_id'],
            $data['customer_name'],
            $data['customer_email'],
            $data['customer_phone'],
            $data['booking_date'],
            $data['booking_time'],
            $data['message'] ?? '',
            'pending'
        ) && $stmt->execute();
    }

    public function getAllBookings($limit = null, $offset = 0) {
        $sql = "
            SELECT b.*, s.name as service_name, s.price as service_price 
            FROM {$this->table} b 
            JOIN services s ON b.service_id = s.id 
            ORDER BY b.created_at DESC
        ";
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getBookingById($id) {
        $stmt = $this->db->prepare("
            SELECT b.*, s.name as service_name, s.price as service_price 
            FROM {$this->table} b 
            JOIN services s ON b.service_id = s.id 
            WHERE b.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateBookingStatus($id, $status) {
        $validStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ? WHERE id = ?");
        return $stmt->bind_param("si", $status, $id) && $stmt->execute();
    }

    public function deleteBooking($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getBookingStats() {
        $result = $this->db->query("
            SELECT 
                COUNT(*) as total_bookings,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_bookings,
                COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed_bookings,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_bookings,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_bookings
            FROM {$this->table}
        ");
        return $result->fetch_assoc();
    }

    public function getAvailableSlots($date) {
        $stmt = $this->db->prepare("SELECT booking_time FROM {$this->table} WHERE booking_date = ?");
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $bookedSlots = [];
        while ($row = $result->fetch_assoc()) {
            $bookedSlots[] = $row['booking_time'];
        }
        
        $timeSlots = [
            '09:00', '10:00', '11:00', '12:00', 
            '14:00', '15:00', '16:00', '17:00'
        ];
        
        return array_diff($timeSlots, $bookedSlots);
    }

    public function getBookingsByDate($date) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE booking_date = ?");
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getBookingsByStatus($status) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE status = ?");
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function searchBookings($searchTerm) {
        $searchPattern = "%$searchTerm%";
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE customer_name LIKE ? 
               OR customer_email LIKE ? 
               OR customer_phone LIKE ?
        ");
        $stmt->bind_param("sss", $searchPattern, $searchPattern, $searchPattern);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}