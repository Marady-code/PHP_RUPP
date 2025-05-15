<?php
class Patient {
    private $conn;
    private $table_name = "patients";

    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $address;
    public $date_of_birth;
    public $gender;
    public $blood_type;
    public $is_active;

    public function __construct($db) {
        $this->conn = $db;
    }    // Create patient
    public function create() {
        try {
            $query = "INSERT INTO " . $this->table_name . "
                    SET first_name=:first_name, last_name=:last_name, email=:email, 
                    phone=:phone, address=:address, date_of_birth=:date_of_birth, 
                    gender=:gender, blood_type=:blood_type, is_active=1";
    
            $stmt = $this->conn->prepare($query);
    
            // Sanitize input
            $this->first_name = htmlspecialchars(strip_tags($this->first_name));
            $this->last_name = htmlspecialchars(strip_tags($this->last_name));
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->phone = htmlspecialchars(strip_tags($this->phone ?? ""));
            $this->address = htmlspecialchars(strip_tags($this->address ?? ""));
            $this->date_of_birth = htmlspecialchars(strip_tags($this->date_of_birth ?? ""));
            $this->gender = htmlspecialchars(strip_tags($this->gender ?? ""));
            $this->blood_type = htmlspecialchars(strip_tags($this->blood_type ?? ""));
    
            // Bind parameters
            $stmt->bindParam(":first_name", $this->first_name);
            $stmt->bindParam(":last_name", $this->last_name);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":phone", $this->phone);
            $stmt->bindParam(":address", $this->address);
            $stmt->bindParam(":date_of_birth", $this->date_of_birth);
            $stmt->bindParam(":gender", $this->gender);
            $stmt->bindParam(":blood_type", $this->blood_type);
    
            if($stmt->execute()) {
                return true;
            }
            
            error_log("Database error: " . print_r($stmt->errorInfo(), true));
            return false;
            
        } catch (PDOException $e) {
            error_log("PDO Exception: " . $e->getMessage());
            return false;
        }
        return false;
    }

    // Read all patients
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE is_active = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single patient
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->address = $row['address'];
            $this->date_of_birth = $row['date_of_birth'];
            $this->gender = $row['gender'];
            $this->blood_type = $row['blood_type'];
        }
    }

    // Update patient
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET first_name=:first_name, last_name=:last_name, email=:email, 
                phone=:phone, address=:address, date_of_birth=:date_of_birth, 
                gender=:gender, blood_type=:blood_type
                WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->date_of_birth = htmlspecialchars(strip_tags($this->date_of_birth));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->blood_type = htmlspecialchars(strip_tags($this->blood_type));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind parameters
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":date_of_birth", $this->date_of_birth);
        $stmt->bindParam(":gender", $this->gender);
        $stmt->bindParam(":blood_type", $this->blood_type);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Soft delete patient
    public function delete() {
        $query = "UPDATE " . $this->table_name . " SET is_active = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>