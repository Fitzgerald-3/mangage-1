<?php

namespace App;

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../config/database.php';

class EnquiryManager {
    private $db;

    public function __construct() {
        $database = new \Database();
        $this->db = $database->connect();
    }

    // General Enquiries
    public function createEnquiry($data) {
        $stmt = $this->db->prepare("
            INSERT INTO enquiries (name, email, phone, subject, message) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['phone'] ?? null,
            $data['subject'],
            $data['message']
        ]);
    }

    public function getAllEnquiries($limit = null, $offset = 0) {
        $sql = "
            SELECT e.*, u.username as assigned_user 
            FROM enquiries e 
            LEFT JOIN users u ON e.assigned_to = u.id 
            ORDER BY e.created_at DESC
        ";
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getEnquiryById($id) {
        $stmt = $this->db->prepare("
            SELECT e.*, u.username as assigned_user 
            FROM enquiries e 
            LEFT JOIN users u ON e.assigned_to = u.id 
            WHERE e.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateEnquiryStatus($id, $status, $assignedTo = null) {
        if ($assignedTo) {
            $stmt = $this->db->prepare("UPDATE enquiries SET status = ?, assigned_to = ? WHERE id = ?");
            return $stmt->execute([$status, $assignedTo, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE enquiries SET status = ? WHERE id = ?");
            return $stmt->execute([$status, $id]);
        }
    }

    // Contact Messages
    public function createContactMessage($data) {
        $stmt = $this->db->prepare("
            INSERT INTO contact_messages (name, email, phone, subject, message) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['phone'] ?? null,
            $data['subject'],
            $data['message']
        ]);
    }

    public function getAllContactMessages($limit = null, $offset = 0) {
        $sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getContactMessageById($id) {
        $stmt = $this->db->prepare("SELECT * FROM contact_messages WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateContactMessageStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function getEnquiryStats() {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_enquiries,
                COUNT(CASE WHEN status = 'new' THEN 1 END) as new_enquiries,
                COUNT(CASE WHEN status = 'in_progress' THEN 1 END) as in_progress_enquiries,
                COUNT(CASE WHEN status = 'resolved' THEN 1 END) as resolved_enquiries
            FROM enquiries
        ");
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getContactStats() {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_messages,
                COUNT(CASE WHEN status = 'new' THEN 1 END) as new_messages,
                COUNT(CASE WHEN status = 'read' THEN 1 END) as read_messages,
                COUNT(CASE WHEN status = 'responded' THEN 1 END) as responded_messages
            FROM contact_messages
        ");
        $stmt->execute();
        return $stmt->fetch();
    }
}