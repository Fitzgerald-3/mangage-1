<?php

class FileStorage {
    private $dataPath;
    
    public function __construct($dataPath = 'data/') {
        $this->dataPath = __DIR__ . '/../../' . $dataPath;
        $this->ensureDataDirectory();
    }
    
    private function ensureDataDirectory() {
        if (!file_exists($this->dataPath)) {
            mkdir($this->dataPath, 0755, true);
        }
    }
    
    public function read($filename) {
        $filepath = $this->dataPath . $filename . '.json';
        if (!file_exists($filepath)) {
            return [];
        }
        
        $content = file_get_contents($filepath);
        return $content ? json_decode($content, true) : [];
    }
    
    public function write($filename, $data) {
        $filepath = $this->dataPath . $filename . '.json';
        return file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT)) !== false;
    }
    
    public function generateId($filename) {
        $data = $this->read($filename);
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
    
    public function backup($filename) {
        $data = $this->read($filename);
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
}