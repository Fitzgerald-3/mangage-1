<?php

namespace App;

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../config/database.php';

class BookingManager {
    private $db;

    public function __construct() {
        $database = new \Database();
        $this->db = $database->connect();
    }

    public function createBooking($data) {
        $stmt = $this->db->prepare("
            INSERT INTO bookings (service_id, customer_name, customer_email, customer_phone, 
                                booking_date, booking_time, message) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['service_id'],
            $data['customer_name'],
            $data['customer_email'],
            $data['customer_phone'],
            $data['booking_date'],
            $data['booking_time'],
            $data['message'] ?? ''
        ]);
    }

    public function getAllBookings($limit = null, $offset = 0) {
        $sql = "
            SELECT b.*, s.name as service_name, s.price as service_price 
            FROM bookings b 
            JOIN services s ON b.service_id = s.id 
            ORDER BY b.created_at DESC
        ";
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBookingById($id) {
        $stmt = $this->db->prepare("
            SELECT b.*, s.name as service_name, s.price as service_price 
            FROM bookings b 
            JOIN services s ON b.service_id = s.id 
            WHERE b.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateBookingStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function deleteBooking($id) {
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getBookingStats() {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_bookings,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_bookings,
                COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed_bookings,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_bookings,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_bookings
            FROM bookings
        ");
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getAvailableSlots($date) {
        $bookedSlots = $this->db->prepare("SELECT booking_time FROM bookings WHERE booking_date = ?");
        $bookedSlots->execute([$date]);
        $booked = $bookedSlots->fetchAll(PDO::FETCH_COLUMN);
        
        $timeSlots = [
            '09:00', '10:00', '11:00', '12:00', 
            '14:00', '15:00', '16:00', '17:00'
        ];
        
        return array_diff($timeSlots, $booked);
    }
}