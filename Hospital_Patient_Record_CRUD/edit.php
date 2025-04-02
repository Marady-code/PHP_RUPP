<?php
    include ('connectDB.php');

    if(!isset($_GET['id'])){
        header("Location: index.php"); 
        exit();
    }
    $id = intval($_GET["id"]);

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $full_name = $conn->real_escape_string($_POST["full_name"]);
        $date_of_birth = $conn->real_escape_string($_POST["date_of_birth"]);
        $contact_number = $conn->real_escape_string($_POST["contact_number"]);
        $email = $conn->real_escape_string($_POST["email"]);
        $address = $conn->real_escape_string($_POST["address"]);
        $blood_type = $conn->real_escape_string($_POST["blood_type"]);
        $admission_date = $conn->real_escape_string($_POST["admission_date"]);
        $discharge_date = !empty($_POST["discharge_date"]) ? $conn->real_escape_string($_POST["discharge_date"]) : NULL;

        $sql = "UPDATE patients SET 
                full_name='$full_name', 
                date_of_birth='$date_of_birth', 
                contact_number='$contact_number', 
                email='$email', 
                address='$address', 
                blood_type='$blood_type', 
                admission_date='$admission_date', 
                discharge_date=" . ($discharge_date ? "'$discharge_date'" : "NULL") . "
                WHERE patient_id=$id";
                
        if($conn->query($sql) === TRUE){
            header("Location: index.php"); 
            exit();
        }else{
            $error = "Error: " . $conn->error;
        }
    }

    $sql = "SELECT * FROM patients WHERE patient_id = $id";
    $result = $conn->query($sql);
    $patient = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="edit.css">
</head>
<body>
    <div class="container">
        <h2 class="form-title"><i class="fas fa-user-edit"></i> Edit Patient</h2>
        
        <?php if(isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <div class="patient-status">
            <span class="patient-id-badge">
                <i class="fas fa-id-card"></i> Patient ID: #<?= htmlspecialchars($patient['patient_id']) ?>
            </span>
            <span class="admission-status">
                <?= $patient['discharge_date'] ? 'Discharged' : 'Currently Admitted' ?>
            </span>
        </div>
        
        <form method="post" action="">
            <div class="form-group">
                <label class="form-label required" for="full_name">Full Name</label>
                <input 
                    type="text" 
                    id="full_name"
                    name="full_name" 
                    class="form-input" 
                    required
                    value="<?= htmlspecialchars($patient['full_name']) ?>" 
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
                        value="<?= htmlspecialchars($patient['date_of_birth']) ?>" 
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
                        value="<?= htmlspecialchars($patient['contact_number']) ?>" 
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
                    value="<?= htmlspecialchars($patient['email']) ?>" 
                />
            </div>
            
            <div class="form-group">
                <label class="form-label" for="address">Full Address</label>
                <textarea 
                    id="address"
                    name="address" 
                    class="form-input" 
                    placeholder="Street, Barangay, City, Province"
                    rows="3"
                ><?= htmlspecialchars($patient['address']) ?></textarea>
            </div>
            
            <div class="input-group">
                <div class="form-group">
                    <label class="form-label" for="blood_type">Blood Type</label>
                    <select id="blood_type" name="blood_type" class="form-input">
                        <option value="">-- Select Blood Type --</option>
                        <option value="A+" <?= ($patient['blood_type'] == 'A+') ? 'selected' : '' ?>>A+</option>
                        <option value="A-" <?= ($patient['blood_type'] == 'A-') ? 'selected' : '' ?>>A-</option>
                        <option value="B+" <?= ($patient['blood_type'] == 'B+') ? 'selected' : '' ?>>B+</option>
                        <option value="B-" <?= ($patient['blood_type'] == 'B-') ? 'selected' : '' ?>>B-</option>
                        <option value="AB+" <?= ($patient['blood_type'] == 'AB+') ? 'selected' : '' ?>>AB+</option>
                        <option value="AB-" <?= ($patient['blood_type'] == 'AB-') ? 'selected' : '' ?>>AB-</option>
                        <option value="O+" <?= ($patient['blood_type'] == 'O+') ? 'selected' : '' ?>>O+</option>
                        <option value="O-" <?= ($patient['blood_type'] == 'O-') ? 'selected' : '' ?>>O-</option>
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
                        value="<?= htmlspecialchars($patient['admission_date']) ?>" 
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
                    value="<?= htmlspecialchars($patient['discharge_date'] ? $patient['discharge_date'] : '') ?>" 
                />
            </div>
            
            <button type="submit" class="submit-btn">
                <i class="fas fa-save"></i> Update Patient Record
            </button>
        </form>
        
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Patient List
        </a>
    </div>
</body>
</html>