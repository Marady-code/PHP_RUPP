<?php
require_once('../config/db_connect.php');

if(!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$teacher_id = $_GET['id'];

try {
    // Fetch teacher data
    $stmt = $conn->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
    $stmt->execute([$teacher_id]);
    $teacher = $stmt->fetch();

    if(!$teacher) {
        $_SESSION['error'] = "Teacher not found";
        header("Location: index.php");
        exit();
    }
} catch(PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $hourly_rate = $_POST['hourly_rate'];

    try {
        $stmt = $conn->prepare("UPDATE teachers SET full_name = ?, email = ?, phone = ?, hourly_rate = ? WHERE teacher_id = ?");
        $stmt->execute([$full_name, $email, $phone, $hourly_rate, $teacher_id]);
        
        $_SESSION['message'] = "Teacher updated successfully!";
        header("Location: index.php");
        exit();
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Teacher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4"><i class="fas fa-edit"></i> Edit Teacher</h1>
        
        <div class="card">
            <div class="card-body">
                <?php flashMessage(); ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="full_name" class="form-label required">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required
                               value="<?= htmlspecialchars($teacher['full_name']) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= htmlspecialchars($teacher['email']) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone"
                               value="<?= htmlspecialchars($teacher['phone']) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="hourly_rate" class="form-label required">Hourly Rate</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control" id="hourly_rate" name="hourly_rate" required
                                   value="<?= htmlspecialchars($teacher['hourly_rate']) ?>">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Teacher</button>
                    <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>