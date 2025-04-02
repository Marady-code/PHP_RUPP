<?php
    include('connectDB.php');

    if($_SERVER["REQUEST_METHOD"] == "POST"){

        $full_name = trim($conn->real_escape_string($_POST["full_name"]));
        $date_of_birth = $conn->real_escape_string($_POST["date_of_birth"]);
        $contact_number = trim($conn->real_escape_string($_POST["contact_number"]));
        $email = trim($conn->real_escape_string($_POST["email"]));
        $address = trim($conn->real_escape_string($_POST["address"]));
        $blood_type = $conn->real_escape_string($_POST["blood_type"]);
        $admission_date = $conn->real_escape_string($_POST["admission_date"]);
        $discharge_date = !empty($_POST["discharge_date"]) ? $conn->real_escape_string($_POST["discharge_date"]) : NULL;

        // Validate required fields
        if(empty($full_name) || empty($date_of_birth) || empty($contact_number) || empty($admission_date)) {
            $error = "Please fill in all required fields.";
        } else {
            $sql = "INSERT INTO patients (full_name, date_of_birth, contact_number, email, address, blood_type, admission_date, discharge_date) 
                    VALUES ('$full_name', '$date_of_birth', '$contact_number', '$email', '$address', '$blood_type', '$admission_date', " . ($discharge_date ? "'$discharge_date'" : "NULL") . ")";
            
            if($conn->query($sql) === TRUE){
                header("Location: index.php");
                exit();
            }else{
                $error = "Error: " . $conn->error;
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add New Patient</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="create.css">

</head>
<body>
    <div class="container">
        <h2 class="form-title"><i class="fas fa-user-plus"></i> Add New Patient</h2>
        
        <?php if(isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label class="form-label required" for="full_name">Full Name</label>
                <input 
                    type="text" 
                    id="full_name"
                    name="full_name" 
                    class="form-input" 
                    required 
                    placeholder="Enter patient's full name"
                    value="<?= isset($full_name) ? htmlspecialchars($full_name) : '' ?>"
                />
            </div>
            
            <div class="input-group">
                <div class="form-group">
                    <label class="form-label required" for="date_of_birth">Date of Birth</label>
                    <input 
                        type="date" 
                        id="date_of_birth"
                        name="date_of_birth" 
                        class="form-input" 
                        required 
                        value="<?= isset($date_of_birth) ? htmlspecialchars($date_of_birth) : '' ?>"
                    />
                </div>
                
                <div class="form-group">
                    <label class="form-label required" for="contact_number">Contact Number</label>
                    <input 
                        type="tel" 
                        id="contact_number"
                        name="contact_number" 
                        class="form-input" 
                        required 
                        placeholder="e.g. 09123456789"
                        value="<?= isset($contact_number) ? htmlspecialchars($contact_number) : '' ?>"
                    />
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input 
                    type="email" 
                    id="email"
                    name="email" 
                    class="form-input" 
                    placeholder="patient@example.com"
                    value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"
                />
            </div>
            
            <div class="form-group">
                <label class="form-label" for="address">Full Address</label>
                <textarea 
                    id="address"
                    name="address" 
                    class="form-input" 
                    placeholder="Street, ​District, commune, City or Province"
                    rows="3"
                ><?= isset($address) ? htmlspecialchars($address) : '' ?></textarea>
            </div>
            
            <div class="input-group">
                <div class="form-group">
                    <label class="form-label" for="blood_type">Blood Type</label>
                    <select id="blood_type" name="blood_type" class="form-input">
                        <option value="">-- Select Blood Type --</option>
                        <option value="A+" <?= (isset($blood_type) && $blood_type == 'A+') ? 'selected' : '' ?>>A+</option>
                        <option value="A-" <?= (isset($blood_type) && $blood_type == 'A-') ? 'selected' : '' ?>>A-</option>
                        <option value="B+" <?= (isset($blood_type) && $blood_type == 'B+') ? 'selected' : '' ?>>B+</option>
                        <option value="B-" <?= (isset($blood_type) && $blood_type == 'B-') ? 'selected' : '' ?>>B-</option>
                        <option value="AB+" <?= (isset($blood_type) && $blood_type == 'AB+') ? 'selected' : '' ?>>AB+</option>
                        <option value="AB-" <?= (isset($blood_type) && $blood_type == 'AB-') ? 'selected' : '' ?>>AB-</option>
                        <option value="O+" <?= (isset($blood_type) && $blood_type == 'O+') ? 'selected' : '' ?>>O+</option>
                        <option value="O-" <?= (isset($blood_type) && $blood_type == 'O-') ? 'selected' : '' ?>>O-</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label required" for="admission_date">Admission Date</label>
                    <input 
                        type="date" 
                        id="admission_date"
                        name="admission_date" 
                        class="form-input" 
                        required 
                        value="<?= isset($admission_date) ? htmlspecialchars($admission_date) : date('Y-m-d') ?>"
                    />
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="discharge_date">Discharge Date</label>
                <input 
                    type="date" 
                    id="discharge_date"
                    name="discharge_date" 
                    class="form-input" 
                    value="<?= isset($discharge_date) ? htmlspecialchars($discharge_date) : '' ?>"
                />
            </div>
            
            <button type="submit" class="submit-btn">
                <i class="fas fa-save"></i> Save Patient Record
            </button>
        </form>
        
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Patient List
        </a>
    </div>
</body>
</html>