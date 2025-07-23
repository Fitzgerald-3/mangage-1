<?php

namespace App;

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../config/storage.php';

class EnquiryManager {
    private $storage;
    private $enquiryTable = 'enquiries';
    private $contactTable = 'contact_messages';

    public function __construct() {
        $this->storage = new \FileStorage();
    }

    // General Enquiries
    public function createEnquiry($data) {
        $enquiryData = [
            'name' => $this->storage->escape_string($data['name']),
            'email' => $this->storage->escape_string($data['email']),
            'phone' => $this->storage->escape_string($data['phone'] ?? ''),
            'subject' => $this->storage->escape_string($data['subject']),
            'message' => $this->storage->escape_string($data['message']),
            'status' => 'new',
            'assigned_to' => null
        ];
        
        return $this->storage->insert($this->enquiryTable, $enquiryData);
    }

    public function getAllEnquiries($limit = null, $offset = 0) {
        $enquiries = $this->storage->select(
            $this->enquiryTable,
            [],
            ['field' => 'created_at', 'direction' => 'desc'],
            $limit,
            $offset
        );
        
        // Add assigned user information (simulated since we don't have users table)
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
        $enquiry = $this->storage->selectOne($this->enquiryTable, ['id' => $id]);
        
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
        
        $updateData = ['status' => $status];
        if ($assignedTo !== null) {
            $updateData['assigned_to'] = $assignedTo;
        }
        
        return $this->storage->update($this->enquiryTable, ['id' => $id], $updateData);
    }

    // Contact Messages
    public function createContactMessage($data) {
        $contactData = [
            'name' => $this->storage->escape_string($data['name']),
            'email' => $this->storage->escape_string($data['email']),
            'phone' => $this->storage->escape_string($data['phone'] ?? ''),
            'subject' => $this->storage->escape_string($data['subject']),
            'message' => $this->storage->escape_string($data['message']),
            'status' => 'new'
        ];
        
        return $this->storage->insert($this->contactTable, $contactData);
    }

    public function getAllContactMessages($limit = null, $offset = 0) {
        return $this->storage->select(
            $this->contactTable,
            [],
            ['field' => 'created_at', 'direction' => 'desc'],
            $limit,
            $offset
        );
    }

    public function getContactMessageById($id) {
        return $this->storage->selectOne($this->contactTable, ['id' => $id]);
    }

    public function updateContactMessageStatus($id, $status) {
        $validStatuses = ['new', 'read', 'responded', 'archived'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        return $this->storage->update($this->contactTable, ['id' => $id], ['status' => $status]);
    }

    public function getEnquiryStats() {
        $enquiries = $this->storage->query($this->enquiryTable);
        
        $stats = [
            'total_enquiries' => count($enquiries),
            'new_enquiries' => 0,
            'in_progress_enquiries' => 0,
            'resolved_enquiries' => 0
        ];
        
        foreach ($enquiries as $enquiry) {
            switch ($enquiry['status']) {
                case 'new':
                    $stats['new_enquiries']++;
                    break;
                case 'in_progress':
                    $stats['in_progress_enquiries']++;
                    break;
                case 'resolved':
                    $stats['resolved_enquiries']++;
                    break;
            }
        }
        
        return $stats;
    }

    public function getContactStats() {
        $contacts = $this->storage->query($this->contactTable);
        
        $stats = [
            'total_messages' => count($contacts),
            'new_messages' => 0,
            'read_messages' => 0,
            'responded_messages' => 0
        ];
        
        foreach ($contacts as $contact) {
            switch ($contact['status']) {
                case 'new':
                    $stats['new_messages']++;
                    break;
                case 'read':
                    $stats['read_messages']++;
                    break;
                case 'responded':
                    $stats['responded_messages']++;
                    break;
            }
        }
        
        return $stats;
    }

    public function deleteEnquiry($id) {
        return $this->storage->delete($this->enquiryTable, ['id' => $id]);
    }

    public function deleteContactMessage($id) {
        return $this->storage->delete($this->contactTable, ['id' => $id]);
    }

    public function searchEnquiries($searchTerm) {
        $enquiries = $this->storage->query($this->enquiryTable);
        $searchTerm = strtolower($searchTerm);
        
        return array_filter($enquiries, function($enquiry) use ($searchTerm) {
            return strpos(strtolower($enquiry['name']), $searchTerm) !== false ||
                   strpos(strtolower($enquiry['email']), $searchTerm) !== false ||
                   strpos(strtolower($enquiry['subject']), $searchTerm) !== false ||
                   strpos(strtolower($enquiry['message']), $searchTerm) !== false;
        });
    }

    public function searchContactMessages($searchTerm) {
        $contacts = $this->storage->query($this->contactTable);
        $searchTerm = strtolower($searchTerm);
        
        return array_filter($contacts, function($contact) use ($searchTerm) {
            return strpos(strtolower($contact['name']), $searchTerm) !== false ||
                   strpos(strtolower($contact['email']), $searchTerm) !== false ||
                   strpos(strtolower($contact['subject']), $searchTerm) !== false ||
                   strpos(strtolower($contact['message']), $searchTerm) !== false;
        });
    }
}