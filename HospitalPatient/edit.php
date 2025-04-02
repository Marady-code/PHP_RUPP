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
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #e6f0ff;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #fd7e14;
            --dark: #212529;
            --light: #f8f9fa;
            --gray: #6c757d;
            --border: #dee2e6;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --radius: 8px;
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fb;
            color: var(--dark);
            line-height: 1.6;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .container {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 30px;
            width: 100%;
            max-width: 700px;
            animation: fadeIn 0.5s ease-out;
        }
        
        .form-title {
            text-align: center;
            color: var(--primary);
            margin-bottom: 30px;
            font-weight: 600;
            position: relative;
            padding-bottom: 15px;
            font-size: 24px;
        }
        
        .form-title::after {
            content: "";
            position: absolute;
            width: 60px;
            height: 4px;
            background: var(--primary);
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark);
            font-weight: 500;
            font-size: 14px;
        }
        
        .form-label.required::after {
            content: " *";
            color: var(--danger);
        }
        
        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 15px;
            transition: var(--transition);
            background-color: var(--light);
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }
        
        textarea.form-input {
            min-height: 100px;
            resize: vertical;
        }
        
        select.form-input {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236c757d' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
        }
        
        .input-group {
            display: flex;
            gap: 15px;
        }
        
        .input-group .form-group {
            flex: 1;
        }
        
        .submit-btn {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .submit-btn:hover {
            background: #3a56e8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--gray);
            text-decoration: none;
            transition: var(--transition);
            font-size: 14px;
        }
        
        .back-link:hover {
            color: var(--primary);
        }
        
        .error-message {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger);
            padding: 12px 15px;
            border-radius: var(--radius);
            margin-bottom: 25px;
            text-align: center;
            border: 1px solid rgba(220, 53, 69, 0.2);
            font-size: 14px;
            animation: fadeIn 0.3s ease-out;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .patient-status {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: var(--light);
            border-radius: var(--radius);
            margin-bottom: 20px;
        }
        
        .patient-id-badge {
            background-color: var(--primary-light);
            color: var(--primary);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .admission-status {
            font-size: 13px;
            color: var(--gray);
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .input-group {
                flex-direction: column;
                gap: 20px;
            }
        }
    </style>
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