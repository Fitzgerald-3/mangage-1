<?php

class FileStorage {
    private $dataPath;
    private $lastInsertId = null;
    private $affectedRows = 0;
    
    public function __construct($dataPath = 'data/') {
        $this->dataPath = __DIR__ . '/../../' . $dataPath;
        $this->ensureDataDirectory();
    }
    
    private function ensureDataDirectory() {
        if (!file_exists($this->dataPath)) {
            mkdir($this->dataPath, 0755, true);
        }
    }
    
    public function query($filename) {
        $filepath = $this->dataPath . $filename . '.json';
        if (!file_exists($filepath)) {
            return [];
        }
        
        $content = file_get_contents($filepath);
        return $content ? json_decode($content, true) : [];
    }
    
    public function insert($filename, $data) {
        $records = $this->query($filename);
        $data['id'] = $this->generateId($filename);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $records[] = $data;
        $success = $this->writeData($filename, $records);
        
        if ($success) {
            $this->lastInsertId = $data['id'];
            $this->affectedRows = 1;
        }
        
        return $success;
    }
    
    public function update($filename, $conditions, $data) {
        $records = $this->query($filename);
        $updated = 0;
        
        foreach ($records as &$record) {
            $match = true;
            foreach ($conditions as $field => $value) {
                if (!isset($record[$field]) || $record[$field] != $value) {
                    $match = false;
                    break;
                }
            }
            
            if ($match) {
                foreach ($data as $field => $value) {
                    $record[$field] = $value;
                }
                $record['updated_at'] = date('Y-m-d H:i:s');
                $updated++;
            }
        }
        
        $this->affectedRows = $updated;
        return $this->writeData($filename, $records);
    }
    
    public function delete($filename, $conditions) {
        $records = $this->query($filename);
        $originalCount = count($records);
        
        $records = array_filter($records, function($record) use ($conditions) {
            foreach ($conditions as $field => $value) {
                if (isset($record[$field]) && $record[$field] == $value) {
                    return false; // Remove this record
                }
            }
            return true; // Keep this record
        });
        
        $records = array_values($records); // Re-index
        $this->affectedRows = $originalCount - count($records);
        
        return $this->writeData($filename, $records);
    }
    
    public function select($filename, $conditions = [], $orderBy = null, $limit = null, $offset = 0) {
        $records = $this->query($filename);
        
        // Apply conditions (WHERE clause)
        if (!empty($conditions)) {
            $records = array_filter($records, function($record) use ($conditions) {
                foreach ($conditions as $field => $value) {
                    if (!isset($record[$field]) || $record[$field] != $value) {
                        return false;
                    }
                }
                return true;
            });
        }
        
        // Apply sorting (ORDER BY clause)
        if ($orderBy) {
            if (is_array($orderBy)) {
                $field = $orderBy['field'];
                $direction = strtolower($orderBy['direction']) === 'desc' ? -1 : 1;
            } else {
                $field = $orderBy;
                $direction = 1;
            }
            
            usort($records, function($a, $b) use ($field, $direction) {
                if (!isset($a[$field]) || !isset($b[$field])) return 0;
                return $direction * strcmp($a[$field], $b[$field]);
            });
        }
        
        // Apply pagination (LIMIT and OFFSET)
        if ($limit || $offset) {
            $records = array_slice($records, $offset, $limit);
        }
        
        return $records;
    }
    
    public function selectOne($filename, $conditions = []) {
        $results = $this->select($filename, $conditions, null, 1);
        return !empty($results) ? $results[0] : null;
    }
    
    private function writeData($filename, $data) {
        $filepath = $this->dataPath . $filename . '.json';
        return file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
    }
    
    private function generateId($filename) {
        $data = $this->query($filename);
        if (empty($data)) {
            return 1;
        }
        
        $maxId = 0;
        foreach ($data as $item) {
            if (isset($item['id']) && $item['id'] > $maxId) {
                $maxId = $item['id'];
            }
        }
        
        return $maxId + 1;
    }
    
    // mysqli-style properties and methods
    public function insert_id() {
        return $this->lastInsertId;
    }
    
    public function affected_rows() {
        return $this->affectedRows;
    }
    
    public function num_rows($filename, $conditions = []) {
        return count($this->select($filename, $conditions));
    }
    
    public function escape_string($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    public function backup($filename) {
        $data = $this->query($filename);
        $backupPath = $this->dataPath . 'backups/';
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }
        
        $backupFile = $backupPath . $filename . '_' . date('Y-m-d_H-i-s') . '.json';
        return file_put_contents($backupFile, json_encode($data, JSON_PRETTY_PRINT)) !== false;
    }
    
    public function getDataPath() {
        return $this->dataPath;
    }
    
    // Join functionality for related data
    public function join($primaryTable, $joinTable, $primaryKey, $foreignKey, $conditions = []) {
        $primaryData = $this->select($primaryTable, $conditions);
        $joinData = $this->query($joinTable);
        
        // Create lookup array for join data
        $joinLookup = [];
        foreach ($joinData as $item) {
            $joinLookup[$item[$foreignKey]] = $item;
        }
        
        // Merge data
        foreach ($primaryData as &$item) {
            if (isset($joinLookup[$item[$primaryKey]])) {
                foreach ($joinLookup[$item[$primaryKey]] as $key => $value) {
                    $item[$joinTable . '_' . $key] = $value;
                }
            }
        }
        
        return $primaryData;
    }
}