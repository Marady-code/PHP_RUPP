<?php
require_once '../../config/db_connect.php';

// Get all active teachers
$teachersStmt = $conn->query("SELECT teacher_id, full_name FROM teachers WHERE isActive = 1 ORDER BY full_name");
$teachers = $teachersStmt->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_id = $_POST['teacher_id'];
    $date_taught = $_POST['date_taught'];
    $hours_taught = $_POST['hours_taught'];
    $notes = $_POST['notes'];

    // Validate input
    $errors = [];
    if(empty($teacher_id)) $errors[] = "Teacher is required";
    if(empty($date_taught)) $errors[] = "Date is required";
    if(empty($hours_taught) || $hours_taught <= 0) $errors[] = "Valid hours required";

    if(empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO teaching_hours (teacher_id, date_taught, hours_taught, notes) VALUES (?, ?, ?, ?)");
            $stmt->execute([$teacher_id, $date_taught, $hours_taught, $notes]);
            
            $_SESSION['message'] = "Teaching hours added successfully!";
            header("Location: index.php");
            exit();
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = implode("<br>", $errors);
    }
}

// Pre-select teacher if coming from teacher view
$selectedTeacher = isset($_GET['teacher_id']) ? intval($_GET['teacher_id']) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Teaching Hours</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4"><i class="fas fa-plus-circle"></i> Add Teaching Hours</h1>
        
        <div class="card">
            <div class="card-body">
                <?php flashMessage(); ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="teacher_id" class="form-label">Teacher <span class="text-danger">*</span></label>
                        <select class="form-select" id="teacher_id" name="teacher_id" required>
                            <option value="">Select Teacher</option>
                            <?php foreach($teachers as $teacher): ?>
                                <option value="<?= $teacher['teacher_id'] ?>" 
                                    <?= (isset($_POST['teacher_id']) && $_POST['teacher_id'] == $teacher['teacher_id']) || 
                                        ($selectedTeacher && $selectedTeacher == $teacher['teacher_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($teacher['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="date_taught" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="date_taught" name="date_taught" required
                               value="<?= isset($_POST['date_taught']) ? htmlspecialchars($_POST['date_taught']) : date('Y-m-d') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="hours_taught" class="form-label">Hours Taught <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="hours_taught" name="hours_taught" required
                               value="<?= isset($_POST['hours_taught']) ? htmlspecialchars($_POST['hours_taught']) : '' ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"><?= isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : '' ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Hours</button>
                    <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>