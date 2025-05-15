<?php
class Database {
    private $host = "localhost";
    private $db_name = "patient_records";
    private $username = "root";
    private $password = "Rupp155";
    public $conn;
    
    public function getConnection() {
        $this->conn = null;

        try {
            // Check if database exists, if not create it
            $pdo = new PDO(
                "mysql:host=" . $this->host, 
                $this->username, 
                $this->password,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
            
            // Try to create the database if it doesn't exist
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . $this->db_name . "` 
                        CHARACTER SET utf8 COLLATE utf8_general_ci");
            
            // Connect to the specific database
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                $this->username, 
                $this->password,
                array(PDO::ATTR_PERSISTENT => true)
            );
            
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create patients table if not exists
            $this->createPatientsTableIfNeeded();
            
            error_log("Database connection successful");
            return $this->conn;
        } catch(PDOException $exception) {
            error_log("Database Connection Error: " . $exception->getMessage());
            return null;
        }
    }
      // Function to test the database connection
    public function testConnection() {
        try {
            $this->getConnection();
            return $this->conn !== null;
        } catch(Exception $e) {
            return false;
        }
    }
    
    // Create patients table if needed
    private function createPatientsTableIfNeeded() {
        try {
            $query = "CREATE TABLE IF NOT EXISTS patients (
                id INT AUTO_INCREMENT PRIMARY KEY,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                phone VARCHAR(20),
                address TEXT,
                date_of_birth DATE,
                gender VARCHAR(10),
                blood_type VARCHAR(5),
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            
            $this->conn->exec($query);
            return true;
        } catch(PDOException $exception) {
            error_log("Error creating patients table: " . $exception->getMessage());
            return false;
        }
    }
}

?>