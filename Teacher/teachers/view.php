<?php
require_once('../config/db_connect.php');

if(!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$teacher_id = $_GET['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
    $stmt->execute([$teacher_id]);
    $teacher = $stmt->fetch();
    
    if(!$teacher) {
        $_SESSION['error'] = "Teacher not found";
        header("Location: index.php");
        exit();
    }

    // Get teaching hours
    $hoursStmt = $conn->prepare("
        SELECT date_taught, hours_taught, notes 
        FROM teaching_hours 
        WHERE teacher_id = ? 
        ORDER BY date_taught DESC 
        LIMIT 5
    ");
    $hoursStmt->execute([$teacher_id]);
    $teachingHours = $hoursStmt->fetchAll();

} catch(PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Teacher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4"><i class="fas fa-user"></i> Teacher Details</h1>
        
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h3><?= htmlspecialchars($teacher['full_name']) ?></h3>
                        <p class="text-muted mb-1"><i class="fas fa-envelope"></i> <?= htmlspecialchars($teacher['email'] ?: 'N/A') ?></p>
                        <p class="text-muted mb-1"><i class="fas fa-phone"></i> <?= htmlspecialchars($teacher['phone'] ?: 'N/A') ?></p>
                        <p class="text-muted"><i class="fas fa-money-bill-wave"></i> $<?= number_format($teacher['hourly_rate'], 2) ?> per hour</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="edit.php?id=<?= $teacher['teacher_id'] ?>" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
                        <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Teaching Hours</h5>
            </div>
            <div class="card-body">
                <?php if(!empty($teachingHours)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Hours</th>
                                    <th>Earnings</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($teachingHours as $hour): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($hour['date_taught'])) ?></td>
                                        <td><?= number_format($hour['hours_taught'], 2) ?></td>
                                        <td>$<?= number_format($hour['hours_taught'] * $teacher['hourly_rate'], 2) ?></td>
                                        <td><?= htmlspecialchars($hour['notes'] ?: 'N/A') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="../hours/add.php?teacher_id=<?= $teacher['teacher_id'] ?>" class="btn btn-success mt-3">
                        <i class="fas fa-plus"></i> Add Teaching Hours
                    </a>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i> No teaching hours recorded yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>