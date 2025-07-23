<?php

// Database Configuration
class Database {
    private $host = 'localhost';        // Change this to your host
    private $dbname = 'nananom_farms';  // Change this to your database name
    private $username = 'root';         // Change this to your username
    private $password = '';             // Change this to your password
    private $connection;

    public function connect() {
        if ($this->connection === null) {
            try {
                // Create MySQLi connection
                $this->connection = new mysqli($this->host, $this->username, $this->password, $this->dbname);
                
                if ($this->connection->connect_error) {
                    throw new Exception("Database connection failed: " . $this->connection->connect_error);
                }
                
                // Set charset
                $this->connection->set_charset("utf8mb4");
                
            } catch (Exception $e) {
                throw new Exception("Database connection failed: " . $e->getMessage());
            }
        }
        return $this->connection;
    }

    public function disconnect() {
        if ($this->connection) {
            $this->connection->close();
            $this->connection = null;
        }
    }
}

// Helper function to get database connection
function getDatabase() {
    $database = new Database();
    return $database->connect();
}
?>