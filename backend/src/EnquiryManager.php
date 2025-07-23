<?php

namespace App;

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../config/database.php';

class EnquiryManager {
    private $db;
    private $enquiryTable = 'enquiries';
    private $contactTable = 'contact_messages';

    public function __construct() {
        $database = new \Database();
        $this->db = $database->connect();
    }

    // General Enquiries
    public function createEnquiry($data) {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->enquiryTable} (name, email, phone, subject, message, status) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->bind_param("ssssss",
            $data['name'],
            $data['email'],
            $data['phone'] ?? '',
            $data['subject'],
            $data['message'],
            'new'
        ) && $stmt->execute();
    }

    public function getAllEnquiries($limit = null, $offset = 0) {
        $sql = "SELECT * FROM {$this->enquiryTable} ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $result = $this->db->query($sql);
        $enquiries = $result->fetch_all(MYSQLI_ASSOC);
        
        // Add assigned user information (simulated)
        foreach ($enquiries as &$enquiry) {
            if ($enquiry['assigned_to']) {
                $enquiry['assigned_user'] = 'Admin User ' . $enquiry['assigned_to'];
            } else {
                $enquiry['assigned_user'] = null;
            }
        }
        
        return $enquiries;
    }

    public function getEnquiryById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->enquiryTable} WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $enquiry = $result->fetch_assoc();
        
        if ($enquiry && $enquiry['assigned_to']) {
            $enquiry['assigned_user'] = 'Admin User ' . $enquiry['assigned_to'];
        }
        
        return $enquiry;
    }

    public function updateEnquiryStatus($id, $status, $assignedTo = null) {
        $validStatuses = ['new', 'in_progress', 'resolved', 'closed'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        if ($assignedTo !== null) {
            $stmt = $this->db->prepare("UPDATE {$this->enquiryTable} SET status = ?, assigned_to = ? WHERE id = ?");
            return $stmt->bind_param("sii", $status, $assignedTo, $id) && $stmt->execute();
        } else {
            $stmt = $this->db->prepare("UPDATE {$this->enquiryTable} SET status = ? WHERE id = ?");
            return $stmt->bind_param("si", $status, $id) && $stmt->execute();
        }
    }

    // Contact Messages
    public function createContactMessage($data) {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->contactTable} (name, email, phone, subject, message, status) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->bind_param("ssssss",
            $data['name'],
            $data['email'],
            $data['phone'] ?? '',
            $data['subject'],
            $data['message'],
            'new'
        ) && $stmt->execute();
    }

    public function getAllContactMessages($limit = null, $offset = 0) {
        $sql = "SELECT * FROM {$this->contactTable} ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getContactMessageById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->contactTable} WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateContactMessageStatus($id, $status) {
        $validStatuses = ['new', 'read', 'responded', 'archived'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $stmt = $this->db->prepare("UPDATE {$this->contactTable} SET status = ? WHERE id = ?");
        return $stmt->bind_param("si", $status, $id) && $stmt->execute();
    }

    public function getEnquiryStats() {
        $result = $this->db->query("
            SELECT 
                COUNT(*) as total_enquiries,
                COUNT(CASE WHEN status = 'new' THEN 1 END) as new_enquiries,
                COUNT(CASE WHEN status = 'in_progress' THEN 1 END) as in_progress_enquiries,
                COUNT(CASE WHEN status = 'resolved' THEN 1 END) as resolved_enquiries
            FROM {$this->enquiryTable}
        ");
        return $result->fetch_assoc();
    }

    public function getContactStats() {
        $result = $this->db->query("
            SELECT 
                COUNT(*) as total_messages,
                COUNT(CASE WHEN status = 'new' THEN 1 END) as new_messages,
                COUNT(CASE WHEN status = 'read' THEN 1 END) as read_messages,
                COUNT(CASE WHEN status = 'responded' THEN 1 END) as responded_messages
            FROM {$this->contactTable}
        ");
        return $result->fetch_assoc();
    }

    public function deleteEnquiry($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->enquiryTable} WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function deleteContactMessage($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->contactTable} WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function searchEnquiries($searchTerm) {
        $searchPattern = "%$searchTerm%";
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->enquiryTable} 
            WHERE name LIKE ? 
               OR email LIKE ? 
               OR subject LIKE ? 
               OR message LIKE ?
        ");
        $stmt->bind_param("ssss", $searchPattern, $searchPattern, $searchPattern, $searchPattern);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function searchContactMessages($searchTerm) {
        $searchPattern = "%$searchTerm%";
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->contactTable} 
            WHERE name LIKE ? 
               OR email LIKE ? 
               OR subject LIKE ? 
               OR message LIKE ?
        ");
        $stmt->bind_param("ssss", $searchPattern, $searchPattern, $searchPattern, $searchPattern);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}